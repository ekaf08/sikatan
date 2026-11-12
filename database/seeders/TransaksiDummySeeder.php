<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiDummySeeder extends Seeder
{
    private int $bendaharaId;

    public function run(): void
    {
        $this->bendaharaId = (int) DB::table('users')->where('email', 'bendahara@babatan.test')->value('id');

        $pos = DB::table('pos_dana')->pluck('id', 'jenis');
        $kategori = DB::table('kategori')->pluck('id', 'nama');
        $daftarRt = DB::table('rt')->get();
        $acaraId = (int) DB::table('acara')->orderByDesc('id')->value('id');
        $tarifIuran = (int) DB::table('pengaturan')->where('kunci', 'iuran_kematian_per_kk')->value('nilai');

        $baris = [];

        // 1. Setoran iuran kematian: tiap Minggu, per RT, 12 minggu terakhir
        $minggu = Carbon::today();
        while ($minggu->dayOfWeek !== Carbon::SUNDAY) {
            $minggu->subDay();
        }

        for ($i = 0; $i < 12; $i++) {
            $tanggal = $minggu->copy()->subWeeks($i)->toDateString();

            foreach ($daftarRt as $rt) {
                $bayar = random_int((int) floor($rt->jumlah_kk * 0.8), $rt->jumlah_kk);

                $baris[] = $this->baris([
                    'pos_dana_id' => $pos['kematian'],
                    'kategori_id' => $kategori['Iuran Kematian'],
                    'rt_id' => $rt->id,
                    'jenis' => 'masuk',
                    'tanggal' => $tanggal,
                    'nominal' => $bayar * $tarifIuran,
                    'jumlah_kk_bayar' => $bayar,
                    'catatan_publik' => 'Setoran iuran kematian',
                ]);
            }
        }

        // 2. Uang kotak masjid: tiap Jumat, 12 Jumat terakhir
        $jumat = Carbon::today();
        while ($jumat->dayOfWeek !== Carbon::FRIDAY) {
            $jumat->subDay();
        }

        for ($i = 0; $i < 12; $i++) {
            $baris[] = $this->baris([
                'pos_dana_id' => $pos['masjid'],
                'kategori_id' => $kategori['Uang Kotak Jumat'],
                'jenis' => 'masuk',
                'tanggal' => $jumat->copy()->subWeeks($i)->toDateString(),
                'nominal' => random_int(8, 25) * 50000,
                'catatan_publik' => 'Uang kotak setelah salat Jumat',
            ]);
        }

        // 3. Donasi perorangan untuk masjid
        $baris[] = $this->baris([
            'pos_dana_id' => $pos['masjid'],
            'kategori_id' => $kategori['Donasi Masjid'],
            'jenis' => 'masuk',
            'tanggal' => Carbon::today()->subDays(20)->toDateString(),
            'nominal' => 1000000,
            'nama_donatur' => 'Fulan (dummy)',
            'donatur_anonim' => false,
            'tujuan' => 'Perawatan masjid',
            'catatan_publik' => 'Donasi untuk perawatan masjid',
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['masjid'],
            'kategori_id' => $kategori['Donasi Masjid'],
            'jenis' => 'masuk',
            'tanggal' => Carbon::today()->subDays(9)->toDateString(),
            'nominal' => 500000,
            'nama_donatur' => 'Donatur Dummy',
            'donatur_anonim' => true,
            'tujuan' => 'Operasional masjid',
            'catatan_internal' => 'Donatur meminta namanya dirahasiakan',
            'catatan_publik' => 'Donasi hamba Allah',
        ]);

        // 4. Iuran sedekah bumi per RT (sekali setahun)
        foreach ($daftarRt as $rt) {
            $bayar = random_int((int) floor($rt->jumlah_kk * 0.8), $rt->jumlah_kk);

            $baris[] = $this->baris([
                'pos_dana_id' => $pos['sedekah_bumi'],
                'kategori_id' => $kategori['Iuran Sedekah Bumi'],
                'rt_id' => $rt->id,
                'jenis' => 'masuk',
                'tanggal' => Carbon::today()->subDays(40)->toDateString(),
                'nominal' => $bayar * 50000,
                'jumlah_kk_bayar' => $bayar,
                'catatan_publik' => 'Setoran iuran sedekah bumi',
            ]);
        }

        // 5. Pemasukan lain-lain
        $baris[] = $this->baris([
            'pos_dana_id' => $pos['umum'],
            'kategori_id' => $kategori['Pemasukan Lain-lain'],
            'jenis' => 'masuk',
            'tanggal' => Carbon::today()->subDays(30)->toDateString(),
            'nominal' => 750000,
            'catatan_publik' => 'Hasil sewa terop warga',
        ]);

        // 6. Pengeluaran
        $baris[] = $this->baris([
            'pos_dana_id' => $pos['kematian'],
            'kategori_id' => $kategori['Santunan Kematian'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(45)->toDateString(),
            'nominal' => 2000000,
            'catatan_internal' => 'Santunan keluarga almarhum Bpk. Dummy A, RT 03',
            'catatan_publik' => 'Santunan kematian warga',
            'sembunyikan_lampiran' => true,
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['kematian'],
            'kategori_id' => $kategori['Santunan Kematian'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(18)->toDateString(),
            'nominal' => 3500000,
            'alasan_selisih' => 'Ahli waris anak yatim piatu yang masih bersekolah',
            'catatan_internal' => 'Santunan keluarga almarhum Bpk. Dummy B, RT 05, tambahan biaya pendidikan',
            'catatan_publik' => 'Santunan kematian warga, dengan tambahan untuk kebutuhan pendidikan ahli waris',
            'sembunyikan_lampiran' => true,
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['masjid'],
            'kategori_id' => $kategori['Perawatan Masjid'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(14)->toDateString(),
            'nominal' => 350000,
            'catatan_publik' => 'Ganti kran tempat wudu yang rusak',
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['umum'],
            'kategori_id' => $kategori['Lampu Jalan'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(12)->toDateString(),
            'nominal' => 480000,
            'catatan_publik' => 'Ganti 4 lampu jalan di RW 01',
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['sedekah_bumi'],
            'kategori_id' => $kategori['Konsumsi Sedekah Bumi'],
            'acara_id' => $acaraId,
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(7)->toDateString(),
            'nominal' => 4500000,
            'catatan_publik' => 'Uang muka konsumsi sedekah bumi',
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['sedekah_bumi'],
            'kategori_id' => $kategori['Sewa dan Hiburan'],
            'acara_id' => $acaraId,
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(5)->toDateString(),
            'nominal' => 2500000,
            'catatan_publik' => 'Uang muka sewa tenda dan pengeras suara',
        ]);

        // 7. Contoh transaksi yang dibatalkan (void)
        $baris[] = $this->baris([
            'pos_dana_id' => $pos['umum'],
            'kategori_id' => $kategori['Pengeluaran Lain-lain'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(3)->toDateString(),
            'nominal' => 250000,
            'catatan_publik' => 'Pembelian alat kebersihan',
            'status' => 'batal',
            'alasan_batal' => 'Salah input, nominal seharusnya Rp125.000 (dicatat ulang)',
        ]);

        $baris[] = $this->baris([
            'pos_dana_id' => $pos['umum'],
            'kategori_id' => $kategori['Pengeluaran Lain-lain'],
            'jenis' => 'keluar',
            'tanggal' => Carbon::today()->subDays(3)->toDateString(),
            'nominal' => 125000,
            'catatan_publik' => 'Pembelian alat kebersihan',
        ]);

        foreach (array_chunk($baris, 200) as $bagian) {
            DB::table('transaksi')->insert($bagian);
        }

        // 8. Contoh mutasi antar pos dana
        DB::table('mutasi_dana')->insert([
            'dari_pos_id' => $pos['umum'],
            'ke_pos_id' => $pos['sedekah_bumi'],
            'dibuat_oleh' => $this->bendaharaId,
            'tanggal' => Carbon::today()->subDays(35)->toDateString(),
            'nominal' => 1000000,
            'keterangan' => 'Tambahan dana awal persiapan sedekah bumi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function baris(array $data): array
    {
        return array_merge([
            'pos_dana_id' => null,
            'kategori_id' => null,
            'rt_id' => null,
            'acara_id' => null,
            'dibuat_oleh' => $this->bendaharaId,
            'jenis' => 'masuk',
            'tanggal' => null,
            'nominal' => 0,
            'jumlah_kk_bayar' => null,
            'nama_donatur' => null,
            'donatur_anonim' => false,
            'tujuan' => null,
            'catatan_internal' => null,
            'catatan_publik' => null,
            'alasan_selisih' => null,
            'sembunyikan_lampiran' => false,
            'status' => 'aktif',
            'alasan_batal' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $data);
    }
}
