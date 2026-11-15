<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    public function index(): View
    {
        return view('pages.transaksi');
    }

    public function data(): JsonResponse
    {
        $query = Transaksi::query()
            ->with(['posDana', 'kategori', 'rt.rw', 'acara'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

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
            ->addColumn('nominal_format', fn (Transaksi $transaksi): string => $this->nominal($transaksi))
            ->addColumn('status_label', fn (Transaksi $transaksi): string => $transaksi->status === 'batal'
                ? '<span class="badge-soft badge-neutral">Batal</span>'
                : '<span class="badge-soft badge-in">Aktif</span>')
            ->addColumn('status_nilai', fn (Transaksi $transaksi): string => $transaksi->status)
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
            ->rawColumns(['jenis_label', 'keterangan', 'nominal_format', 'status_label'])
            ->toJson();
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
