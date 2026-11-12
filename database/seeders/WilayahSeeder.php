<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        // RW 1 punya 7 RT, RW 2 punya 8 RT
        $daftar = [
            ['nama' => 'RW 01', 'nama_ketua' => 'Bapak Slamet (dummy)', 'jumlah_rt' => 7],
            ['nama' => 'RW 02', 'nama_ketua' => 'Bapak Hartono (dummy)', 'jumlah_rt' => 8],
        ];

        $penarik = [
            'Pak Joko', 'Pak Budi', 'Pak Agus', 'Pak Wahyu', 'Pak Eko',
            'Pak Imam', 'Pak Darto', 'Pak Rudi', 'Pak Heri', 'Pak Sugeng',
            'Pak Anwar', 'Pak Bambang', 'Pak Teguh', 'Pak Yanto', 'Pak Mulyadi',
        ];

        $urut = 0;

        foreach ($daftar as $rw) {
            $rwId = DB::table('rw')->insertGetId([
                'nama' => $rw['nama'],
                'nama_ketua' => $rw['nama_ketua'],
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);

            for ($i = 1; $i <= $rw['jumlah_rt']; $i++) {
                DB::table('rt')->insert([
                    'rw_id' => $rwId,
                    'nomor' => str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'nama_penarik' => $penarik[$urut % count($penarik)] . ' (dummy)',
                    'jumlah_kk' => random_int(25, 50),
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ]);
                $urut++;
            }
        }
    }
}
