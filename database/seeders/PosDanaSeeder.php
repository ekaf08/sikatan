<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosDanaSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();
        $tanggalAwal = $sekarang->copy()->subMonths(4)->startOfMonth()->toDateString();

        $pos = [
            ['nama' => 'Kas Kematian',     'jenis' => 'kematian',     'saldo_awal' => 12500000],
            ['nama' => 'Kas Masjid',       'jenis' => 'masjid',       'saldo_awal' => 3200000],
            ['nama' => 'Kas Sedekah Bumi', 'jenis' => 'sedekah_bumi', 'saldo_awal' => 0],
            ['nama' => 'Kas Lain-lain',    'jenis' => 'umum',         'saldo_awal' => 1500000],
        ];

        foreach ($pos as $p) {
            DB::table('pos_dana')->updateOrInsert(
                ['jenis' => $p['jenis']],
                [
                    'nama' => $p['nama'],
                    'saldo_awal' => $p['saldo_awal'],
                    'tanggal_saldo_awal' => $tanggalAwal,
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ]
            );
        }
    }
}
