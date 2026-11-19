# To-Do List - Sistem Kampung Babatan (evolusi dari SIKATAN)

Terakhir diperbarui: **2026-11-19**

Dokumen kerja terpadu agar pekerjaan dapat dilanjutkan tanpa membaca ulang konteks awal.

## Ringkasan progres
- [x] **Midtrans donasi publik (implementasi awal + rekap admin + tes otomatis):** mayoritas kode & hardening selesai; **validasi sandbox end-to-end** tertunda (skip sementara, butuh kredensial/URL publik & keputusan bisnis).
- [x] **Perbaikan halaman publik (fo.home):** filter RW/RT/periode dihapus, data saldo/grafik/pengeluaran terhubung ke DB.
- [x] **Rebranding & menu dinamis (tahap awal):** rancangan DB, migrasi+model+seeder+service menu, render menu DB di layout publik/admin, rebranding UI utama ke "Sistem Kampung Babatan".
- [x] **Ekspor admin & perbaikan menu publik:** ekspor Excel(CSV)/PDF untuk daftar transaksi, pemasukan, pengeluaran; header publik diperbaiki ke Iuran Kematian/Donasi.
- [ ] **Modul kampung lanjutan:** pengumuman, kegiatan/lomba, rebranding aset/APP_NAME penuh, halaman publik terpisah, dsb.

---

## Riwayat pekerjaan

### 2026-11-19 - Ekspor admin (Excel/PDF) & perbaikan menu publik
- [x] **Menu publik diperbaiki:** data `menu` area `publik` masih berisi item lama (Pengeluaran/Pemasukan `#anchor`), sehingga header DB override fallback. Dijalankan `php artisan db:seed --class=MenuSeeder --force` → menu publik menjadi Beranda, `Iuran Kematian` (route `iuran-kematian`), `Donasi` (route `donasi`), Masuk bendahara.
- [x] **Ekspor daftar transaksi:** `TransaksiController@eksporExcel` (CSV UTF-8 BOM, `;`, pola `LaporanController`) & `@eksporPdf` (view cetak), reuse `terapkanFilter` (jenis/pos/status/tanggal) agar sesuai filter DataTable; route `transaksi.excel/pdf`; tombol ekspor di `pages/transaksi.blade.php` mengirim filter aktif; view `pages/transaksi-cetak.blade.php`.
- [x] **Ekspor pemasukan:** `PemasukanController@eksporExcel` & `@eksporPdf` (seluruh data `jenis=masuk`, urut sama dengan daftar); route `pemasukan.excel/pdf`; tombol di header `pages/pemasukan.blade.php`; view `pages/pemasukan-cetak.blade.php`.
- [x] **Ekspor pengeluaran:** `PengeluaranController@eksporExcel` & `@eksporPdf` (seluruh data `jenis=keluar`); route `pengeluaran.excel/pdf`; tombol di header `pages/pengeluaran.blade.php`; view `pages/pengeluaran-cetak.blade.php`.
- [x] Lint: `php -l` lulus untuk controller & routes yang diubah.
- [ ] Catatan: format Excel berupa **CSV** (tanpa dependensi baru) seperti laporan; pemasukan/pengeluaran belum punya UI filter sehingga ekspor mencakup seluruh data; ekspor transaksi mengikuti filter form (bukan teks pencarian/sort client DataTables).

### 2026-11-18 - Rebranding awal, menu dinamis, & beranda publik DB
- [x] Rancangan database & roadmap: `docs/rancangan-database-sistem-kampung-babatan.md`.
- [x] Migrasi `menu` (`database/migrations/2026_11_18_111303_buat_tabel_menu.php`) & model `App\Models\Menu` (`app/Models/Menu.php`).
- [x] **Migrasi dijalankan** di workspace ini (`php artisan migrate`): tabel `menu` dibuat.
- [x] Seeder menu awal publik & admin: `database/seeders/MenuSeeder.php` (22 item), didaftarkan di `DatabaseSeeder`, **dijalankan** (`php artisan db:seed --class=MenuSeeder`).
- [x] Service render menu: `app/Services/MenuLayanan.php` (ambil per area, filter aktif & peran/tamu/auth, resolve href/active, fallback koleksi kosong jika tabel belum siap).
- [x] Layout **admin** (`resources/views/layouts/sidebar.blade.php`) & **publik** (`resources/views/fo/home.blade.php`, `resources/views/fo/donasi-selesai.blade.php`) membaca menu dari DB dengan fallback hardcode.
- [x] Rebranding UI utama: judul/brand/layout publik & admin, footer, label saldo, dan judul halaman di `resources/views` (termasuk auth/master/laporan/cetak) ke "Sistem Kampung Babatan"; `.env.example` `APP_NAME` diperbarui.
- [x] Beranda publik (`fo.home`): filter RW/RT/periode dihapus, data dari DB via `BerandaPublikController@index` + `SaldoDana` (saldo/pos, pemasukan 6 bulan, 30 pengeluaran terbaru aktif, keterangan publik & lampiran non-tersembunyi).
- [x] Perbaikan UX sidebar: judul brand tidak terpotong (wrap), state collapsed diterapkan awal render + `sidebar-no-transition` agar tidak blink saat pindah menu.
- [ ] Belum: modul pengumuman/kegiatan-lomba, halaman publik terpisah, paginasi/caching, rename aset `sikatan/*` & logo, set `APP_NAME` di `.env` produksi, tes/regresi lanjutan.

### 2026-11-16 - Midtrans & rekap pembayaran
- [x] Implementasi awal integrasi Midtrans (donasi publik, webhook, auto-posting idempotent), rekap pembayaran admin, pengujian otomatis, hardening, dependensi terpasang. **Sandbox end-to-end tertunda** (lihat bagian C).

---

## A. Rebranding & Perluasan: "Sistem Kampung Babatan"
**Tujuan:** berkembang dari sistem kas menjadi portal kampung (pengumuman, acara/lomba, dll.) dengan navigasi dinamis.

### Selesai
- [x] Rancangan database & roadmap: `docs/rancangan-database-sistem-kampung-babatan.md`.
- [x] Tabel & model **`menu`** (dinamis, hierarki, area/peran/urutan/aktif): migrasi `database/migrations/2026_11_18_111303_buat_tabel_menu.php`, `app/Models/Menu.php`.
- [x] Migrasi `menu` dijalankan di workspace ini.
- [x] Seeder data `menu` awal (publik & admin) + registrasi di `DatabaseSeeder` (`database/seeders/MenuSeeder.php`).
- [x] Service/helper render pohon menu: `app/Services/MenuLayanan.php` (filter `aktif` & `peran`/auth, fallback aman).
- [x] Refactor layout publik & admin agar membaca menu dari DB (fallback hardcode): `layouts/sidebar.blade.php`, `fo/home.blade.php`, `fo/donasi-selesai.blade.php`.
- [x] Rebranding teks/label utama di UI ke "Sistem Kampung Babatan" (view publik/admin/auth/master/laporan, `.env.example`).

### Belum / rencana berikutnya
- [ ] Jalankan `php artisan migrate` & seeder menu di lingkungan selain workspace ini bila belum (deploy/staging).
- [ ] Cache menu (opsional) & invalidasi saat menu dikelola.
- [ ] CRUD/pengelolaan menu dinamis (admin) agar bisa tambah/edit/hapus/urut tanpa ubah kode/seeder.
- [ ] Rebranding lanjutan: pertimbangkan rename path aset `public/sikatan` & `logo-sikatan.png` (butuh update referensi luas), set `APP_NAME` di `.env` tiap environment, rapikan README bawaan Laravel.
- [ ] Modul **Pengumuman**: migrasi & model, CRUD admin, daftar/detail publik, status terbit/arsip, sanitasi konten & gambar (lihat rancangan).
- [ ] Modul **Kegiatan & Lomba**: migrasi & model, CRUD admin, daftar/detail publik, jenis (acara/lomba/rapat/dll.), jadwal & poster (lihat rancangan).
- [ ] (Keputusan) Relasi `acara` keuangan eksisting dengan entitas kegiatan baru (tautkan vs refactor).
- [ ] (Opsional/lanjut) Pendaftaran lomba, galeri, halaman statis/CMS, notifikasi/subscriber.
- [ ] Penyesuaian hak akses, validasi, audit, dan uji regresi modul kas setelah perubahan.

### Keputusan terbuka (kampung)
1. Relasi `acara` (keuangan) ↔ `kegiatan` baru.
2. Menu admin: full dinamis dari DB vs hybrid (sebagian sensitif tetap hardcode).
3. Kebutuhan & data pendaftaran lomba (privasi).
4. Kanal pengumuman (web saja / email / WA).
5. Struktur URL/slug publik (`/pengumuman`, `/kegiatan`, dll.) & penamaan/penyimpanan aset (tetap `sikatan` atau rebrand).

> Detail skema & opsi ada di `docs/rancangan-database-sistem-kampung-babatan.md`.

---

## B. Halaman Publik (fo.home) - Transparansi Kas
### Selesai
- [x] Hapus filter RW/RT/periode di beranda publik.
- [x] Sambungkan ke DB: total & saldo per pos (`SaldoDana`), grafik pemasukan 6 bulan, daftar pengeluaran terbaru (aktif) beserta keterangan publik & lampiran non-tersembunyi.
- [x] `routes/web.php` menggunakan `BerandaPublikController@index`; view dinamis `resources/views/fo/home.blade.php`; controller `app/Http/Controllers/BerandaPublikController.php`.

### Belum / bisa ditingkatkan
- [ ] Paginasi/"lihat semua" pengeluaran publik (saat ini dibatasi 30).
- [ ] Halaman publik terpisah untuk pemasukan/pengeluaran/laporan (saat ini anchor/tautan belum ke halaman khusus).
- [ ] Cache/optimasi query jika data besar.

---

## C. Payment Gateway Midtrans (Donasi Publik) - DISKIP SEMENTARA
**Status:** implementasi awal & rekap admin & tes otomatis **selesai**; **validasi sandbox end-to-end tertunda**.

### Selesai
- [x] Konfigurasi, skema/model `pembayaran`, gateway & auto-posting idempotent, controller/routes publik+webhook, UI donasi & status, seeder user sistem.
- [x] Rekap pembayaran online bendahara/admin (controller, view, route, menu sidebar).
- [x] Tes otomatis webhook & rekap admin; hardening awal (throttle, batas nominal, `expired_at`, pencatatan kegagalan).
- [x] Dependensi `midtrans/midtrans-php` terpasang.

### Belum / saat dilanjutkan
- [ ] Lengkapi env: `MIDTRANS_CLIENT_KEY` (pasangan Server Key di Settings > Access Keys, Sandbox `SB-Mid-client-...`), `MIDTRANS_IS_PRODUCTION=false` untuk sandbox; `php artisan config:clear`.
- [ ] Pastikan migrasi/seed & master Pos Dana `masjid` + Kategori masuk `Donasi Masjid` ada.
- [ ] **Validasi sandbox end-to-end**: sukses/pending/expire/deny/cancel, notifikasi duplikat, webhook via URL publik/tunnel ke `/webhook/midtrans`, cek transaksi & saldo/laporan.
- [ ] Kunci keputusan bisnis: field donasi wajib, channel Snap, perilaku bila master donasi hilang saat sukses, bukti ke donatur, konfirmasi akun `dibuat_oleh` sistem.

### Catatan penting Midtrans
- Jangan buat `Transaksi` dari redirect/finish; hanya dari webhook terverifikasi (idempotent, lock, cek nominal).
- `SaldoDana` hanya menghitung transaksi `status=aktif` & non-deleted; pembatalan dana yang sudah masuk hanya via bendahara.
- Jalankan `php artisan test` sebelum deploy.

---

## Catatan operasional umum
- Setelah ubah `.env`/config: `php artisan config:clear` (dan cache ulang bila produksi).
- Setelah menarik kode baru: `php artisan migrate` (migrasi `menu` & lainnya) dan pastikan seeder menu dijalankan bila tabel `menu` kosong.
- Modul kas adalah inti; pastikan regresi saldo/laporan dicek saat menambah modul kampung.
- Lint PHP cepat: `php -l` pada file yang diubah; tes fitur tersedia via `php artisan test`.
