<?php

use App\Http\Controllers\Admin\AcaraController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\LogAuditController;
use App\Http\Controllers\Admin\MutasiDanaController;
use App\Http\Controllers\Admin\PemasukanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\PosDanaController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\Auth\GantiPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranPublikController;
use App\Http\Controllers\Webhook\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// Beranda publik (tanpa login)
Route::view('/', 'fo.home')->name('publik');

// Donasi publik & webhook Midtrans (tanpa login, webhook tanpa CSRF - dikecualikan di bootstrap/app.php)
Route::post('/donasi/bayar', [PembayaranPublikController::class, 'buat'])
    ->middleware('throttle:10,1')
    ->name('donasi.buat');
Route::get('/donasi/selesai', [PembayaranPublikController::class, 'selesai'])->name('donasi.selesai');
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])->name('webhook.midtrans');

Route::redirect('/login', '/masuk');

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('login.store');
    Route::view('/lupa-password', 'pages.auth.lupa-password')->name('password.lupa');
});

// Cukup login (tanpa cek peran dan tanpa paksa.ganti)
Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/ganti-password', [GantiPasswordController::class, 'edit'])->name('password.ganti');
    Route::post('/ganti-password', [GantiPasswordController::class, 'update'])->name('password.ganti.simpan');
});

// Area bendahara dan admin
Route::middleware(['auth', 'nocache', 'paksa.ganti', 'role:admin,bendahara'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan');

    Route::prefix('pemasukan')->name('pemasukan.')->group(function () {
        Route::get('/statistik', [PemasukanController::class, 'statistik'])->name('statistik');
        Route::get('/data', [PemasukanController::class, 'data'])->name('data');
        Route::post('/', [PemasukanController::class, 'simpan'])->name('simpan');
        Route::put('/{pemasukan}', [PemasukanController::class, 'ubah'])->name('update');
        Route::delete('/{pemasukan}', [PemasukanController::class, 'hapus'])->name('destroy');
    });

    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran');

    Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
        Route::get('/statistik', [PengeluaranController::class, 'statistik'])->name('statistik');
        Route::get('/data', [PengeluaranController::class, 'data'])->name('data');
        Route::post('/', [PengeluaranController::class, 'simpan'])->name('simpan');
        Route::put('/{pengeluaran}', [PengeluaranController::class, 'ubah'])->name('update');
        Route::delete('/{pengeluaran}', [PengeluaranController::class, 'hapus'])->name('destroy');
    });
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');

    Route::get('/transaksi/data', [TransaksiController::class, 'data'])->name('transaksi.data');
    Route::get('/transaksi/{id}/detail', [TransaksiController::class, 'detail'])->name('transaksi.detail');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran');
    Route::get('/pembayaran/data', [PembayaranController::class, 'data'])->name('pembayaran.data');

    Route::get('/mutasi-dana', [MutasiDanaController::class, 'index'])->name('mutasi-dana');

    Route::prefix('mutasi-dana')->name('mutasi-dana.')->group(function () {
        Route::get('/data', [MutasiDanaController::class, 'data'])->name('data');
        Route::get('/saldo', [MutasiDanaController::class, 'saldo'])->name('saldo');
        Route::get('/statistik', [MutasiDanaController::class, 'statistik'])->name('statistik');
        Route::post('/', [MutasiDanaController::class, 'simpan'])->name('simpan');
        Route::put('/{mutasi}', [MutasiDanaController::class, 'ubah'])->name('update');
        Route::delete('/{mutasi}', [MutasiDanaController::class, 'hapus'])->name('destroy');
    });

    Route::get('/acara', [AcaraController::class, 'index'])->name('acara');

    Route::prefix('acara')->name('acara.')->group(function () {
        Route::post('/', [AcaraController::class, 'simpan'])->name('simpan');
        Route::put('/{acara}', [AcaraController::class, 'ubah'])->name('update');
        Route::delete('/{acara}', [AcaraController::class, 'hapus'])->name('destroy');
        Route::post('/{acara}/anggaran', [AcaraController::class, 'anggaranSimpan'])->name('anggaran.simpan');
        Route::put('/{acara}/anggaran/{anggaran}', [AcaraController::class, 'anggaranUbah'])->name('anggaran.update');
        Route::delete('/{acara}/anggaran/{anggaran}', [AcaraController::class, 'anggaranHapus'])->name('anggaran.destroy');
    });

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/data', [LaporanController::class, 'data'])->name('data');
        Route::get('/ekspor/pdf', [LaporanController::class, 'eksporPdf'])->name('pdf');
        Route::get('/ekspor/excel', [LaporanController::class, 'eksporExcel'])->name('excel');
    });

    Route::get('/log-audit', [LogAuditController::class, 'index'])->name('log-audit');

    Route::get('/log-audit/data', [LogAuditController::class, 'data'])->name('log-audit.data');

    Route::get('/master/wilayah', [WilayahController::class, 'index'])->name('master-wilayah');

    Route::prefix('master/wilayah')->name('wilayah.')->group(function () {
        Route::get('/statistik', [WilayahController::class, 'statistik'])->name('statistik');

        Route::get('/rw/opsi', [WilayahController::class, 'opsiRw'])->name('rw.opsi');
        Route::get('/rw/data', [WilayahController::class, 'dataRw'])->name('rw.data');
        Route::post('/rw', [WilayahController::class, 'simpanRw'])->name('rw.store');
        Route::put('/rw/{rw}', [WilayahController::class, 'ubahRw'])->name('rw.update');
        Route::delete('/rw/{rw}', [WilayahController::class, 'hapusRw'])->name('rw.destroy');

        Route::get('/rt/data', [WilayahController::class, 'dataRt'])->name('rt.data');
        Route::post('/rt', [WilayahController::class, 'simpanRt'])->name('rt.store');
        Route::put('/rt/{rt}', [WilayahController::class, 'ubahRt'])->name('rt.update');
        Route::delete('/rt/{rt}', [WilayahController::class, 'hapusRt'])->name('rt.destroy');
    });

    Route::get('/master/pos-dana', [PosDanaController::class, 'index'])->name('master-pos-dana');

    Route::prefix('master/pos-dana')->name('pos-dana.')->group(function () {
        Route::get('/statistik', [PosDanaController::class, 'statistik'])->name('statistik');
        Route::get('/data', [PosDanaController::class, 'data'])->name('data');
        Route::post('/', [PosDanaController::class, 'simpan'])->name('simpan');
        Route::put('/{posDana}', [PosDanaController::class, 'ubah'])->name('update');
        Route::delete('/{posDana}', [PosDanaController::class, 'hapus'])->name('destroy');
    });

    Route::get('/master/kategori', [KategoriController::class, 'index'])->name('master-kategori');

    Route::prefix('master/kategori')->name('kategori.')->group(function () {
        Route::get('/statistik', [KategoriController::class, 'statistik'])->name('statistik');
        Route::get('/data', [KategoriController::class, 'data'])->name('data');
        Route::post('/', [KategoriController::class, 'simpan'])->name('simpan');
        Route::put('/{kategori}', [KategoriController::class, 'ubah'])->name('update');
        Route::delete('/{kategori}', [KategoriController::class, 'hapus'])->name('destroy');
    });
});

// Khusus admin
Route::middleware(['auth', 'nocache', 'paksa.ganti', 'role:admin'])->group(function () {
    Route::get('/pengaturan', [AkunController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan', [AkunController::class, 'simpanPengaturan'])->name('pengaturan.simpan');

    // Group khusus untuk manajemen akun
    Route::prefix('pengaturan/akun')->name('akun.')->group(function () {
        Route::post('/', [AkunController::class, 'store'])->name('simpan');
        Route::put('/{user}', [AkunController::class, 'update'])->name('update');
        Route::delete('/{user}', [AkunController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/atur-ulang-password', [AkunController::class, 'aturUlangPassword'])->name('atur-ulang-password');
    });
});
