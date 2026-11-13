@extends('layouts.app')

@section('title', 'Ganti Kata Sandi — Kas Desa Babatan')

@section('topbarTitle', 'Ganti kata sandi')
@section('topbarSubtitle', 'Perbarui kata sandi akun')

@section('content')

    <section class="login-form-wrap">
        <div class="login-form d-flex flex-column gap-3">
            <div class="mb-1">
                <span class="brand-mark mb-3" style="display:flex;background:var(--ks-soft-2);color:var(--ks-green)">
                    <i class="bi bi-key"></i>
                </span>
                <h2>Ganti kata sandi</h2>
                <p class="page-subtitle mt-1">
                    @if ($daftarPengguna->isNotEmpty())
                        Ganti kata sandi akun Anda sendiri atau pengguna lain.
                    @else
                        Buat kata sandi baru untuk akun Anda.
                    @endif
                </p>
            </div>

            @if ($wajib)
                <div class="notice notice-amber">
                    <i class="bi bi-exclamation-triangle me-1"></i>Anda masuk dengan kata sandi sementara. Buat
                    kata sandi baru dulu sebelum melanjutkan.
                </div>
            @endif

            @if (session('status'))
                <div class="notice notice-green">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-0" role="alert">{{ $errors->first() }}</div>
            @endif

            <form class="d-flex flex-column gap-3" method="POST" action="{{ route('password.ganti.simpan') }}">
                @csrf

                @if ($daftarPengguna->isNotEmpty())
                    <div>
                        <label class="form-label-app" for="targetUser">Ganti kata sandi untuk</label>
                        <select class="form-select" id="targetUser" name="user_id">
                            <option value="" @selected($terpilih === 0)>Akun saya
                                ({{ auth()->user()->name }})</option>
                            @foreach ($daftarPengguna as $u)
                                @continue($u->is(auth()->user()))
                                <option value="{{ $u->id }}" @selected($terpilih === $u->id)>
                                    {{ $u->name }} ({{ $u->username }})
                                    &mdash; {{ $u->adalahAdmin() ? 'Admin' : 'Bendahara' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="hint mt-1">Pilih pengguna lain untuk mengatur ulang kata sandinya.</p>
                    </div>
                @endif

                <div id="grupPasswordLama">
                    <label class="form-label-app" for="passwordLama">
                        {{ $wajib ? 'Kata sandi sementara' : 'Kata sandi saat ini' }}
                    </label>
                    <input class="form-control @error('password_lama') is-invalid @enderror" id="passwordLama"
                        name="password_lama" type="password" autocomplete="current-password"
                        @if (!$daftarPengguna->isNotEmpty() || $terpilih === 0) required @endif>
                    @error('password_lama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="hint mt-1">Hanya diperlukan saat mengganti kata sandi akun sendiri.</p>
                </div>

                <div>
                    <label class="form-label-app" for="passwordBaru">Kata sandi baru</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="passwordBaru" name="password"
                        type="password" autocomplete="new-password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p class="hint mt-1">Minimal 8 karakter, mengandung huruf dan angka.</p>
                </div>

                <div>
                    <label class="form-label-app" for="passwordKonfirmasi">Ulangi kata sandi baru</label>
                    <input class="form-control" id="passwordKonfirmasi" name="password_confirmation" type="password"
                        autocomplete="new-password" required>
                </div>

                <button class="btn btn-brand w-100 mt-2" type="submit" style="min-height:48px">
                    <i class="bi bi-check2"></i>Simpan kata sandi
                </button>

                <a class="link-app text-center" href="{{ route('dashboard') }}">Kembali ke dashboard</a>
            </form>
        </div>
    </section>

    <script>
        (function() {
            var select = document.getElementById("targetUser");
            var grup = document.getElementById("grupPasswordLama");
            var input = document.getElementById("passwordLama");

            if (!select || !grup || !input) {
                return;
            }

            function sync() {
                var orangLain = select.value !== "";
                grup.style.display = orangLain ? "none" : "";
                input.required = !orangLain;

                if (orangLain) {
                    input.value = "";
                }
            }

            select.addEventListener("change", sync);
            sync();
        })();
    </script>
@endsection
