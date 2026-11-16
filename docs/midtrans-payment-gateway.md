# Rencana & Progres Integrasi Payment Gateway Midtrans (SIKATAN)

Dokumen ini dibuat agar pekerjaan dapat dilanjutkan tanpa membaca ulang konteks awal.

## Keputusan (disepakati)
- **Use case:** Donasi **publik** (tanpa login).
- **Provider:** **Midtrans Snap** (dokumentasi lengkap, paling umum di Indonesia; QRIS/VA/e-wallet/kartu via Snap, sandbox, webhook notifikasi + verifikasi signature).
- **Posting kas:** Jika pembayaran **berhasil/settlement/capture** (terverifikasi via webhook server-to-server) → **auto-posting** membuat `Transaksi` jenis `masuk` (sekali, idempotent) dan menautkan `pembayaran.transaksi_id`.
- **Pembatalan:** Pembayaran gagal/expire/deny/cancel dari gateway **tidak** membuat transaksi. Dana yang **sudah masuk kas** hanya bisa dibatalkan oleh **bendahara/admin** melalui mekanisme batal transaksi yang ada (`status=batal`/soft-delete + audit) agar saldo (`App\Services\SaldoDana`) tetap sinkron. Tidak ada auto-refund/auto-reversal dari notifikasi gateway.
- **Sumber kebenaran status bayar:** webhook (signature + kecocokan order/amount), **bukan** halaman finish/return (UX saja).

## Arsitektur alur
1. Publik isi form donasi → `POST /donasi/bayar` membuat record `pembayaran` (`pending`, `order_id` unik) + request Snap → redirect/token bayar.
2. `POST /webhook/midtrans` (tanpa auth, exclude CSRF) verifikasi `signature_key` SHA512(`order_id+status_code+gross_amount+serverKey`) & `gross_amount` vs DB, lalu idempotent (lock + cek sudah punya `transaksi_id`/status final): sukses → buat `Transaksi` masuk & tautkan; status gagal/expire → update status `pembayaran` saja.
3. `GET /donasi/selesai` menampilkan status lokal berdasarkan `order_id` (UX).
4. Bendahara melihat rekap pembayaran & transaksi; pembatalan kas hanya lewat modul transaksi eksisting.

## Mapping donasi publik → transaksi (auto-posting)
- Mengikuti pola `PemasukanController` donasi: pos **Kas Masjid** (`jenis=masjid`) & kategori **Donasi Masjid** (`jenis=masuk`) — resolve seperti `resolveMeta`/konfigurasi `TIPE['donasi']`.
- Field transaksi: `jenis=masuk`, `status=aktif`, `tanggal` = waktu bayar (atau hari ini), `nominal` terverifikasi, `nama_donatur`, `donatur_anonim`, `tujuan`, `catatan_publik`, `dibuat_oleh` = **akun sistem** (lihat keputusan teknis), `pos_dana_id/kategori_id` sesuai mapping.
- Audit: catat via `App\Services\PencatatAudit` (tambah transaksi & perubahan status pembayaran).

## Keputusan teknis / asumsi implementasi
- **`dibuat_oleh` (wajib, FK users):** gunakan/seed user sistem `sistem@babatan.test` (username `sistem`, name "Sistem Pembayaran", role `admin`/non-aktif login opsional sesuai kebutuhan) dan jadikan referensi tetap untuk transaksi auto-posting. *Perlu konfirmasi akhir apakah role/aktifitas user ini sudah sesuai kebijakan.*
- **Idempotensi:** unique `order_id`, kolom `transaksi_id` nullable, proses webhook dalam `DB::transaction` + `lockForUpdate`, abaikan jika sudah settlement & bertaut transaksi.
- **Nominal:** simpan & verifikasi sebagai angka yang konsisten (decimal(15,2) selaras `transaksi`); bandingkan `gross_amount` notifikasi dengan nominal order.
- **CSRF:** route webhook dikecualikan dari proteksi CSRF.
- **Master data:** auto-posting mensyaratkan pos/kategori donasi ada (seperti sekarang). Jika tidak ada, webhook harus mencatat kegagalan pemrosesan (log/status) tanpa membuat transaksi ganda/parsial. *Perlu dikonfirmasi perilaku fallback.*
- **Dependency:** `midtrans/midtrans-php` (composer) untuk Snap & util signature/config.

## Perubahan (status per-file)
- [x] `composer.json`: tambah `midtrans/midtrans-php` ^2.6. **`composer.lock` belum diupdate** (jalankan `composer update midtrans/midtrans-php` / `composer install`).
- [x] `.env.example`: `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, `MIDTRANS_MERCHANT_ID` (opsional).
- [x] `config/services.php`: blok `midtrans`.
- [x] `bootstrap/app.php`: exclude CSRF untuk `webhook/midtrans` via `validateCsrfTokens`.
- [x] Migrasi `database/migrations/2026_11_16_110313_buat_tabel_pembayaran.php`: tabel `pembayaran`.
- [x] `app/Models/Pembayaran.php`: model + relasi `transaksi()`, casts.
- [x] `app/Services/Pembayaran/MidtransGateway.php`: config Snap, create transaction, verifikasi signature, mapping status.
- [x] `app/Services/Pembayaran/PencatatPembayaranSukses.php`: idempotent auto-posting donasi → `Transaksi` (pos/kategori Donasi Masjid/Kas Masjid) + audit + tautan pembayaran; update status non-sukses.
- [x] `app/Http/Controllers/PembayaranPublikController.php`: buat pembayaran (JSON/redirect) + halaman selesai.
- [x] `app/Http/Controllers/Webhook/MidtransWebhookController.php`: verifikasi signature & proses notifikasi.
- [x] `routes/web.php`: route publik `POST /donasi/bayar`, `GET /donasi/selesai`, webhook `POST /webhook/midtrans`.
- [x] `resources/views/fo/home.blade.php`: form donasi publik + integrasi Snap JS.
- [x] `resources/views/fo/donasi-selesai.blade.php`: halaman status lokal.
- [x] `database/seeders/PenggunaSeeder.php`: user sistem `sistem@babatan.test` (username `sistem`, `aktif=false`, password random) untuk `dibuat_oleh`.
- [ ] (Opsional) Rekap pembayaran di area bendahara/admin (daftar status, channel, order, link transaksi) — **belum dibuat**.
- [ ] Tes/validasi sandbox: sukses (settlement/capture), pending, expire, deny/cancel, notifikasi duplikat; verifikasi saldo/laporan.

## Progres implementasi
- [x] Analisis konteks kodebase (transaksi, pemasukan, saldo, audit, routes, home publik).
- [x] Dokumen rencana/progres ini dibuat & diperbarui.
- [x] Konfigurasi dasar (env example, services, CSRF, composer.json) — *composer install/lock tertunda*.
- [x] Skema & model (`pembayaran`).
- [x] Service gateway + auto-posting idempotent (implementasi awal).
- [x] Controller & routes (publik + webhook). Admin rekap opsional belum.
- [x] UI donasi publik & halaman selesai (di `fo.home`).
- [x] Seeder user sistem & mapping donasi (asumsi master Donasi Masjid/Kas Masjid ada).
- [ ] Verifikasi end-to-end (sandbox), hardening & penyesuaian final.

## Langkah untuk melanjutkan (besok)
1. **Install dependensi & siapkan env**
   - `composer install` (atau `composer update midtrans/midtrans-php`) agar `midtrans/midtrans-php` terpasang & `composer.lock` konsisten.
   - Salin/env: isi `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false` (sandbox).
2. **Migrasi & seed**
   - `php artisan migrate`
   - `php artisan db:seed --class=PenggunaSeeder` (atau seeder yang memastikan user `sistem@babatan.test` ada). Pastikan master **Pos Dana jenis `masjid`** dan **Kategori masuk `Donasi Masjid`** tersedia (sudah ada di seeder pos/kategori).
3. **Uji alur**
   - Buka beranda `/#donasi`, buat donasi nominal uji, bayar via Snap sandbox.
   - Pastikan webhook dapat diakses publik (butuh URL publik/tunnel ke `/webhook/midtrans`) dan notifikasi sukses membuat 1 `transaksi` masuk + set `pembayaran.transaksi_id` (idempotent saat retry).
   - Cek halaman `/donasi/selesai?order_id=...`, rekap transaksi/pemasukan & saldo.
4. **(Opsional) Fitur lanjutan**
   - Rekap/monitoring `pembayaran` untuk bendahara (read-only) + tautan ke transaksi.
   - Notifikasi/bukti ke donatur (email/WA) jika kontak diisi.
   - Hardening: rate limit `donasi/bayar`, validasi nominal min/max kebijakan, kebijakan saat master donasi hilang, penanganan status `challenge`, logging/alerting, dan review response code webhook untuk retry kegagalan sementara.
5. **Keputusan yang masih perlu dikunci**: lihat bagian "Hal yang masih perlu konfirmasi/diputuskan" di bawah (beberapa sudah diasumsikan di implementasi awal).

## Hal yang masih perlu konfirmasi/diputuskan
1. Akun `dibuat_oleh` transaksi otomatis: seed user "Sistem" (asumsi saat ini) — konfirmasi role/aktif boleh login?
2. Field donasi publik wajib: nama/kontak (email/WA)/tujuan/catatan? nominal bebas atau paket?
3. Channel Snap yang diaktifkan (default semua vs dibatasi QRIS/VA).
4. Perilaku jika master pos/kategori donasi belum ada saat webhook sukses (gagal tercatat vs fallback).
5. Perlu kirim bukti/tautan status ke donatur (butuh mail/WA) atau cukup halaman selesai.

## Catatan penting saat melanjutkan
- Jangan pernah buat `Transaksi` dari redirect/finish; hanya dari webhook terverifikasi.
- Pastikan idempotensi & lock untuk cegah double-credit pada retry notifikasi.
- Bandingkan nominal notifikasi dengan nominal order di DB sebelum posting.
- `SaldoDana` hanya menghitung transaksi `status=aktif` & non-deleted sesuai logika saat ini—posting sukses harus menghasilkan transaksi aktif valid.
- Cek kembali `PemasukanController` (resolveMeta/kategori-pos donasi) agar mapping auto-post konsisten.
- Setelah implementasi, uji dengan simulator/notifikasi Midtrans sandbox dan skenario duplikat.
