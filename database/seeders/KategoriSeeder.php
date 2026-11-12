<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        $kategori = [
            // pemasukan
            ['jenis' => 'masuk',  'nama' => 'Iuran Kematian',        'nominal_standar' => 5000,    'sensitif' => false],
            ['jenis' => 'masuk',  'nama' => 'Uang Kotak Jumat',      'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'masuk',  'nama' => 'Donasi Masjid',         'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'masuk',  'nama' => 'Iuran Sedekah Bumi',    'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'masuk',  'nama' => 'Pemasukan Lain-lain',   'nominal_standar' => null,    'sensitif' => false],

            // pengeluaran
            ['jenis' => 'keluar', 'nama' => 'Santunan Kematian',     'nominal_standar' => 2000000, 'sensitif' => true],
            ['jenis' => 'keluar', 'nama' => 'Perawatan Masjid',      'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'keluar', 'nama' => 'Lampu Jalan',           'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'keluar', 'nama' => 'Konsumsi Sedekah Bumi', 'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'keluar', 'nama' => 'Sewa dan Hiburan',      'nominal_standar' => null,    'sensitif' => false],
            ['jenis' => 'keluar', 'nama' => 'Pengeluaran Lain-lain', 'nominal_standar' => null,    'sensitif' => false],
        ];

        foreach ($kategori as $k) {
            DB::table('kategori')->updateOrInsert(
                ['jenis' => $k['jenis'], 'nama' => $k['nama']],
                [
                    'nominal_standar' => $k['nominal_standar'],
                    'sensitif' => $k['sensitif'],
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ]
            );
        }
    }
}
