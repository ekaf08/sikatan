<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use App\Models\Kategori;
use App\Models\PosDana;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Transaksi;
use App\Services\SaldoDana;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly SaldoDana $saldoDana) {}

    public function index(Request $request): View
    {
        $sekarang = now();
        $mulaiBulan = $sekarang->copy()->startOfMonth()->toDateString();
        $akhirBulan = $sekarang->copy()->endOfMonth()->toDateString();
        $mulaiMinggu = $sekarang->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $akhirMinggu = $sekarang->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $pos = PosDana::query()->orderBy('nama')->get();

        $saldoPos = $pos
            ->mapWithKeys(fn (PosDana $item): array => [$item->id => $this->saldoDana->hitung($item)])
            ->all();

        $pemasukanBulanIni = (float) Transaksi::query()
            ->where('jenis', 'masuk')
            ->where('status', 'aktif')
            ->whereBetween('tanggal', [$mulaiBulan, $akhirBulan])
            ->sum('nominal');

        $pengeluaranBulanIni = (float) Transaksi::query()
            ->where('jenis', 'keluar')
            ->where('status', 'aktif')
            ->whereBetween('tanggal', [$mulaiBulan, $akhirBulan])
            ->sum('nominal');

        $kategoriIuran = Kategori::query()->where('nama', 'Iuran Kematian')->first();

        $rtSudahSetor = $kategoriIuran
            ? Transaksi::query()
                ->where('kategori_id', $kategoriIuran->id)
                ->where('jenis', 'masuk')
                ->where('status', 'aktif')
                ->whereBetween('tanggal', [$mulaiMinggu, $akhirMinggu])
                ->whereNotNull('rt_id')
                ->distinct()
                ->pluck('rt_id')
                ->map(fn ($id): int => (int) $id)
                ->all()
            : [];

        $daftarRw = Rw::query()
            ->with(['rt' => fn ($query) => $query->orderBy('nomor')])
            ->orderBy('nama')
            ->get();

        $totalRt = $daftarRw->sum(fn (Rw $rw): int => $rw->rt->count());
        $jumlahRtSetor = $daftarRw->sum(
            fn (Rw $rw): int => $rw->rt->filter(fn (Rt $rt): bool => in_array($rt->id, $rtSudahSetor, true))->count()
        );

        $setoranRw = $daftarRw->map(function (Rw $rw) use ($rtSudahSetor): array {
            return [
                'nama' => $rw->nama,
                'rt' => $rw->rt->map(fn (Rt $rt): array => [
                    'nomor' => $rt->nomor,
                    'sudah' => in_array($rt->id, $rtSudahSetor, true),
                ])->all(),
            ];
        })->filter(fn (array $rw): bool => $rw['rt'] !== [])->values();

        $transaksiTerbaru = Transaksi::query()
            ->with(['kategori', 'rt.rw'])
            ->where('status', 'aktif')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Transaksi $transaksi): array => [
                'tanggal' => $transaksi->tanggal
                    ? $transaksi->tanggal->copy()->locale('id')->translatedFormat('d M')
                    : '-',
                'jenis' => $transaksi->jenis,
                'keterangan' => $this->keterangan($transaksi),
                'nominal' => (float) $transaksi->nominal,
            ]);

        $acara = Acara::query()
            ->where('status', 'berjalan')
            ->orderByDesc('tahun')
            ->first()
            ?? Acara::query()->orderByDesc('tahun')->orderByDesc('id')->first();

        $totalAnggaranAcara = $acara
            ? (float) $acara->anggaran()->sum('nominal_anggaran')
            : 0.0;

        $realisasiAcara = $acara
            ? (float) Transaksi::query()
                ->where('acara_id', $acara->id)
                ->where('jenis', 'keluar')
                ->where('status', 'aktif')
                ->sum('nominal')
            : 0.0;

        $persenRealisasiAcara = $totalAnggaranAcara > 0
            ? (int) min(100, round($realisasiAcara / $totalAnggaranAcara * 100))
            : 0;

        $jam = (int) $sekarang->format('G');
        $sapaan = match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };

        return view('pages.dashboard', [
            'sapaan' => $sapaan,
            'namaPengguna' => $request->user()->name,
            'tanggalHariIni' => $sekarang->copy()->locale('id')->translatedFormat('l, j F Y'),
            'totalSaldo' => array_sum($saldoPos),
            'jumlahPos' => $pos->count(),
            'pemasukanBulanIni' => $pemasukanBulanIni,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'labelBulanIni' => $sekarang->copy()->locale('id')->translatedFormat('F Y'),
            'jumlahRtSetor' => $jumlahRtSetor,
            'totalRt' => $totalRt,
            'periodeSetoran' => $sekarang->copy()->startOfWeek(Carbon::MONDAY)->locale('id')->translatedFormat('d M')
                .' - '.$sekarang->copy()->endOfWeek(Carbon::SUNDAY)->locale('id')->translatedFormat('d M'),
            'setoranRw' => $setoranRw,
            'transaksiTerbaru' => $transaksiTerbaru,
            'rtBelumSetor' => max(0, $totalRt - $jumlahRtSetor),
            'acara' => $acara,
            'persenRealisasiAcara' => $persenRealisasiAcara,
        ]);
    }

    private function keterangan(Transaksi $transaksi): string
    {
        $catatan = trim((string) $transaksi->catatan_publik);
        $dasar = ($catatan !== '' && $catatan !== '-')
            ? $catatan
            : ($transaksi->kategori?->nama ?? 'Transaksi');

        if ($transaksi->jenis === 'masuk' && $transaksi->rt) {
            $rw = $transaksi->rt->rw?->nama ?? 'RW ?';

            return $dasar.', '.$rw.' RT '.$transaksi->rt->nomor;
        }

        return $dasar;
    }
}
