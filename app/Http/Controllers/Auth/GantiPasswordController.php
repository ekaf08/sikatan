<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GantiPasswordController extends Controller
{
    public function edit(Request $request): View
    {
        return view('auth.ganti-password', [
            'wajib' => $request->user()->harus_ganti_password,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:password_lama', Password::min(8)->letters()->numbers()],
        ], [
            'password_lama.current_password' => 'Kata sandi saat ini salah.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan yang lama.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->harus_ganti_password = false;
        $user->remember_token = Str::random(60);
        $user->save();

        return redirect()->route('dashboard')->with('status', 'Kata sandi berhasil diganti.');
    }
}
