# Rencana & Progres Integrasi Payment Gateway Midtrans (SIKATAN)

Terakhir diperbarui: **2026-11-16**

Dokumen ini dibuat agar pekerjaan dapat dilanjutkan tanpa membaca ulang konteks awal.

## Riwayat pekerjaan (terbaru di atas)

### 2026-11-16 (lanjutan) — Rekap pembayaran, pengujian otomatis, hardening
- [x] **Rekap pembayaran online untuk bendahara/admin** (read-only):
  - `app/Http/Controllers/Admin/PembayaranController.php` (index + data server-side DataTables, filter status & rentang tanggal).
  - `resources/views/pages/pembayaran.blade.php` (tabel order, donatur, channel, nominal, status, tombol detail transaksi).
  - Route `GET /pembayaran` & `GET /pembayaran/data` (`pembayaran`, `pembayaran.data`) di area admin/bendahara.
  - Menu **Pembayaran online** di `resources/views/layouts/sidebar.blade.php`.
- [x] **Pengujian otomatis** (tanpa kredensial Midtrans, memakai signature lokal):
  - `tests/Feature/MidtransWebhookTest.php`: signature invalid (403), settlement membuat transaksi + tautan, notifikasi duplikat tidak menggandakan transaksi/audit, status `pending`/`expire` tidak membuat transaksi, nominal tidak cocok → `gagal`, master donasi hilang → kegagalan tercatat tanpa transaksi.
  - `tests/Feature/PembayaranAdminTest.php`: bendahara dapat membuka halaman & endpoint data rekap pembayaran.
  - Hasil `php artisan test`: **10 tes lulus, 48 assertion**.
- [x] **Hardening**:
  - Rate limit `POST /donasi/bayar` → `throttle:10,1`.
  - Validasi nominal maksimal Rp100.000.000 + pesan validasi.
  - `expired_at` pembayaran diisi `now()+1 hari` saat order dibuat.
  - Kegagalan auto-posting kini dicatat di luar transaksi utama (meta & log tidak ikut ter-rollback).
  - Perbaikan deprecation `?string $channel = null`.
- [x] **Dependensi terpasang**: `composer install` dijalankan, `midtrans/midtrans-php` v2.6.2 ada di `vendor/` dan autoload.
- [ ] **Validasi sandbox end-to-end** dengan kredensial Midtrans asli (sukses/pending/expire/deny/cancel, notifikasi duplikat, cek saldo/laporan) — menunggu `MIDTRANS_*` sandbox.

### 2026-11-16 11.03 — Implementasi awal integrasi Midtrans
- [x] Analisis konteks kodebase (transaksi, pemasukan, saldo, audit, routes, home publik).
- [x] Konfigurasi dasar: `composer.json`, `.env.example`, `config/services.php`, exclude CSRF `webhook/midtrans`.
- [x] Skema & model `pembayaran` (`database/migrations/2026_11_16_110313_buat_tabel_pembayaran.php`, `app/Models/Pembayaran.php`).
- [x] Service gateway (`MidtransGateway`) + auto-posting idempotent (`PencatatPembayaranSukses`).
- [x] Controller & routes publik + webhook (`PembayaranPublikController`, `Webhook\MidtransWebhookController`).
- [x] UI donasi publik (`fo.home`) & halaman status (`fo.donasi-selesai`).
- [x] Seeder user sistem `sistem@babatan.test` (`PenggunaSeeder`).

## Keputusan (disepakati)
- **Use case:** Donasi **publik** (tanpa login).
- **Provider:** **Midtrans Snap** (QRIS/VA/e-wallet/kartu via Snap, sandbox, webhook notifikasi + verifikasi signature).
- **Posting kas:** Jika pembayaran **berhasil/settlement/capture** (terverifikasi via webhook server-to-server) → **auto-posting** membuat `Transaksi` jenis `masuk` (sekali, idempotent) dan menautkan `pembayaran.transaksi_id`.
- **Pembatalan:** Pembayaran gagal/expire/deny/cancel dari gateway **tidak** membuat transaksi. Dana yang **sudah masuk kas** hanya bisa dibatalkan oleh **bendahara/admin** melalui mekanisme batal transaksi yang ada (`status=batal`/soft-delete + audit) agar saldo (`App\Services\SaldoDana`) tetap sinkron. Tidak ada auto-refund/auto-reversal dari notifikasi gateway.
- **Sumber kebenaran status bayar:** webhook (signature + kecocokan order/amount), **bukan** halaman finish/return (UX saja).

## Arsitektur alur
1. Publik isi form donasi → `POST /donasi/bayar` membuat record `pembayaran` (`pending`, `order_id` unik) + request Snap → redirect/token bayar.
2. `POST /webhook/midtrans` (tanpa auth, exclude CSRF) verifikasi `signature_key` SHA512(`order_id+status_code+gross_amount+serverKey`) & `gross_amount` vs DB, lalu idempotent (lock + cek sudah punya `transaksi_id`/status final): sukses → buat `Transaksi` masuk & tautkan; status gagal/expire → update status `pembayaran` saja.
3. `GET /donasi/selesai` menampilkan status lokal berdasarkan `order_id` (UX).
4. Bendahara melihat rekap pembayaran (`/pembayaran`) & transaksi; pembatalan kas hanya lewat modul transaksi eksisting.

## Mapping donasi publik → transaksi (auto-posting)
- Mengikuti pola `PemasukanController` donasi: pos **Kas Masjid** (`jenis=masjid`) & kategori **Donasi Masjid** (`jenis=masuk`) — resolve seperti `resolveMeta`/konfigurasi `TIPE['donasi']`.
- Field transaksi: `jenis=masuk`, `status=aktif`, `tanggal` = waktu bayar (atau hari ini), `nominal` terverifikasi, `nama_donatur`, `donatur_anonim`, `tujuan`, `catatan_publik`, `dibuat_oleh` = akun sistem, `pos_dana_id/kategori_id` sesuai mapping.
- Audit: dicatat via `App\Services\PencatatAudit` (tambah transaksi & perubahan status pembayaran).

## Keputusan teknis / asumsi implementasi
- **`dibuat_oleh` (wajib, FK users):** user sistem `sistem@babatan.test` (username `sistem`, name "Sistem Pembayaran", role `admin`, `aktif=false`) sebagai referensi transaksi auto-posting.
- **Idempotensi:** unique `order_id`, kolom `transaksi_id` nullable, proses webhook dalam `DB::transaction` + `lockForUpdate`; notifikasi ulang setelah settlement diabaikan.
- **Nominal:** decimal(15,2) selaras `transaksi`; `gross_amount` notifikasi dibandingkan dengan nominal order sebelum posting.
- **CSRF:** route webhook dikecualikan dari proteksi CSRF.
- **Master data:** auto-posting mensyaratkan pos/kategori donasi ada. Jika tidak ada, kegagalan dicatat di `pembayaran.meta.error_terakhir` + log, tanpa transaksi ganda/parsial.
- **Respons webhook:** error pemrosesan logika tetap dibalas `200` "Diproses dengan catatan" agar Midtrans tidak retry tak terkendali; kegagalan tersimpan untuk investigasi. Ubah ke `500` bila ingin memanfaatkan retry Midtrans.
- **Dependency:** `midtrans/midtrans-php` ^2.6 (terpasang v2.6.2).

## Status per-file
- [x] `composer.json` + `composer.lock` + `vendor/`: `midtrans/midtrans-php` terpasang.
- [x] `.env.example`: `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, `MIDTRANS_MERCHANT_ID`.
- [x] `config/services.php`: blok `midtrans`.
- [x] `bootstrap/app.php`: exclude CSRF `webhook/midtrans`.
- [x] Migrasi `pembayaran`, model `Pembayaran`.
- [x] `app/Services/Pembayaran/MidtransGateway.php`.
- [x] `app/Services/Pembayaran/PencatatPembayaranSukses.php`.
- [x] `app/Http/Controllers/PembayaranPublikController.php`.
- [x] `app/Http/Controllers/Webhook/MidtransWebhookController.php`.
- [x] `routes/web.php`: donasi publik, halaman selesai, webhook, rekap pembayaran admin.
- [x] `resources/views/fo/home.blade.php` + `fo/donasi-selesai.blade.php`.
- [x] `database/seeders/PenggunaSeeder.php`: user sistem.
- [x] `app/Http/Controllers/Admin/PembayaranController.php` + `resources/views/pages/pembayaran.blade.php` + menu sidebar.
- [x] `tests/Feature/MidtransWebhookTest.php` + `tests/Feature/PembayaranAdminTest.php`.

## Yang belum selesai
- [ ] **Validasi sandbox end-to-end** (butuh kredensial Midtrans asli):
  - Isi `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false`.
  - `php artisan migrate` & `php artisan db:seed --class=PenggunaSeeder` bila DB baru.
  - Uji sukses (settlement/capture), pending, expire, deny/cancel, notifikasi duplikat.
  - Pastikan webhook dapat diakses publik (tunnel) ke `/webhook/midtrans`; cek transaksi + saldo/laporan.
- [ ] Konfirmasi keputusan bisnis yang masih terbuka (lihat bagian di bawah).

## Keputusan yang masih perlu dikunci
1. Akun `dibuat_oleh` transaksi otomatis: user "Sistem" (`aktif=false`) — konfirmasi role/aktif.
2. Field donasi publik wajib: nama/kontak (email/WA)/tujuan/catatan? nominal bebas (saat ini min Rp1.000, maks Rp100.000.000) atau paket?
3. Channel Snap yang diaktifkan (default semua vs dibatasi QRIS/VA).
4. Perilaku jika master pos/kategori donasi belum ada saat webhook sukses (saat ini: gagal tercatat di meta/log, tanpa transaksi; bisa diubah agar Midtrans retry dengan balasan 500).
5. Perlu kirim bukti/tautan status ke donatur (butuh mail/WA) atau cukup halaman selesai.

## Catatan penting saat melanjutkan
- Jangan pernah buat `Transaksi` dari redirect/finish; hanya dari webhook terverifikasi.
- Pastikan idempotensi & lock untuk cegah double-credit pada retry notifikasi.
- Bandingkan nominal notifikasi dengan nominal order di DB sebelum posting.
- `SaldoDana` hanya menghitung transaksi `status=aktif` & non-deleted—posting sukses harus menghasilkan transaksi aktif valid.
- Cek kembali `PemasukanController` (resolveMeta/kategori-pos donasi) agar mapping auto-post konsisten.
- Jalankan `php artisan test` sebelum deploy (mencakup webhook & rekap pembayaran).
