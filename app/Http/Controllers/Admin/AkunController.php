<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function index(): View
    {
        $pengaturan = Pengaturan::query()->pluck('nilai', 'kunci')->all();
        $pengaturan['santunan_kematian_standar'] = $pengaturan['santunan_kematian_standar']
            ?? Kategori::query()->where('nama', 'Santunan Kematian')->value('nominal_standar')
            ?? 2000000;

        return view('pages.pengaturan', [
            'akun' => User::orderBy('name')->get(),
            'pengaturan' => $pengaturan,
        ]);
    }

    public function simpanPengaturan(Request $request): RedirectResponse
    {
        $request->merge([
            'iuran_kematian_per_kk' => $this->angka($request->input('iuran_kematian_per_kk')),
            'santunan_kematian_standar' => $this->angka($request->input('santunan_kematian_standar')),
        ]);

        $data = $request->validate([
            'nama_desa' => ['required', 'string', 'max:255'],
            'kop_baris_1' => ['required', 'string', 'max:255'],
            'kop_baris_2' => ['required', 'string', 'max:255'],
            'kop_alamat' => ['required', 'string', 'max:500'],
            'nama_bendahara' => ['required', 'string', 'max:255'],
            'nama_ketua' => ['required', 'string', 'max:255'],
            'iuran_kematian_per_kk' => ['required', 'integer', 'min:1'],
            'santunan_kematian_standar' => ['required', 'integer', 'min:1'],
        ], [
            'nama_desa.required' => 'Nama desa wajib diisi.',
            'kop_baris_1.required' => 'Baris kop 1 wajib diisi.',
            'kop_baris_2.required' => 'Baris kop 2 wajib diisi.',
            'kop_alamat.required' => 'Alamat pada kop wajib diisi.',
            'nama_bendahara.required' => 'Nama bendahara wajib diisi.',
            'nama_ketua.required' => 'Nama ketua wajib diisi.',
            'iuran_kematian_per_kk.required' => 'Iuran kematian wajib diisi.',
            'iuran_kematian_per_kk.min' => 'Iuran kematian harus lebih dari nol.',
            'santunan_kematian_standar.required' => 'Santunan kematian wajib diisi.',
            'santunan_kematian_standar.min' => 'Santunan kematian harus lebih dari nol.',
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data as $kunci => $nilai) {
                Pengaturan::query()->updateOrCreate(
                    ['kunci' => $kunci],
                    ['nilai' => (string) $nilai]
                );
            }

            Kategori::query()->where('nama', 'Iuran Kematian')
                ->update(['nominal_standar' => $data['iuran_kematian_per_kk']]);
            Kategori::query()->where('nama', 'Santunan Kematian')
                ->update(['nominal_standar' => $data['santunan_kematian_standar']]);
        });

        return back()->with('sukses', 'Pengaturan berhasil disimpan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['admin', 'bendahara'])],

            // Kode lama: 'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            // Kode baru: Hanya wajib 8 karakter, bebas (freetext)
            'password' => ['required', 'string', Password::min(8)],
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

        $user = new User;
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

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'bendahara'])],
            // Password opsional saat edit (jika kosong, jangan diubah)
            'password' => ['nullable', 'string', Password::min(8)],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username sudah dipakai.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai.',
            'role.in' => 'Peran tidak valid.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        // Update password hanya jika form password diisi
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->harus_ganti_password = true; // Opsional: paksa ganti sandi jika direset admin
        }

        $user->save();

        return back()->with('sukses', 'Data akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Cegah admin menghapus akunnya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('galat', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('sukses', 'Akun berhasil dihapus.');
    }

    private function angka(mixed $nilai): int
    {
        return (int) preg_replace('/\D/', '', (string) $nilai);
    }
}
