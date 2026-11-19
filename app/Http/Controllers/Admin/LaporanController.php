<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acara;
use App\Models\MutasiDana;
use App\Models\Pengaturan;
use App\Models\PosDana;
use App\Models\Transaksi;
use App\Services\SaldoDana;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private const PILIHAN_ISI = ['kategori', 'pengeluaran', 'anggaran', 'internal'];

    private const ISI_DEFAULT = ['kategori', 'pengeluaran', 'anggaran'];

    public function __construct(private readonly SaldoDana $saldo) {}

    public function index(Request $request): View
    {
        $filter = $this->filter($request);

        return view('pages.laporan', [
            'daftarPos' => PosDana::query()->orderBy('nama')->get(),
            'opsiPeriode' => $this->periodeOpsi(),
            'filter' => $filter,
            'data' => $this->laporan($filter),
            'pengaturan' => $this->pengaturan(),
        ]);
    }

    public function data(Request $request): View
    {
        $filter = $this->filter($request);

        return view('pages.laporan-preview', [
            'data' => $this->laporan($filter),
            'isi' => $filter['isi'],
            'pengaturan' => $this->pengaturan(),
        ]);
    }

    public function eksporExcel(Request $request): StreamedResponse
    {
        $filter = $this->filter($request);
        $data = $this->laporan($filter);

        $namaBerkas = 'laporan-kas-'.$data['tanggal_mulai'].'-sampai-'.$data['tanggal_selesai'].'.csv';

        return response()->streamDownload(function () use ($data): void {
            $keluaran = fopen('php://output', 'w');

            fwrite($keluaran, "\xEF\xBB\xBF");

            $baris = [
                [$data['kop_baris_1']],
                [$data['kop_baris_2']],
                [$data['periode_label']],
                ['Pos dana: '.$data['pos_label']],
                [],
                ['RINGKASAN'],
                ['Keterangan', 'Jumlah (Rp)'],
                ['Saldo awal', $this->angkaCsv($data['saldo_awal'])],
                ['Total pemasukan', $this->angkaCsv($data['total_masuk'])],
                ['Total pengeluaran', $this->angkaCsv($data['total_keluar'])],
                ['Saldo akhir', $this->angkaCsv($data['saldo_akhir'])],
                [],
                ['A. PEMASUKAN PER KATEGORI'],
                ['Kategori', 'Jumlah (Rp)'],
            ];

            foreach ($data['pemasukan_kategori'] as $kategori) {
                $baris[] = [$kategori['nama'], $this->angkaCsv($kategori['jumlah'])];
            }

            if ($data['pemasukan_kategori'] === []) {
                $baris[] = ['Tidak ada pemasukan pada periode ini', '0'];
            }

            $baris[] = [];
            $baris[] = ['B. DAFTAR PENGELUARAN'];
            $baris[] = ['Tanggal', 'Pos dana', 'Kategori', 'Keterangan', 'Penerima', 'Jumlah (Rp)'];

            foreach ($data['pengeluaran'] as $item) {
                $baris[] = [
                    $item['tanggal_label'],
                    $item['pos'],
                    $item['kategori'],
                    $item['keterangan'],
                    $item['penerima'] ?? '-',
                    $this->angkaCsv($item['nominal']),
                ];
            }

            if ($data['pengeluaran'] === []) {
                $baris[] = ['Tidak ada pengeluaran pada periode ini'];
            }

            $baris[] = [];
            $baris[] = ['C. ANGGARAN VS REALISASI ACARA'];
            $baris[] = ['Acara', 'Tahun', 'Anggaran (Rp)', 'Realisasi (Rp)', 'Selisih (Rp)'];

            foreach ($data['anggaran'] as $item) {
                $selisih = $item['anggaran'] - $item['realisasi'];
                $baris[] = [
                    $item['nama'],
                    $item['tahun'],
                    $this->angkaCsv($item['anggaran']),
                    $this->angkaCsv($item['realisasi']),
                    $this->angkaCsv($selisih),
                ];
            }

            if ($data['anggaran'] === []) {
                $baris[] = ['Tidak ada anggaran acara pada periode ini'];
            }

            $baris[] = [];
            $baris[] = ['D. MUTASI DANA'];
            $baris[] = ['Tanggal', 'Dari pos', 'Ke pos', 'Keterangan', 'Nominal (Rp)'];

            foreach ($data['mutasi'] as $item) {
                $baris[] = [
                    $item['tanggal_label'],
                    $item['dari'],
                    $item['ke'],
                    $item['keterangan'],
                    $this->angkaCsv($item['nominal']),
                ];
            }

            if ($data['mutasi'] === []) {
                $baris[] = ['Tidak ada mutasi dana pada periode ini'];
            }

            foreach ($baris as $row) {
                fputcsv($keluaran, $row, ';', '"', '');
            }

            fclose($keluaran);
        }, $namaBerkas, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function eksporPdf(Request $request): View
    {
        $filter = $this->filter($request);

        return view('pages.cetak.laporan-cetak', [
            'data' => $this->laporan($filter),
            'isi' => $filter['isi'],
            'pengaturan' => $this->pengaturan(),
        ]);
    }

    private function laporan(array $filter): array
    {
        $mulai = Carbon::parse($filter['tanggal_mulai'])->startOfDay();
        $selesai = Carbon::parse($filter['tanggal_selesai'])->endOfDay();
        $posId = $filter['pos_dana_id'];

        $posDana = PosDana::query()
            ->when($posId, fn ($query) => $query->where('id', $posId))
            ->orderBy('nama')
            ->get();

        $saldoAwal = 0.0;
        $saldoAkhir = 0.0;

        foreach ($posDana as $pos) {
            $saldoAwal += $this->saldo->hitung($pos, $mulai->toDateString());
            $saldoAkhir += $this->saldo->hitung($pos, $selesai->copy()->addDay()->toDateString());
        }

        $base = Transaksi::query()
            ->where('status', 'aktif')
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->when($posId, fn ($query) => $query->where('pos_dana_id', $posId));

        $totalMasuk = (float) (clone $base)->where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) (clone $base)->where('jenis', 'keluar')->sum('nominal');

        $pemasukanKategori = (clone $base)
            ->where('jenis', 'masuk')
            ->with('kategori')
            ->get()
            ->groupBy(fn (Transaksi $transaksi): string => $transaksi->kategori?->nama ?? 'Lainnya')
            ->map(fn (Collection $items, string $nama): array => [
                'nama' => $nama,
                'jumlah' => (float) $items->sum('nominal'),
            ])
            ->sortBy('nama')
            ->values()
            ->all();

        $pengeluaran = (clone $base)
            ->where('jenis', 'keluar')
            ->with(['posDana', 'kategori', 'acara'])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->map(fn (Transaksi $transaksi): array => [
                'tanggal' => $transaksi->tanggal?->toDateString(),
                'tanggal_label' => $transaksi->tanggal
                    ? $transaksi->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                    : '-',
                'pos' => $transaksi->posDana?->nama ?? '-',
                'kategori' => $transaksi->kategori?->nama ?? '-',
                'acara' => $transaksi->acara?->nama ?? '-',
                'keterangan' => $transaksi->catatan_publik
                    ?: ($transaksi->kategori?->nama ?? 'Pengeluaran'),
                'penerima' => $transaksi->penerima,
                'nominal' => (float) $transaksi->nominal,
                'catatan_internal' => $transaksi->catatan_internal,
            ])
            ->values()
            ->all();

        $realisasi = (clone $base)
            ->where('jenis', 'keluar')
            ->whereNotNull('acara_id')
            ->select('acara_id', DB::raw('SUM(nominal) as total'))
            ->groupBy('acara_id')
            ->pluck('total', 'acara_id');

        $anggaran = Acara::query()
            ->with('anggaran')
            ->orderByDesc('tahun')
            ->orderBy('nama')
            ->get()
            ->map(fn (Acara $acara): array => [
                'nama' => $acara->nama,
                'tahun' => $acara->tahun,
                'anggaran' => (float) $acara->anggaran->sum('nominal_anggaran'),
                'realisasi' => (float) ($realisasi[$acara->id] ?? 0),
            ])
            ->filter(fn (array $item): bool => $item['anggaran'] > 0 || $item['realisasi'] > 0)
            ->values()
            ->all();

        $mutasi = MutasiDana::query()
            ->with(['dariPos', 'kePos'])
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->when($posId, fn ($query) => $query->where(function ($sub) use ($posId): void {
                $sub->where('dari_pos_id', $posId)->orWhere('ke_pos_id', $posId);
            }))
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get()
            ->map(fn (MutasiDana $item): array => [
                'tanggal' => $item->tanggal?->toDateString(),
                'tanggal_label' => $item->tanggal
                    ? $item->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                    : '-',
                'dari' => $item->dariPos?->nama ?? '-',
                'ke' => $item->kePos?->nama ?? '-',
                'keterangan' => $item->keterangan ?: '-',
                'nominal' => (float) $item->nominal,
            ])
            ->values()
            ->all();

        $internal = array_values(array_filter($pengeluaran, fn (array $item): bool => filled($item['catatan_internal'])));

        $pengaturan = $this->pengaturan();

        return [
            'kop_baris_1' => $pengaturan['kop_baris_1'] ?? 'PEMERINTAH DESA BABATAN',
            'kop_baris_2' => $pengaturan['kop_baris_2'] ?? 'LAPORAN KEUANGAN KAS WARGA',
            'kop_alamat' => $pengaturan['kop_alamat'] ?? '',
            'nama_bendahara' => $pengaturan['nama_bendahara'] ?? 'Bendahara',
            'nama_ketua' => $pengaturan['nama_ketua'] ?? 'Ketua',
            'periode_label' => $filter['label'],
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'tanggal_mulai_label' => $mulai->copy()->locale('id')->translatedFormat('d F Y'),
            'tanggal_selesai_label' => $selesai->copy()->locale('id')->translatedFormat('d F Y'),
            'pos_label' => $posId ? ($posDana->first()?->nama ?? 'Pos tidak ditemukan') : 'Semua pos dana',
            'saldo_awal' => $saldoAwal,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'saldo_akhir' => $saldoAkhir,
            'pemasukan_kategori' => $pemasukanKategori,
            'pengeluaran' => $pengeluaran,
            'anggaran' => $anggaran,
            'mutasi' => $mutasi,
            'internal' => $internal,
        ];
    }

    private function filter(Request $request): array
    {
        $opsi = $this->periodeOpsi();
        $default = $this->periodeDefault();
        $adaFilter = $request->hasAny(['periode', 'pos_dana_id', 'isi_present', 'tanggal_mulai', 'tanggal_selesai']);

        $periode = $adaFilter
            ? (string) $request->input('periode', $default)
            : $default;

        $dipilih = collect($opsi)->firstWhere('value', $periode);

        if ($periode === 'custom') {
            $mulai = $this->parseTanggal($request->input('tanggal_mulai')) ?? Carbon::now()->startOfQuarter();
            $selesai = $this->parseTanggal($request->input('tanggal_selesai')) ?? Carbon::now()->endOfQuarter();

            if ($mulai->greaterThan($selesai)) {
                [$mulai, $selesai] = [$selesai, $mulai];
            }

            $label = $mulai->copy()->locale('id')->translatedFormat('d F Y')
                .' sampai '.$selesai->copy()->locale('id')->translatedFormat('d F Y');
        } else {
            $dipilih ??= collect($opsi)->firstWhere('value', $default);
            $periode = $dipilih['value'];
            $mulai = Carbon::parse($dipilih['mulai']);
            $selesai = Carbon::parse($dipilih['selesai']);
            $label = $dipilih['label_lengkap'];
        }

        $posId = $request->input('pos_dana_id');
        $posId = $posId !== null && $posId !== '' ? (int) $posId : null;

        if ($posId !== null && ! PosDana::query()->whereKey($posId)->exists()) {
            $posId = null;
        }

        $isi = $request->has('isi_present') || $request->has('isi')
            ? (array) $request->input('isi', [])
            : self::ISI_DEFAULT;

        $isi = array_values(array_intersect(self::PILIHAN_ISI, $isi));

        return [
            'periode' => $periode,
            'tanggal_mulai' => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'label' => $label,
            'pos_dana_id' => $posId,
            'isi' => $isi,
        ];
    }

    private function periodeOpsi(): array
    {
        $tahun = (int) now()->year;
        $opsi = [];

        for ($kuartal = 1; $kuartal <= 4; $kuartal++) {
            $mulai = Carbon::create($tahun, ($kuartal - 1) * 3 + 1, 1)->startOfDay();
            $selesai = $mulai->copy()->addMonths(3)->subDay();
            $bulanAwal = $mulai->copy()->locale('id')->translatedFormat('M');
            $bulanAkhir = $selesai->copy()->locale('id')->translatedFormat('M');

            $opsi[] = [
                'value' => 'Q'.$kuartal.'-'.$tahun,
                'label' => 'Triwulan '.$kuartal.' '.$tahun.' ('.$bulanAwal.'-'.$bulanAkhir.')',
                'label_lengkap' => 'Triwulan '.$kuartal.' Tahun '.$tahun.' ('
                    .$mulai->copy()->locale('id')->translatedFormat('j F').' sampai '
                    .$selesai->copy()->locale('id')->translatedFormat('j F Y').')',
                'mulai' => $mulai->toDateString(),
                'selesai' => $selesai->toDateString(),
            ];
        }

        $awalTahun = Carbon::create($tahun, 1, 1)->startOfDay();
        $akhirTahun = Carbon::create($tahun, 12, 31)->startOfDay();

        $opsi[] = [
            'value' => 'Y-'.$tahun,
            'label' => 'Tahun '.$tahun,
            'label_lengkap' => 'Tahun '.$tahun.' ('
                .$awalTahun->copy()->locale('id')->translatedFormat('j F').' sampai '
                .$akhirTahun->copy()->locale('id')->translatedFormat('j F Y').')',
            'mulai' => $awalTahun->toDateString(),
            'selesai' => $akhirTahun->toDateString(),
        ];

        $opsi[] = [
            'value' => 'custom',
            'label' => 'Rentang tanggal sendiri',
            'label_lengkap' => 'Rentang tanggal sendiri',
            'mulai' => null,
            'selesai' => null,
        ];

        return $opsi;
    }

    private function periodeDefault(): string
    {
        return 'Q'.(int) ceil(now()->month / 3).'-'.now()->year;
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

    private function pengaturan(): array
    {
        return Pengaturan::query()->pluck('nilai', 'kunci')->toArray();
    }

    private function angkaCsv(float $nilai): string
    {
        return number_format($nilai, 2, ',', '.');
    }
}
