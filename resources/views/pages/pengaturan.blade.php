@extends('layouts.app')

@section('title', 'Pengaturan — Kas Desa Babatan')

@section('topbarTitle', 'Pengaturan')
@section('topbarSubtitle', 'Kop, penandatangan, akun')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-subtitle">Kop laporan, penandatangan, nominal standar, dan akun pengguna.</p>
    </div>

    @if (session('password_sementara'))
        <div class="notice notice-green mb-3">
            Kata sandi sementara untuk <strong>{{ session('username_diatur_ulang') }}</strong>:
            <strong>{{ session('password_sementara') }}</strong>. Salin sekarang, kata sandi ini tidak ditampilkan lagi.
        </div>
    @endif

    @if (session('akun_baru'))
        <div class="notice notice-green mb-3">
            Akun <strong>{{ session('akun_baru') }}</strong> berhasil dibuat. Pengguna akan diminta mengganti kata sandi
            saat pertama kali masuk.
        </div>
    @endif

    @if (session('galat'))
        <div class="notice notice-amber mb-3">{{ session('galat') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice notice-amber mb-3">
            <strong>Akun belum bisa dibuat:</strong>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <form class="card h-100" data-demo-form data-demo-message="Pengaturan disimpan (contoh)."
                onsubmit="return false">
                <div class="card-body d-flex flex-column gap-3">
                    <h2 class="section-title">Kop laporan</h2>
                    <div>
                        <label class="form-label-app" for="setNamaDesa">Nama desa</label>
                        <input class="form-control" id="setNamaDesa" type="text" value="Desa Babatan">
                    </div>
                    <div>
                        <label class="form-label-app" for="setKop1">Baris kop 1</label>
                        <input class="form-control" id="setKop1" type="text" value="PEMERINTAH DESA BABATAN">
                    </div>
                    <div>
                        <label class="form-label-app" for="setKop2">Baris kop 2</label>
                        <input class="form-control" id="setKop2" type="text" value="LAPORAN KEUANGAN KAS WARGA">
                    </div>
                    <div>
                        <label class="form-label-app" for="setAlamat">Alamat pada kop</label>
                        <input class="form-control" id="setAlamat" type="text" value="Alamat desa diisi di sini">
                    </div>

                    <h2 class="section-title border-top pt-3 mt-2">Penandatangan laporan</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setBendahara">Nama bendahara</label>
                            <input class="form-control" id="setBendahara" type="text" value="Bendahara (contoh)">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setKetua">Nama ketua</label>
                            <input class="form-control" id="setKetua" type="text" value="Ketua (contoh)">
                        </div>
                    </div>

                    <h2 class="section-title border-top pt-3 mt-2">Nominal standar</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setIuran">Iuran kematian per KK per minggu (Rp)</label>
                            <input class="form-control" id="setIuran" type="text" value="5.000">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-app" for="setSantunan">Santunan kematian standar (Rp)</label>
                            <input class="form-control" id="setSantunan" type="text" value="2.000.000">
                        </div>
                    </div>
                    <p class="hint mb-0">Mengubah nominal standar tidak mengubah transaksi yang sudah tercatat.</p>

                    <div class="notice notice-green" data-demo-feedback hidden></div>

                    <div>
                        <button class="btn btn-brand" type="submit">Simpan pengaturan</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="section-title mb-3">Akun pengguna</h2>

                    <div class="table-responsive">
                        <table class="table-app">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($akun as $u)
                                    <tr>
                                        <td>
                                            <strong class="d-block">{{ $u->name }}</strong>
                                            <span class="hint">{{ $u->username ?? $u->email }}</span>
                                            @unless ($u->aktif)
                                                <span class="badge-soft badge-neutral">Nonaktif</span>
                                            @endunless
                                        </td>
                                        <td>
                                            @if ($u->adalahAdmin())
                                                <span class="badge-soft badge-in">Admin</span>
                                            @else
                                                <span class="badge-soft badge-warn">Bendahara</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($u->is(auth()->user()))
                                                <span class="hint">Akun Anda</span>
                                            @else
                                                <form method="POST"
                                                    action="{{ route('akun.atur-ulang-password', $u) }}"
                                                    onsubmit="return confirm('Atur ulang kata sandi untuk {{ $u->name }}?')">
                                                    @csrf
                                                    <button class="btn-ghost" type="submit">Atur ulang</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="hint">Belum ada akun.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <button class="btn btn-outline-brand w-100 mt-3" type="button" data-bs-toggle="modal"
                        data-bs-target="#modalAkun">
                        <i class="bi bi-plus-lg"></i>Tambah akun
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAkun" tabindex="-1" aria-labelledby="modalAkunLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalAkunLabel">Tambah akun baru</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="POST" action="{{ route('akun.simpan') }}">
                    @csrf
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="akunNama">Nama pengguna</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="akunNama"
                                name="name" type="text" value="{{ old('name') }}"
                                placeholder="Contoh: Sekretaris Babatan" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunUsername">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" id="akunUsername"
                                name="username" type="text" value="{{ old('username') }}"
                                placeholder="Contoh: sekretaris" autocomplete="off" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="hint mt-1">Dipakai untuk masuk. Hanya huruf, angka, garis bawah, dan tanda hubung.
                            </p>
                        </div>
                        <div>
                            <label class="form-label-app" for="akunEmail">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="akunEmail"
                                name="email" type="email" value="{{ old('email') }}"
                                placeholder="nama@babatan.test" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunPeran">Peran</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="akunPeran"
                                name="role" required>
                                <option value="bendahara" @selected(old('role', 'bendahara') === 'bendahara')>Bendahara</option>
                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="akunPass">Kata sandi awal</label>
                            <input class="form-control @error('password') is-invalid @enderror" id="akunPass"
                                name="password" type="password" placeholder="Minimal 8 karakter"
                                autocomplete="new-password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="hint mt-1">Pengguna akan diminta mengganti kata sandi saat pertama masuk.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-brand" type="submit">Simpan akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var el = document.getElementById("modalAkun");
                if (el && window.bootstrap) {
                    new bootstrap.Modal(el).show();
                }
            });
        </script>
    @endif
@endsection
