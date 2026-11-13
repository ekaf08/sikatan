<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(): View
    {
        return view('pages.pengaturan', [
            'akun' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['admin', 'bendahara'])],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username sudah dipakai.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai.',
            'role.in' => 'Peran tidak valid.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'password.min' => 'Kata sandi awal minimal 8 karakter.',
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password = Hash::make($data['password']);
        $user->aktif = true;
        $user->harus_ganti_password = true;
        $user->save();

        return back()->with('akun_baru', $user->name);
    }

    public function aturUlangPassword(Request $request, User $user): RedirectResponse
    {
        // Admin tidak mengatur ulang akunnya sendiri lewat tombol ini
        if ($user->is($request->user())) {
            return back()->with('galat', 'Untuk akun sendiri, gunakan menu Ganti kata sandi.');
        }

        $sementara = Str::password(10, symbols: false);

        $user->password = Hash::make($sementara);
        $user->harus_ganti_password = true;
        $user->remember_token = Str::random(60);
        $user->save();

        return back()->with([
            'password_sementara' => $sementara,
            'username_diatur_ulang' => $user->username,
        ]);
    }
}
