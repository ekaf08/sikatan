<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Rt;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class IuranKematianPublikController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = (int) $request->input('tahun', now()->year);
        if ($tahun < 2000 || $tahun > now()->year) {
            $tahun = now()->year;
        }

        $bulan = (int) $request->input('bulan', now()->month);
        if ($bulan < 1 || $bulan > 12) {
            $bulan = now()->month;
        }

        $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        $jumlahMinggu = (int) ceil($akhirBulan->daysInMonth / 7);
        $mingguDefault = min((int) ceil(now()->day / 7), $jumlahMinggu);
        $minggu = (int) $request->input('minggu', $mingguDefault);
        $peringatanMinggu = null;

        if ($minggu < 0 || $minggu > $jumlahMinggu) {
            if ($minggu > $jumlahMinggu) {
                $peringatanMinggu = 'Bulan '.$awalBulan->copy()->locale('id')->translatedFormat('F Y')
                    .' hanya memiliki '.$jumlahMinggu.' minggu, sehingga filter minggu ke-'.$minggu
                    .' tidak tersedia. Menampilkan seluruh bulan.';
            }

            $minggu = 0;
        }

        $mulai = $awalBulan->copy();
        $selesai = $akhirBulan->copy();

        if ($minggu > 0) {
            $mulai = $awalBulan->copy()->addDays(($minggu - 1) * 7);

            $selesai = $mulai->copy()->addDays(6);
            if ($selesai->greaterThan($akhirBulan)) {
                $selesai = $akhirBulan->copy();
            }
        }

        $kategoriId = Kategori::query()
            ->where('jenis', 'masuk')
            ->where('nama', 'Iuran Kematian')
            ->value('id');

        $pembayaran = collect();

        if ($kategoriId) {
            $pembayaran = Transaksi::query()
                ->where('jenis', 'masuk')
                ->where('status', 'aktif')
                ->where('kategori_id', $kategoriId)
                ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
                ->whereNotNull('rt_id')
                ->groupBy('rt_id')
                ->selectRaw('rt_id, SUM(nominal) as total_nominal, SUM(jumlah_kk_bayar) as total_kk')
                ->get()
                ->keyBy('rt_id');
        }

        $daftarRt = Rt::query()
            ->with('rw')
            ->orderBy('rw_id')
            ->orderBy('nomor')
            ->get();

        $urutanStatus = ['belum' => 0, 'sebagian' => 1, 'lunas' => 2];

        $daftarIuran = $daftarRt
            ->map(function (Rt $rt) use ($pembayaran): array {
                $bayar = $pembayaran->get($rt->id);
                $totalNominal = (float) ($bayar->total_nominal ?? 0);
                $totalKk = (int) ($bayar->total_kk ?? 0);
                $jumlahKk = (int) $rt->jumlah_kk;

                if ($totalNominal <= 0) {
                    $status = 'belum';
                } elseif ($totalKk < $jumlahKk) {
                    $status = 'sebagian';
                } else {
                    $status = 'lunas';
                }

                return [
                    'rw_id' => $rt->rw_id,
                    'nomor' => $rt->nomor,
                    'rw' => $rt->rw?->nama ?? '-',
                    'rt' => $rt->nomor,
                    'penarik' => $rt->nama_penarik ?: '-',
                    'jumlah_kk' => $jumlahKk,
                    'total_kk' => $totalKk,
                    'nominal' => $totalNominal,
                    'status' => $status,
                ];
            })
            ->sortBy(fn (array $baris): string => sprintf(
                '%d-%05d-%05d',
                $urutanStatus[$baris['status']],
                $baris['rw_id'],
                $baris['nomor']
            ))
            ->values();

        $jumlahBelumSetor = $daftarIuran->where('status', 'belum')->count();

        $labelPeriode = $minggu > 0
            ? 'Minggu ke-'.$minggu.', '.$awalBulan->copy()->locale('id')->translatedFormat('F Y')
            : $awalBulan->copy()->locale('id')->translatedFormat('F Y');

        $opsiBulan = collect(range(1, 12))->map(fn (int $nomor): array => [
            'value' => $nomor,
            'label' => Carbon::create()->month($nomor)->locale('id')->translatedFormat('F'),
        ]);

        return view('fo.iuran-kematian', [
            'daftarIuran' => $daftarIuran,
            'jumlahRt' => $daftarRt->count(),
            'jumlahSudahSetor' => $daftarRt->count() - $jumlahBelumSetor,
            'jumlahBelumSetor' => $jumlahBelumSetor,
            'jumlahMinggu' => $jumlahMinggu,
            'peringatanMinggu' => $peringatanMinggu,
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'minggu' => $minggu,
            ],
            'labelPeriode' => $labelPeriode,
            'opsiBulan' => $opsiBulan,
            'opsiTahun' => range(now()->year, now()->year - 5),
        ]);
    }
}
