<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class GantiPasswordController extends Controller
{
    public function edit(Request $request): View
    {
        $pengguna = $request->user();
        $wajib = $pengguna->harus_ganti_password;
        $bolehGantiLain = $pengguna->adalahAdmin() && !$wajib;

        return view('pages.auth.ganti-password', [
            'wajib' => $wajib,
            'daftarPengguna' => $bolehGantiLain ? User::orderBy('name')->get() : collect(),
            'terpilih' => (int) $request->query('user'),
        ]);
    }

    // permintaan user untuk passowrd agar freetext 
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        $pengguna = $request->user();
        $targetId = (int) $request->input('user_id');
        $menggantiLain = $targetId > 0 && $targetId !== $pengguna->id;

        if ($menggantiLain) {
            abort_unless(
                $pengguna->adalahAdmin() && !$pengguna->harus_ganti_password,
                403,
                'Hanya admin yang dapat mengganti kata sandi pengguna lain.'
            );

            $target = User::findOrFail($targetId);
        } else {
            $target = $pengguna;
        }

        $aturan = [
            // KODE LAMA: Menggunakan validasi huruf dan angka
            // 'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],

            // KODE BARU: Hanya wajib 8 karakter, tanpa validasi huruf besar/kecil/angka/simbol
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        $pesan = [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sama.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',

            // CATATAN: Pesan di bawah ini bisa dibiarkan saja (tidak akan error), 
            // atau bisa Anda hapus/hapus komentarnya jika nanti validasi huruf/angka diaktifkan kembali.
            // 'password.letters' => 'Kata sandi harus mengandung minimal satu huruf.',
            // 'password.numbers' => 'Kata sandi harus mengandung minimal satu angka.',
        ];

        if (!$menggantiLain) {
            $aturan['password_lama'] = ['required', 'current_password'];
            $aturan['password'][] = 'different:password_lama';

            $pesan['password_lama.required'] = 'Kata sandi saat ini wajib diisi.';
            $pesan['password_lama.current_password'] = 'Kata sandi saat ini salah.';
            $pesan['password.different'] = 'Kata sandi baru tidak boleh sama dengan yang lama.';
        }

        $data = $request->validate($aturan, $pesan);

        $target->password = Hash::make($data['password']);
        $target->harus_ganti_password = false;
        $target->remember_token = Str::random(60);
        $target->save();

        if ($target->is($pengguna)) {
            return redirect()
                ->route('dashboard')
                ->with('status', 'Kata sandi berhasil diganti.');
        }

        return redirect()
            ->route('password.ganti')
            ->with('status', 'Kata sandi ' . $target->name . ' berhasil diganti.');
    }
}
