<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcaraSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        $acaraId = DB::table('acara')->insertGetId([
            'nama' => 'Sedekah Bumi ' . $sekarang->year,
            'tahun' => $sekarang->year,
            'status' => 'berjalan',
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        $anggaran = [
            ['uraian' => 'Konsumsi',                       'nominal_anggaran' => 15000000],
            ['uraian' => 'Sewa tenda dan pengeras suara',  'nominal_anggaran' => 6000000],
            ['uraian' => 'Hiburan (karawitan/wayang)',     'nominal_anggaran' => 10000000],
            ['uraian' => 'Dokumentasi dan lain-lain',      'nominal_anggaran' => 2000000],
        ];

        foreach ($anggaran as $a) {
            DB::table('anggaran_acara')->insert([
                'acara_id' => $acaraId,
                'uraian' => $a['uraian'],
                'nominal_anggaran' => $a['nominal_anggaran'],
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);
        }
    }
}
