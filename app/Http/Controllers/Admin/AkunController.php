<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(): View
    {
        return view('pages.pengaturan', [
            'akun' => User::orderBy('name')->get(),
        ]);
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
