<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $sekarang = now();

        // Bersihkan dan isi ulang agar idempoten dalam pengembangan (aman jika tabel kosong/baru)
        DB::table('menu')->delete();

        $publik = [
            ['judul' => 'Beranda', 'slug' => 'publik-beranda', 'tipe' => 'route', 'route_name' => 'publik', 'url' => null, 'ikon' => null, 'area' => 'publik', 'bagian' => 'utama', 'urutan' => 10, 'peran' => 'semua'],
            ['judul' => 'Iuran Kematian', 'slug' => 'publik-iuran-kematian', 'tipe' => 'route', 'route_name' => 'iuran-kematian', 'url' => null, 'ikon' => null, 'area' => 'publik', 'bagian' => 'utama', 'urutan' => 20, 'peran' => 'semua'],
            ['judul' => 'Donasi', 'slug' => 'publik-donasi', 'tipe' => 'route', 'route_name' => 'donasi', 'url' => null, 'ikon' => null, 'area' => 'publik', 'bagian' => 'utama', 'urutan' => 30, 'peran' => 'semua'],
            ['judul' => 'Masuk bendahara', 'slug' => 'publik-login', 'tipe' => 'route', 'route_name' => 'login', 'url' => null, 'ikon' => null, 'area' => 'publik', 'bagian' => 'utama', 'urutan' => 40, 'peran' => 'tamu'],
        ];

        foreach ($publik as $m) {
            DB::table('menu')->insert(array_merge($m, [
                'induk_id' => null,
                'route_params' => null,
                'target' => '_self',
                'aktif' => true,
                'tampil_di_mobile' => true,
                'keterangan' => null,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]));
        }

        $admin = [
            ['judul' => 'Dashboard', 'slug' => 'admin-dashboard', 'tipe' => 'route', 'route_name' => 'dashboard', 'ikon' => 'bi bi-grid-1x2', 'bagian' => null, 'urutan' => 10, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Transaksi', 'slug' => 'admin-grup-transaksi', 'tipe' => 'grup', 'route_name' => null, 'ikon' => null, 'bagian' => 'transaksi', 'urutan' => 20, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => true],
            ['judul' => 'Pemasukan', 'slug' => 'admin-pemasukan', 'tipe' => 'route', 'route_name' => 'pemasukan', 'ikon' => 'bi bi-arrow-down-left-circle', 'bagian' => 'transaksi', 'urutan' => 21, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Pengeluaran', 'slug' => 'admin-pengeluaran', 'tipe' => 'route', 'route_name' => 'pengeluaran', 'ikon' => 'bi bi-arrow-up-right-circle', 'bagian' => 'transaksi', 'urutan' => 22, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Daftar transaksi', 'slug' => 'admin-transaksi', 'tipe' => 'route', 'route_name' => 'transaksi', 'ikon' => 'bi bi-list-ul', 'bagian' => 'transaksi', 'urutan' => 23, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Pembayaran online', 'slug' => 'admin-pembayaran', 'tipe' => 'route', 'route_name' => 'pembayaran', 'ikon' => 'bi bi-credit-card', 'bagian' => 'transaksi', 'urutan' => 24, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Mutasi dana', 'slug' => 'admin-mutasi-dana', 'tipe' => 'route', 'route_name' => 'mutasi-dana', 'ikon' => 'bi bi-arrow-left-right', 'bagian' => 'transaksi', 'urutan' => 25, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],

            ['judul' => 'Kegiatan dan laporan', 'slug' => 'admin-grup-kegiatan', 'tipe' => 'grup', 'route_name' => null, 'ikon' => null, 'bagian' => 'kegiatan', 'urutan' => 30, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => true],
            ['judul' => 'Acara sedekah bumi', 'slug' => 'admin-acara', 'tipe' => 'route', 'route_name' => 'acara', 'ikon' => 'bi bi-calendar-event', 'bagian' => 'kegiatan', 'urutan' => 31, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Laporan dan ekspor', 'slug' => 'admin-laporan', 'tipe' => 'route', 'route_name' => 'laporan', 'ikon' => 'bi bi-file-earmark-bar-graph', 'bagian' => 'kegiatan', 'urutan' => 32, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],

            ['judul' => 'Pengelolaan', 'slug' => 'admin-grup-pengelolaan', 'tipe' => 'grup', 'route_name' => null, 'ikon' => null, 'bagian' => 'pengelolaan', 'urutan' => 40, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => true],
            ['judul' => 'Master data', 'slug' => 'admin-master', 'tipe' => 'route', 'route_name' => 'master-wilayah', 'ikon' => 'bi bi-database', 'bagian' => 'pengelolaan', 'urutan' => 41, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false, 'has_children' => true],
            ['judul' => 'Pengaturan', 'slug' => 'admin-pengaturan', 'tipe' => 'route', 'route_name' => 'pengaturan', 'ikon' => 'bi bi-gear', 'bagian' => 'pengelolaan', 'urutan' => 50, 'peran' => 'admin', 'induk' => null, 'is_grup' => false],
            ['judul' => 'Log audit', 'slug' => 'admin-log-audit', 'tipe' => 'route', 'route_name' => 'log-audit', 'ikon' => 'bi bi-shield-check', 'bagian' => 'pengelolaan', 'urutan' => 51, 'peran' => 'admin,bendahara', 'induk' => null, 'is_grup' => false],
        ];

        $idMap = [];
        foreach ($admin as $m) {
            $hasChildren = !empty($m['has_children']);
            unset($m['is_grup'], $m['induk'], $m['has_children']);
            $id = DB::table('menu')->insertGetId(array_merge($m, [
                'induk_id' => null,
                'url' => null,
                'route_params' => null,
                'target' => '_self',
                'area' => 'admin',
                'aktif' => true,
                'tampil_di_mobile' => true,
                'keterangan' => null,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]));
            $idMap[$m['slug']] = $id;
            if ($hasChildren) {
                $anak = [
                    ['judul' => 'Wilayah RW/RT', 'slug' => 'admin-master-wilayah', 'route_name' => 'master-wilayah', 'urutan' => 1],
                    ['judul' => 'Pos dana', 'slug' => 'admin-master-pos-dana', 'route_name' => 'master-pos-dana', 'urutan' => 2],
                    ['judul' => 'Kategori', 'slug' => 'admin-master-kategori', 'route_name' => 'master-kategori', 'urutan' => 3],
                ];
                foreach ($anak as $a) {
                    DB::table('menu')->insert([
                        'induk_id' => $id,
                        'judul' => $a['judul'],
                        'slug' => $a['slug'],
                        'tipe' => 'route',
                        'route_name' => $a['route_name'],
                        'url' => null,
                        'route_params' => null,
                        'ikon' => null,
                        'target' => '_self',
                        'area' => 'admin',
                        'bagian' => 'pengelolaan',
                        'urutan' => $a['urutan'],
                        'peran' => 'admin,bendahara',
                        'aktif' => true,
                        'tampil_di_mobile' => true,
                        'keterangan' => null,
                        'created_at' => $sekarang,
                        'updated_at' => $sekarang,
                    ]);
                }
            }
        }
    }
}
