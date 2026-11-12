<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            WilayahSeeder::class,
            PosDanaSeeder::class,
            KategoriSeeder::class,
            PengaturanSeeder::class,
            AcaraSeeder::class,
            TransaksiDummySeeder::class,
        ]);
    }
}
