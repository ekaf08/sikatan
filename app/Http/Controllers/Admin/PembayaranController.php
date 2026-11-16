<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PembayaranController extends Controller
{
    private const STATUS_VALID = ['pending', 'settlement', 'expire', 'deny', 'cancel', 'gagal'];

    public function index(): View
    {
        return view('pages.pembayaran');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Pembayaran::query()
            ->with('transaksi')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $status = $request->input('status');

        if (in_array($status, self::STATUS_VALID, true)) {
            $query->where('status', $status);
        }

        $dari = $request->input('dari');

        if ($this->tanggalValid($dari)) {
            $query->whereDate('created_at', '>=', $dari);
        }

        $sampai = $request->input('sampai');

        if ($this->tanggalValid($sampai)) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('dibuat_format', fn (Pembayaran $pembayaran): string => $pembayaran->created_at
                ? $pembayaran->created_at->copy()->locale('id')->translatedFormat('d M Y H.i')
                : '-')
            ->addColumn('donatur_label', fn (Pembayaran $pembayaran): string => $this->donatur($pembayaran))
            ->addColumn('channel_label', fn (Pembayaran $pembayaran): string => $pembayaran->channel
                ? e(Str::upper($pembayaran->channel))
                : '<span class="hint">&mdash;</span>')
            ->addColumn('nominal_format', fn (Pembayaran $pembayaran): string => 'Rp'.number_format((float) $pembayaran->nominal, 0, ',', '.'))
            ->addColumn('status_label', fn (Pembayaran $pembayaran): string => $this->statusLabel($pembayaran->status))
            ->addColumn('transaksi_label', fn (Pembayaran $pembayaran): string => $this->transaksiLabel($pembayaran))
            ->filterColumn('dibuat_format', function ($query, string $keyword): void {
                $query->whereRaw('CAST(created_at AS TEXT) LIKE ?', ['%'.$keyword.'%']);
            })
            ->filterColumn('donatur_label', function ($query, string $keyword): void {
                $kata = '%'.Str::lower($keyword).'%';

                $query->where(function ($sub) use ($kata, $keyword): void {
                    $sub->whereRaw('LOWER(nama_donatur) LIKE ?', [$kata]);

                    if (str_contains(Str::lower($keyword), 'hamba')) {
                        $sub->orWhere('donatur_anonim', true);
                    }
                });
            })
            ->filterColumn('channel_label', function ($query, string $keyword): void {
                $query->whereRaw('LOWER(channel) LIKE ?', ['%'.Str::lower($keyword).'%']);
            })
            ->filterColumn('status_label', function ($query, string $keyword): void {
                $kata = Str::lower($keyword);
                $peta = [
                    'pending' => 'pending',
                    'menunggu' => 'pending',
                    'berhasil' => 'settlement',
                    'settlement' => 'settlement',
                    'kedaluwarsa' => 'expire',
                    'expire' => 'expire',
                    'ditolak' => 'deny',
                    'deny' => 'deny',
                    'dibatalkan' => 'cancel',
                    'cancel' => 'cancel',
                    'gagal' => 'gagal',
                ];

                foreach ($peta as $label => $nilai) {
                    if (str_contains($kata, $label)) {
                        $query->where('status', $nilai);

                        return;
                    }
                }

                $query->whereRaw('1 = 0');
            })
            ->rawColumns(['channel_label', 'status_label', 'transaksi_label'])
            ->toJson();
    }

    private function donatur(Pembayaran $pembayaran): string
    {
        if ($pembayaran->donatur_anonim) {
            return 'Hamba Allah';
        }

        return $pembayaran->nama_donatur !== null && $pembayaran->nama_donatur !== ''
            ? e($pembayaran->nama_donatur)
            : '<span class="hint">&mdash;</span>';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'settlement' => '<span class="badge-soft badge-in">Berhasil</span>',
            'pending' => '<span class="badge-soft badge-warn">Pending</span>',
            'expire' => '<span class="badge-soft badge-neutral">Kedaluwarsa</span>',
            'deny' => '<span class="badge-soft badge-out">Ditolak</span>',
            'cancel' => '<span class="badge-soft badge-neutral">Dibatalkan</span>',
            default => '<span class="badge-soft badge-out">Gagal</span>',
        };
    }

    private function transaksiLabel(Pembayaran $pembayaran): string
    {
        if (! $pembayaran->transaksi_id) {
            return '<span class="hint">&mdash;</span>';
        }

        return '<button type="button" class="btn btn-sm btn-outline-secondary btn-detail-transaksi" data-id="'.$pembayaran->transaksi_id.'" title="Lihat transaksi"><i class="bi bi-eye"></i></button>'
            .' <span class="badge-soft badge-in">Tertaut</span>';
    }

    private function tanggalValid(?string $tanggal): bool
    {
        if (! $tanggal) {
            return false;
        }

        $hasil = \DateTime::createFromFormat('Y-m-d', $tanggal);

        return $hasil !== false && $hasil->format('Y-m-d') === $tanggal;
    }
}
