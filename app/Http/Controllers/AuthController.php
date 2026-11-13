<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const BATAS_PERCOBAAN = 5;
    private const DETIK_BLOKIR = 60;

    public function create(): View
    {
        return view('pages.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $kunci = $this->kunciPembatas($data['email'], $request->ip());

        if (RateLimiter::tooManyAttempts($kunci, self::BATAS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$detik} detik.",
            ]);
        }

        // 'aktif' => true membuat akun nonaktif ditolak dengan pesan yang sama
        // seperti kata sandi salah, supaya tidak membocorkan status akun.
        $berhasil = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'aktif' => true],
            $request->boolean('remember')
        );

        if (!$berhasil) {
            RateLimiter::hit($kunci, self::DETIK_BLOKIR);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah, atau akun tidak aktif.',
            ]);
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function kunciPembatas(string $email, ?string $ip): string
    {
        return Str::transliterate(Str::lower($email) . '|' . $ip);
    }
}
