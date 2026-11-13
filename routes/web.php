<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('fo.home');
});

Route::get('/login', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('login.store');
});

// Area bendahara dan admin
Route::middleware(['auth', 'nocache', 'role:admin,bendahara'])->group(function () {
    Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');
});
