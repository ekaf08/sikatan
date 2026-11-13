@extends('layouts.app')

@section('title', 'Pengaturan &mdash; Kas Desa Babatan')

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

    @if (session('galat'))
        <div class="notice notice-amber mb-3">{{ session('galat') }}</div>
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

        <div class="col-lg-5 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-body">
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
                </div>
            </div>

            <div class="card">
                <div class="card-body d-flex flex-column gap-2">
                    <h2 class="section-title">Kata sandi</h2>
                    <p class="hint mb-2">Untuk mengganti kata sandi akun Anda sendiri.</p>
                    <div>
                        <a class="btn btn-outline-brand" href="{{ route('password.ganti') }}">
                            <i class="bi bi-key"></i>Ganti kata sandi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
