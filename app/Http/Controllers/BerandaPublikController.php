<?php

namespace App\Http\Controllers;

use App\Models\PosDana;
use App\Models\Transaksi;
use App\Services\SaldoDana;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BerandaPublikController extends Controller
{
    private const WARNA_MASUK = [
        '#17443a',
        '#2f7561',
        '#5a9a86',
        '#8fbbaa',
        '#c7a76a',
        '#b0725a',
        '#7c8b84',
        '#9db8ac',
    ];

    private const WARNA_KELUAR = [
        '#8a3a12',
        '#b0725a',
        '#c98b4f',
        '#d9ab6a',
        '#a45c40',
        '#7c4b34',
        '#c7a76a',
        '#9db8ac',
    ];

    public function __construct(private readonly SaldoDana $saldoDana) {}

    public function index(Request $request): View
    {
        $filter = $this->filter($request);

        $posDana = $this->saldoDana->semua();
        $totalSaldo = (float) $posDana->sum(fn ($pos) => (float) $pos->getAttribute('saldo_terkini'));

        $terakhir = Transaksi::query()
            ->where('status', 'aktif')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
        $terakhirDiperbarui = $terakhir?->updated_at
            ? $terakhir->updated_at->copy()->locale('id')->translatedFormat('d F Y, H.i')
            : null;

        $pemasukanTerbaru = $this->transaksiTerbaru('masuk', $filter);
        $pengeluaranTerbaru = $this->transaksiTerbaru('keluar', $filter);

        $pemasukanPos = $this->transaksiPerPos($filter, 'masuk', self::WARNA_MASUK);
        $pengeluaranPos = $this->transaksiPerPos($filter, 'keluar', self::WARNA_KELUAR);

        return view('fo.home', [
            'posDana' => $posDana,
            'totalSaldo' => $totalSaldo,
            'terakhirDiperbarui' => $terakhirDiperbarui,
            'pemasukanTerbaru' => $pemasukanTerbaru,
            'pengeluaranTerbaru' => $pengeluaranTerbaru,
            'pemasukanPos' => $pemasukanPos,
            'pengeluaranPos' => $pengeluaranPos,
            'filter' => $filter,
        ]);
    }

    private function transaksiTerbaru(string $jenis, array $filter): array
    {
        return Transaksi::query()
            ->with(['posDana', 'kategori', 'acara', 'lampiran'])
            ->where('jenis', $jenis)
            ->where('status', 'aktif')
            ->whereBetween('tanggal', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['pos_dana_id'], fn ($query, int $posId) => $query->where('pos_dana_id', $posId))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->map(fn (Transaksi $t) => [
                'tanggal' => $t->tanggal ? $t->tanggal->copy()->locale('id')->translatedFormat('d M Y') : '-',
                'pos' => $t->posDana?->nama ?? '-',
                'keterangan' => $this->keteranganPublik($t),
                'nominal' => (float) $t->nominal,
                'lampiran' => $this->lampiranPublik($t),
            ])
            ->all();
    }

    private function keteranganPublik(Transaksi $t): string
    {
        $publik = trim((string) $t->catatan_publik);
        if ($publik !== '' && $publik !== '-') {
            return $publik;
        }

        $bagian = array_filter([
            $t->kategori?->nama,
            $t->acara?->nama,
        ]);

        return $bagian !== [] ? implode(' · ', $bagian) : ($t->jenis === 'masuk' ? 'Pemasukan' : 'Pengeluaran');
    }

    private function lampiranPublik(Transaksi $t): array
    {
        if ($t->sembunyikan_lampiran) {
            return [];
        }

        return $t->lampiran
            ->filter(fn ($l) => (bool) $l->tampil_publik)
            ->map(fn ($l) => asset('storage/'.$l->lokasi_berkas))
            ->values()
            ->all();
    }

    private function transaksiPerPos(array $filter, string $jenis, array $warna): array
    {
        $rows = Transaksi::query()
            ->leftJoin('pos_dana', 'pos_dana.id', '=', 'transaksi.pos_dana_id')
            ->where('transaksi.jenis', $jenis)
            ->where('transaksi.status', 'aktif')
            ->whereBetween('transaksi.tanggal', [$filter['tanggal_mulai'], $filter['tanggal_selesai']])
            ->when($filter['pos_dana_id'], fn ($query, int $posId) => $query->where('transaksi.pos_dana_id', $posId))
            ->groupBy('pos_dana.nama')
            ->orderByDesc('total')
            ->selectRaw('COALESCE(pos_dana.nama, ?) as nama, SUM(transaksi.nominal) as total', ['Tanpa pos'])
            ->get();

        $total = (float) $rows->sum('total');
        $items = $rows->values()->map(function ($row, int $index) use ($total, $warna): array {
            $nominal = (float) $row->total;

            return [
                'nama' => $row->nama,
                'total' => $nominal,
                'persen' => $total > 0 ? ($nominal / $total) * 100 : 0,
                'warna' => $warna[$index % count($warna)],
            ];
        })->all();

        return [
            'total' => $total,
            'items' => $items,
        ];
    }

    private function filter(Request $request): array
    {
        $mulai = $this->parseTanggal($request->input('tanggal_mulai')) ?? now()->subMonths(5)->startOfMonth();
        $selesai = $this->parseTanggal($request->input('tanggal_selesai')) ?? now()->endOfDay();

        if ($mulai->greaterThan($selesai)) {
            [$mulai, $selesai] = [$selesai, $mulai];
        }

        $posId = (int) $request->input('pos_dana_id');
        if ($posId <= 0 || ! PosDana::query()->whereKey($posId)->exists()) {
            $posId = null;
        }

        return [
            'pos_dana_id' => $posId,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'label' => $mulai->copy()->locale('id')->translatedFormat('d M Y')
                .' - '.$selesai->copy()->locale('id')->translatedFormat('d M Y'),
        ];
    }

    private function parseTanggal(mixed $nilai): ?Carbon
    {
        if (! is_string($nilai) || $nilai === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $nilai)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
