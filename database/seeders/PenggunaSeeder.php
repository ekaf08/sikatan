<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@babatan.test'],
            [
                'name' => 'Admin Babatan',
                'password' => Hash::make('password'),
                'peran' => 'admin',
                'username' => 'admin',
                'aktif' => true,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'bendahara@babatan.test'],
            [
                'name' => 'Bendahara Babatan',
                'password' => Hash::make('password'),
                'peran' => 'bendahara',
                'username' => 'bendahara',
                'aktif' => true,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]
        );
    }
}
