<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\SaldoDana;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BerandaPublikController extends Controller
{
    public function __construct(private readonly SaldoDana $saldoDana) {}

    public function index(): View
    {
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

        $pengeluaranTerbaru = Transaksi::query()
            ->with(['posDana', 'kategori', 'acara', 'lampiran'])
            ->where('jenis', 'keluar')
            ->where('status', 'aktif')
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
            ]);

        $pemasukanBulanan = $this->pemasukanBulanan();

        return view('fo.home', [
            'posDana' => $posDana,
            'totalSaldo' => $totalSaldo,
            'terakhirDiperbarui' => $terakhirDiperbarui,
            'pengeluaranTerbaru' => $pengeluaranTerbaru,
            'pemasukanBulanan' => $pemasukanBulanan,
        ]);
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

        return $bagian !== [] ? implode(' · ', $bagian) : 'Pengeluaran';
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

    private function pemasukanBulanan(): array
    {
        $periode = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i)->startOfMonth());
        $data = $periode->map(function (Carbon $bulan) {
            $total = (float) Transaksi::query()
                ->where('jenis', 'masuk')
                ->where('status', 'aktif')
                ->whereYear('tanggal', $bulan->year)
                ->whereMonth('tanggal', $bulan->month)
                ->sum('nominal');

            return [
                'label' => $bulan->copy()->locale('id')->translatedFormat('M'),
                'total' => $total,
                'berjalan' => $bulan->isSameMonth(now()),
            ];
        })->values()->all();

        $max = max(1.0, (float) collect($data)->max('total'));
        foreach ($data as &$row) {
            $row['tinggi'] = $row['total'] > 0 ? max(12, (int) round(($row['total'] / $max) * 140)) : 6;
            $row['tampil'] = $this->formatJutaan($row['total']);
        }

        return $data;
    }

    private function formatJutaan(float $nilai): string
    {
        if ($nilai >= 1000000) {
            $juta = $nilai / 1000000;
            $teks = number_format($juta, $juta >= 10 ? 0 : 1, ',', '.');
            return rtrim(rtrim($teks, '0'), ',');
        }
        if ($nilai >= 1000) {
            return number_format($nilai / 1000, 0, ',', '.').'rb';
        }
        if ($nilai <= 0) {
            return '0';
        }
        return number_format($nilai, 0, ',', '.');
    }
}
