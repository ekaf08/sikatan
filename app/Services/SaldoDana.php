<?php

namespace App\Services;

use App\Models\MutasiDana;
use App\Models\PosDana;
use App\Models\Transaksi;
use Illuminate\Support\Collection;

class SaldoDana
{
    /**
     * Ambil semua pos dana beserta saldo terkini (atau saldo sampai tanggal tertentu).
     */
    public function semua(?string $sampai = null): Collection
    {
        return PosDana::query()
            ->orderBy('nama')
            ->get()
            ->each(function (PosDana $pos) use ($sampai): void {
                $pos->setAttribute('saldo_terkini', $this->hitung($pos, $sampai));
            });
    }

    /**
     * Hitung saldo sebuah pos: saldo awal + transaksi + mutasi masuk - mutasi keluar.
     *
     * @param  string|null  $sampai  Batas atas eksklusif (format Y-m-d). Null berarti sampai sekarang.
     */
    public function hitung(PosDana $pos, ?string $sampai = null): float
    {
        $saldo = (float) $pos->saldo_awal;
        $mulai = $pos->tanggal_saldo_awal?->toDateString();

        $bersihTransaksi = (float) Transaksi::query()
            ->where('pos_dana_id', $pos->id)
            ->where('status', 'aktif')
            ->when($mulai !== null, fn ($query) => $query->where('tanggal', '>=', $mulai))
            ->when($sampai !== null, fn ($query) => $query->where('tanggal', '<', $sampai))
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN nominal ELSE -nominal END), 0) as bersih")
            ->value('bersih');

        $mutasiMasuk = (float) MutasiDana::query()
            ->where('ke_pos_id', $pos->id)
            ->when($mulai !== null, fn ($query) => $query->where('tanggal', '>=', $mulai))
            ->when($sampai !== null, fn ($query) => $query->where('tanggal', '<', $sampai))
            ->sum('nominal');

        $mutasiKeluar = (float) MutasiDana::query()
            ->where('dari_pos_id', $pos->id)
            ->when($mulai !== null, fn ($query) => $query->where('tanggal', '>=', $mulai))
            ->when($sampai !== null, fn ($query) => $query->where('tanggal', '<', $sampai))
            ->sum('nominal');

        return round($saldo + $bersihTransaksi + $mutasiMasuk - $mutasiKeluar, 2);
    }
}
