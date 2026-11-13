<?php

use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\Auth\GantiPasswordController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Beranda publik (tanpa login)
Route::view('/', 'fo.home')->name('publik');

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
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Sementara memakai Route::view, ganti ke controller saat logika siap.
    Route::view('/pemasukan', 'pages.pemasukan')->name('pemasukan');
    Route::view('/pengeluaran', 'pages.pengeluaran')->name('pengeluaran');
    Route::view('/transaksi', 'pages.transaksi')->name('transaksi');
    Route::view('/mutasi-dana', 'pages.mutasi-dana')->name('mutasi-dana');
    Route::view('/acara', 'pages.acara')->name('acara');
    Route::view('/laporan', 'pages.laporan')->name('laporan');
    Route::view('/log-audit', 'pages.log-audit')->name('log-audit');

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

    Route::view('/master/pos-dana', 'pages.master.pos-dana')->name('master-pos-dana');
    Route::view('/master/kategori', 'pages.master.kategori')->name('master-kategori');
});

// Khusus admin
Route::middleware(['auth', 'nocache', 'paksa.ganti', 'role:admin'])->group(function () {
    Route::get('/pengaturan', [AkunController::class, 'index'])->name('pengaturan');

    // Group khusus untuk manajemen akun
    Route::prefix('pengaturan/akun')->name('akun.')->group(function () {
        Route::post('/', [AkunController::class, 'store'])->name('simpan');
        Route::put('/{user}', [AkunController::class, 'update'])->name('update');
        Route::delete('/{user}', [AkunController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/atur-ulang-password', [AkunController::class, 'aturUlangPassword'])->name('atur-ulang-password');
    });
});
