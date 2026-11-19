<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosDana;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    public function index(): View
    {
        return view('pages.transaksi', [
            'posDana' => PosDana::query()->orderBy('nama')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = $this->queryTerfilter($request);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('tanggal_format', fn (Transaksi $transaksi): string => $transaksi->tanggal
                ? $transaksi->tanggal->copy()->locale('id')->translatedFormat('d M Y')
                : '-')
            ->addColumn('jenis_label', fn (Transaksi $transaksi): string => $transaksi->jenis === 'masuk'
                ? '<span class="badge-soft badge-in">Masuk</span>'
                : '<span class="badge-soft badge-out">Keluar</span>')
            ->addColumn('pos_nama', fn (Transaksi $transaksi): string => $transaksi->posDana?->nama ?? '-')
            ->addColumn('kategori_nama', fn (Transaksi $transaksi): string => $transaksi->kategori?->nama ?? '-')
            ->addColumn('keterangan', fn (Transaksi $transaksi): string => $this->keterangan($transaksi))
            ->addColumn('foto', fn (Transaksi $transaksi): string => $this->fotoHtml($transaksi))
            ->addColumn('nominal_format', fn (Transaksi $transaksi): string => $this->nominal($transaksi))
            ->addColumn('status_label', fn (Transaksi $transaksi): string => $this->statusBatal($transaksi)
                ? '<span class="badge-soft badge-neutral">Batal</span>'
                : '<span class="badge-soft badge-in">Aktif</span>')
            ->addColumn('status_nilai', fn (Transaksi $transaksi): string => $this->statusBatal($transaksi) ? 'batal' : 'aktif')
            ->addColumn('aksi', fn (Transaksi $transaksi): string => '<button type="button" class="btn btn-sm btn-outline-secondary btn-detail-transaksi" data-id="'.$transaksi->id.'" title="Lihat detail"><i class="bi bi-eye"></i></button>')
            ->filterColumn('pos_nama', function ($query, string $keyword): void {
                $query->whereHas('posDana', fn ($sub) => $this->cariNama($sub, 'nama', $keyword));
            })
            ->filterColumn('kategori_nama', function ($query, string $keyword): void {
                $query->whereHas('kategori', fn ($sub) => $this->cariNama($sub, 'nama', $keyword));
            })
            ->filterColumn('jenis_label', function ($query, string $keyword): void {
                $kata = Str::lower($keyword);

                if (str_contains($kata, 'masuk')) {
                    $query->where('jenis', 'masuk');

                    return;
                }

                if (str_contains($kata, 'keluar')) {
                    $query->where('jenis', 'keluar');

                    return;
                }

                $query->whereRaw('1 = 0');
            })
            ->filterColumn('keterangan', function ($query, string $keyword): void {
                $kata = '%'.Str::lower($keyword).'%';

                $query->where(function ($sub) use ($kata, $keyword): void {
                    $sub->whereRaw('LOWER(catatan_publik) LIKE ?', [$kata])
                        ->orWhereRaw('LOWER(catatan_internal) LIKE ?', [$kata])
                        ->orWhereRaw('LOWER(nama_donatur) LIKE ?', [$kata])
                        ->orWhereRaw('LOWER(tujuan) LIKE ?', [$kata])
                        ->orWhereHas('acara', fn ($acara) => $this->cariNama($acara, 'nama', $keyword))
                        ->orWhereHas('rt', fn ($rt) => $rt->whereRaw('LOWER(nomor) LIKE ?', [$kata]));
                });
            })
            ->rawColumns(['jenis_label', 'keterangan', 'foto', 'nominal_format', 'status_label', 'aksi'])
            ->toJson();
    }

    public function detail(int $id): View
    {
        $transaksi = Transaksi::query()
            ->withTrashed()
            ->with([
                'posDana',
                'kategori',
                'rt.rw',
                'acara',
                'anggaranAcara',
                'dibuatOleh',
                'lampiran' => fn ($lampiran) => $lampiran->withTrashed(),
            ])
            ->findOrFail($id);

        return view('pages.partials.transaksi-detail', compact('transaksi'));
    }

    public function eksporExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = $this->dataEkspor($request);
        $namaBerkas = 'daftar-transaksi-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($data, $request): void {
            $keluaran = fopen('php://output', 'w');
            fwrite($keluaran, "\xEF\xBB\xBF");

            fputcsv($keluaran, ['DAFTAR TRANSAKSI - SISTEM KAMPUNG BABATAN'], ';', '"', '');
            fputcsv($keluaran, ['Filter: '.$this->ringkasanFilter($request)], ';', '"', '');
            fputcsv($keluaran, ['Dicetak: '.now()->locale('id')->translatedFormat('d F Y H:i')], ';', '"', '');
            fputcsv($keluaran, [], ';', '"', '');
            fputcsv($keluaran, ['No', 'Tanggal', 'Jenis', 'Pos Dana', 'Kategori', 'Keterangan', 'Nominal (Rp)', 'Status'], ';', '"', '');

            foreach ($data as $i => $t) {
                fputcsv($keluaran, [
                    $i + 1,
                    $t->tanggal ? $t->tanggal->format('d/m/Y') : '-',
                    $t->jenis === 'masuk' ? 'Masuk' : 'Keluar',
                    $t->posDana?->nama ?? '-',
                    $t->kategori?->nama ?? '-',
                    $this->keteranganTeks($t),
                    number_format((float) $t->nominal, 2, ',', '.'),
                    $this->statusBatal($t) ? 'Batal' : 'Aktif',
                ], ';', '"', '');
            }

            fclose($keluaran);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function eksporPdf(Request $request): View
    {
        return view('pages.transaksi-cetak', [
            'data' => $this->dataEkspor($request),
            'filterLabel' => $this->ringkasanFilter($request),
            'dicetakPada' => now()->locale('id')->translatedFormat('d F Y H:i'),
        ]);
    }

    private function queryTerfilter(Request $request)
    {
        $query = Transaksi::query()
            ->withTrashed()
            ->with([
                'posDana',
                'kategori',
                'rt.rw',
                'acara',
                'lampiran' => fn ($lampiran) => $lampiran->withTrashed(),
            ])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $this->terapkanFilter($query, $request);

        return $query;
    }

    private function dataEkspor(Request $request)
    {
        return $this->queryTerfilter($request)->get();
    }

    private function ringkasanFilter(Request $request): string
    {
        $bagian = [];

        $jenis = $request->input('jenis');
        if (in_array($jenis, ['masuk', 'keluar'], true)) {
            $bagian[] = 'Jenis: '.($jenis === 'masuk' ? 'Masuk' : 'Keluar');
        }

        $posDanaId = $request->input('pos_dana_id');
        if ($posDanaId && ctype_digit((string) $posDanaId)) {
            $pos = PosDana::query()->find((int) $posDanaId);
            if ($pos) {
                $bagian[] = 'Pos dana: '.$pos->nama;
            }
        }

        $status = $request->input('status');
        if ($status === 'aktif') {
            $bagian[] = 'Status: Aktif';
        } elseif ($status === 'batal') {
            $bagian[] = 'Status: Batal';
        }

        $dari = $request->input('dari');
        $sampai = $request->input('sampai');
        if ($this->tanggalValid($dari) || $this->tanggalValid($sampai)) {
            $bagian[] = 'Periode: '.($this->tanggalValid($dari) ? $dari : '...').' s.d. '.($this->tanggalValid($sampai) ? $sampai : '...');
        }

        return $bagian === [] ? 'Semua data' : implode(', ', $bagian);
    }

    private function keteranganTeks(Transaksi $transaksi): string
    {
        $bagian = [];

        if ($transaksi->jenis === 'masuk' && $transaksi->rt) {
            $bagian[] = 'RT '.$transaksi->rt->nomor.' / '.($transaksi->rt->rw?->nama ?? '-');
        }
        if ($transaksi->jenis === 'masuk' && $transaksi->jumlah_kk_bayar) {
            $bagian[] = $transaksi->jumlah_kk_bayar.' KK';
        }
        if ($transaksi->nama_donatur) {
            $bagian[] = $transaksi->donatur_anonim ? 'Hamba Allah' : $transaksi->nama_donatur;
        }
        if ($transaksi->acara) {
            $bagian[] = $transaksi->acara->nama;
        }
        if ($transaksi->catatan_publik) {
            $bagian[] = Str::limit($transaksi->catatan_publik, 60, '');
        }

        return $bagian === [] ? '-' : implode(' - ', $bagian);
    }

    private function terapkanFilter($query, Request $request): void
    {
        $jenis = $request->input('jenis');

        if (in_array($jenis, ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
        }

        $posDanaId = $request->input('pos_dana_id');

        if ($posDanaId !== null && $posDanaId !== '' && ctype_digit((string) $posDanaId)) {
            $query->where('pos_dana_id', (int) $posDanaId);
        }

        $status = $request->input('status');

        if ($status === 'aktif') {
            $query->whereNull('deleted_at')->where('status', 'aktif');
        } elseif ($status === 'batal') {
            $query->where(function ($sub): void {
                $sub->whereNotNull('deleted_at')->orWhere('status', 'batal');
            });
        }

        $dari = $request->input('dari');

        if ($this->tanggalValid($dari)) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        $sampai = $request->input('sampai');

        if ($this->tanggalValid($sampai)) {
            $query->whereDate('tanggal', '<=', $sampai);
        }
    }

    private function tanggalValid(?string $tanggal): bool
    {
        if (! $tanggal) {
            return false;
        }

        $hasil = \DateTime::createFromFormat('Y-m-d', $tanggal);

        return $hasil !== false && $hasil->format('Y-m-d') === $tanggal;
    }

    private function fotoHtml(Transaksi $transaksi): string
    {
        if ($transaksi->lampiran->isEmpty()) {
            return '<span class="hint">&mdash;</span>';
        }

        $html = '<div class="d-flex flex-wrap gap-1">';

        foreach ($transaksi->lampiran->take(3) as $lampiran) {
            $url = e(asset('storage/'.$lampiran->lokasi_berkas));
            $html .= '<a href="'.$url.'" target="_blank" rel="noopener">'
                .'<img src="'.$url.'" alt="Foto transaksi" style="width:40px;height:40px;object-fit:cover;border-radius:.375rem;border:1px solid rgba(0,0,0,.15)">'
                .'</a>';
        }

        if ($transaksi->lampiran->count() > 3) {
            $html .= '<span class="badge-soft badge-neutral">+'.($transaksi->lampiran->count() - 3).'</span>';
        }

        return $html.'</div>';
    }

    private function statusBatal(Transaksi $transaksi): bool
    {
        return $transaksi->trashed() || $transaksi->status === 'batal';
    }

    private function keterangan(Transaksi $transaksi): string
    {
        $bagian = [];

        if ($transaksi->jenis === 'masuk' && $transaksi->rt) {
            $bagian[] = 'RT '.$transaksi->rt->nomor.' / '.($transaksi->rt->rw?->nama ?? '-');
        }

        if ($transaksi->jenis === 'masuk' && $transaksi->jumlah_kk_bayar) {
            $bagian[] = $transaksi->jumlah_kk_bayar.' KK';
        }

        if ($transaksi->nama_donatur) {
            $bagian[] = $transaksi->donatur_anonim ? 'Hamba Allah' : $transaksi->nama_donatur;
        }

        if ($transaksi->acara) {
            $bagian[] = $transaksi->acara->nama;
        }

        if ($transaksi->catatan_publik) {
            $bagian[] = Str::limit($transaksi->catatan_publik, 60);
        }

        return $bagian === []
            ? '<span class="hint">&mdash;</span>'
            : e(implode(' · ', $bagian));
    }

    private function nominal(Transaksi $transaksi): string
    {
        $nominal = 'Rp'.number_format((float) $transaksi->nominal, 0, ',', '.');
        $warna = $transaksi->jenis === 'masuk' ? 'var(--ks-success-text)' : 'var(--ks-warning-text)';
        $tanda = $transaksi->jenis === 'masuk' ? '+' : '-';

        return '<span style="color:'.$warna.';white-space:nowrap">'.$tanda.$nominal.'</span>';
    }

    private function cariNama($query, string $kolom, string $keyword): void
    {
        $kata = '%'.Str::lower($keyword).'%';

        $query->whereRaw('LOWER('.$kolom.') LIKE ?', [$kata]);
    }
}
