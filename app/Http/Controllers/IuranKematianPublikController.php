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

        $mingguDefault = (int) ceil(now()->day / 7);
        $minggu = (int) $request->input('minggu', $mingguDefault);
        if (! in_array($minggu, [0, 1, 2, 3, 4, 5], true)) {
            $minggu = $mingguDefault;
        }

        $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        $mulai = $awalBulan->copy();
        $selesai = $akhirBulan->copy();

        if ($minggu > 0) {
            $mulai = $awalBulan->copy()->addDays(($minggu - 1) * 7);
            if ($mulai->greaterThan($akhirBulan)) {
                $mulai = $akhirBulan->copy()->startOfDay();
            }

            $selesai = $mulai->copy()->addDays(6);
            if ($selesai->greaterThan($akhirBulan)) {
                $selesai = $akhirBulan->copy();
            }
        }

        $kategoriId = Kategori::query()
            ->where('jenis', 'masuk')
            ->where('nama', 'Iuran Kematian')
            ->value('id');

        $sudahSetor = $kategoriId
            ? Transaksi::query()
                ->where('jenis', 'masuk')
                ->where('status', 'aktif')
                ->where('kategori_id', $kategoriId)
                ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
                ->whereNotNull('rt_id')
                ->distinct()
                ->pluck('rt_id')
                ->all()
            : [];

        $daftarRt = Rt::query()
            ->with('rw')
            ->orderBy('rw_id')
            ->orderBy('nomor')
            ->get();

        $belumSetor = $daftarRt
            ->reject(fn (Rt $rt) => in_array($rt->id, $sudahSetor, true))
            ->values();

        $labelPeriode = $minggu > 0
            ? 'Minggu ke-'.$minggu.', '.$awalBulan->copy()->locale('id')->translatedFormat('F Y')
            : $awalBulan->copy()->locale('id')->translatedFormat('F Y');

        $opsiBulan = collect(range(1, 12))->map(fn (int $nomor): array => [
            'value' => $nomor,
            'label' => Carbon::create()->month($nomor)->locale('id')->translatedFormat('F'),
        ]);

        return view('fo.iuran-kematian', [
            'belumSetor' => $belumSetor,
            'jumlahRt' => $daftarRt->count(),
            'jumlahSudahSetor' => $daftarRt->count() - $belumSetor->count(),
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
