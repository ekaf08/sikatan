<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        $pengaturan = [
            'nama_desa'              => 'Desa Babatan',
            'kop_baris_1'            => 'PEMERINTAH DESA BABATAN',
            'kop_baris_2'            => 'LAPORAN KEUANGAN KAS WARGA',
            'kop_alamat'             => 'Alamat desa diisi di sini',
            'nama_bendahara'         => 'Bendahara (dummy)',
            'nama_ketua'             => 'Ketua (dummy)',
            'iuran_kematian_per_kk'  => '5000',
        ];

        foreach ($pengaturan as $kunci => $nilai) {
            DB::table('pengaturan')->updateOrInsert(
                ['kunci' => $kunci],
                ['nilai' => $nilai, 'created_at' => $sekarang, 'updated_at' => $sekarang]
            );
        }
    }
}
