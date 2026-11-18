# Rancangan Database - Sistem Kampung Babatan

Terakhir diperbarui: **2026-11-18**

Dokumen ini merancang evolusi aplikasi dari **SIKATAN (Sistem Kas Babatan)** menjadi **Sistem Kampung Babatan**: portal informasi dan layanan kampung (transparansi kas, pengumuman, kegiatan/lomba, dll.) dengan navigasi/menu yang dapat dikelola dinamis.

## 1. Tujuan & prinsip
- **Rebranding:** identitas utama menjadi "Sistem Kampung Babatan" (kas/keuangan tetap menjadi modul inti).
- **Modular & bertahap:** tabel/fitur baru ditambah tanpa merusak modul kas yang sudah berjalan.
- **Transparansi & audit:** data publik hanya menampilkan informasi non-sensitif; perubahan penting tercatat (audit/logika status) sesuai pola yang ada.
- **Konsistensi:** mengikuti konvensi proyek (Laravel, tabel/kolom berbahasa Indonesia campuran sesuai eksisting, soft delete bila relevan, timestamps).

## 2. Ringkasan skema saat ini (eksisting)
| Tabel | Fungsi utama |
|---|---|
| `users` | Akun pengguna (admin/bendahara/sistem), status aktif, ganti password |
| `rw`, `rt` | Wilayah |
| `pos_dana` | Pos/kantong dana & saldo awal |
| `kategori` | Kategori pemasukan/pengeluaran |
| `acara`, `anggaran_acara` | Acara sedekah bumi & rencana anggaran (terkait keuangan) |
| `transaksi` | Pencatatan pemasukan/pengeluaran kas (inti, memengaruhi saldo) |
| `lampiran` | Bukti/lampiran transaksi |
| `mutasi_dana` | Perpindahan dana antar pos |
| `pembayaran` | Pembayaran online/donasi (Midtrans) & tautan ke transaksi |
| `log_audit` | Jejak perubahan data |
| `pengaturan` | Key-value pengaturan aplikasi |

> Catatan: saldo dihitung oleh `App\Services\SaldoDana` dari saldo awal pos + transaksi aktif + mutasi; perubahan skema baru tidak boleh mengganggu perhitungan ini.

## 3. Tabel baru: Menu Dinamis (**sudah ditambahkan**)
**Tabel:** `menu`  
**Migrasi:** `database/migrations/2026_11_18_111303_buat_tabel_menu.php`  
**Model:** `app/Models/Menu.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint PK | |
| `induk_id` | FK `menu.id` nullable, nullOnDelete | Hierarki menu (sub-menu) |
| `judul` | string | Label tampilan |
| `slug` | string nullable | Identifikasi unik per area (opsional) |
| `tipe` | string(20), default `tautan` | `tautan` (URL), `route` (nama route Laravel), `grup` (heading/section) |
| `route_name` | string nullable | Nama route jika `tipe=route` |
| `url` | string nullable | URL/path jika `tipe=tautan` (mendukung anchor/#) |
| `route_params` | json nullable | Parameter route |
| `ikon` | string(100) nullable | Kelas ikon (mis. `bi bi-house`) |
| `target` | string(20), default `_self` | `_self`/`_blank` |
| `area` | string(30), default `publik` | `publik`, `admin` (bisa diperluas: `footer`, dll.) |
| `bagian` | string(50) nullable | Pengelompokan (mis. `utama`, `transaksi`, `laporan`) |
| `urutan` | unsigned int, default 0 | Urutan tampil |
| `peran` | string(100) nullable | Visibilitas: `semua`, `tamu`, `auth`, `admin`, `bendahara`, `admin,bendahara` |
| `aktif` | bool, default true | Tampil/sembunyi |
| `tampil_di_mobile` | bool, default true | Kontrol responsif |
| `keterangan` | text nullable | Catatan internal |
| timestamps, softDeletes | | |

**Index/constraint:** unik (`area`,`slug`), index (`area`,`aktif`,`urutan`) & (`induk_id`,`urutan`).  
**Relasi:** self `induk()` / `anak()`.

**Rencana pemakaian:** service/helper untuk mengambil pohon menu per `area` + filter `aktif` & `peran`/auth, lalu render di navbar publik (`fo.home`/layout publik) dan sidebar admin. Perlu seeder menu awal agar perilaku setara menu hardcode saat ini.

## 4. Rancangan modul baru (tahap berikutnya)

### 4.1 Pengumuman
Tujuan: informasi kampung (pengumuman umum, jadwal, imbauan).
**Tabel usulan:** `pengumuman`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | PK | |
| judul | string | |
| slug | string unique | URL ramah |
| ringkasan | string/text nullable | Cuplikan daftar |
| isi | longText | Konten (HTML/markdown ter-sanitasi) |
| gambar_sampul | string nullable | Path storage |
| penulis_id | FK `users` nullable | Pembuat |
| status | enum/string | `draft`, `terbit`, `arsip` |
| terbit_pada | datetime nullable | Jadwal/waktu terbit |
| berakhir_pada | datetime nullable | Opsional |
| tampil_publik | bool | Kontrol visibilitas |
| disematkan | bool | Pinned di daftar |
| dilihat | unsigned bigint default 0 | (opsional) counter |
| timestamps, softDeletes | | |

Index: (`status`,`tampil_publik`,`terbit_pada`), `slug`.

### 4.2 Kegiatan & Lomba
Tujuan: agenda kampung dan perlombaan (mis. 17-an).
**Tabel usulan:** `kegiatan`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | PK | |
| jenis | string/enum | `acara`, `lomba`, `rapat`, `kerja_bakti`, dll. |
| nama | string | |
| slug | string unique | |
| deskripsi | longText nullable | |
| lokasi | string nullable | |
| mulai_pada | datetime | |
| selesai_pada | datetime nullable | |
| status | string | `rencana`,`berlangsung`,`selesai`,`dibatalkan` |
| poster | string nullable | |
| penyelenggara | string nullable | |
| kontak_nama / kontak_hp | string nullable | |
| pendaftaran_buka, pendaftaran_tutup | datetime nullable | Jika butuh pendaftaran |
| kuota | unsigned int nullable | |
| tampil_publik | bool | |
| dibuat_oleh | FK `users` nullable | |
| timestamps, softDeletes | | |

**Tabel opsional pendukung lomba (fase lanjut):**
- `kategori_lomba` (id, kegiatan_id FK, nama, aturan/deskripsi, kuota, hadiah/keterangan, urutan)
- `pendaftaran_lomba` (id, kategori_lomba_id FK, nama_peserta/tim, perwakilan/rt_id nullable, kontak, status pendaftaran, catatan, timestamps) — perhatikan privasi data warga.

**Keterkaitan dengan `acara` (keuangan):** `acara` & `anggaran_acara` saat ini dipakai modul kas/sedekah bumi. Opsi evolusi: (a) biarkan `acara` khusus keuangan dan tautkan opsional ke `kegiatan` via kolom `kegiatan_id` nullable di `acara`, atau (b) perluas `acara` menjadi entitas umum dan refactor referensi keuangan — **butuh keputusan** sebelum migrasi perubahan.

### 4.3 Halaman statis (CMS ringan, opsional)
**Tabel usulan:** `halaman` (id, judul, slug unique, isi, status/tampil_publik, urutan, meta, timestamps, softDeletes) untuk konten seperti profil kampung, kontak, dll.

### 4.4 Dokumentasi/Galeri (opsional)
**Tabel usulan:** `galeri_album` & `galeri_foto` (album_id, judul, path, keterangan, kegiatan_id/pengumuman_id nullable, tampil_publik, urutan) — atau polimorfik sederhana.

### 4.5 Notifikasi/Subscriber (opsional, jauh)
Jika perlu broadcast pengumuman ke warga (email/WA), butuh tabel subscriber & log pengiriman; bergantung keputusan kanal.

## 5. Perubahan penamaan & UI (rencana)
- Ganti label/branding "Kas Desa Babatan / SIKATAN" → **"Sistem Kampung Babatan"** pada layout publik, admin (sidebar/topbar), judul halaman, dan metadata, tanpa mengubah makna modul kas.
- Navigasi publik & admin beralih dari hardcode ke render dari tabel `menu` (dengan fallback/seed setara menu saat ini) + filter peran.
- Halaman publik baru: daftar/detail pengumuman, daftar/detail kegiatan/lomba (setelah modul & tabelnya dibuat).

## 6. Roadmap migrasi (disarankan)
1. **(Selesai)** Tabel & model `menu` (dinamis).
2. Seeder `menu` default (publik & admin) agar kompatibel dengan menu saat ini.
3. Refactor layout publik/admin membaca menu dari DB (dengan cache & fallback).
4. Rebranding teks/logo di UI.
5. Migrasi & CRUD `pengumuman` (+ halaman publik).
6. Migrasi & CRUD `kegiatan` (jenis lomba/acara) (+ halaman publik).
7. (Opsional) Pendaftaran lomba, galeri, halaman CMS, penautan `acara` keuangan ↔ `kegiatan`.
8. Penyesuaian hak akses, audit, dan pengujian regresi modul kas.

## 7. Keputusan terbuka
- Bentuk relasi `acara` (keuangan, eksisting) dengan `kegiatan` baru: tautkan vs perluas/refactor.
- Apakah menu admin juga sepenuhnya dinamis dari DB atau tetap hybrid (bagian sensitif/berbasis peran hardcode)?
- Perlukah pendaftaran lomba online dan data apa yang dikumpulkan (privasi).
- Kanal distribusi pengumuman (web saja, atau email/WA).
- Perlukah slug/SEO & struktur URL publik (prefix `/pengumuman`, `/kegiatan`, dll.).
