<?php

namespace App\Services;

use App\Models\LogAudit;
use App\Models\User;

class PencatatAudit
{
    public function catat(
        ?User $pengguna,
        string $tabel,
        int $idData,
        string $aksi,
        ?array $nilaiLama = null,
        ?array $nilaiBaru = null,
    ): void {
        LogAudit::create([
            'pengguna_id' => $pengguna?->id,
            'nama_tabel' => $tabel,
            'id_data' => $idData,
            'aksi' => $aksi,
            'nilai_lama' => $nilaiLama,
            'nilai_baru' => $nilaiBaru,
            'waktu' => now(),
        ]);
    }
}
