@extends('layouts.app')

@section('title', 'Ganti Kata Sandi — Kas Desa Babatan')

@section('topbarTitle', 'Ganti kata sandi')
@section('topbarSubtitle', 'Perbarui kata sandi akun')

@section('content')

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
        <!-- Card Wrapper -->
        <div class="card border-0 shadow-sm" style="max-width: 480px; width: 100%; border-radius: 16px;">
            <div class="card-body p-4 p-md-5">

                <!-- Header: Ikon & Judul Sejajar -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <!-- Ikon Kunci -->
                    <span class="brand-mark"
                        style="display:flex; background:var(--ks-green); color:white; width: 52px; height: 52px; align-items: center; justify-content: center; border-radius: 12px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        <i class="bi bi-key-fill fs-4"></i>
                    </span>

                    <!-- Teks Judul -->
                    <div>
                        <h2 class="fw-bold mb-1" style="color: var(--ks-green); font-size: 1.5rem;">Ganti kata sandi</h2>
                        <p class="text-muted mb-0" style="font-size: 14px;">
                            @if ($daftarPengguna->isNotEmpty())
                                Ganti kata sandi akun Anda atau pengguna lain.
                            @else
                                Buat kata sandi baru untuk akun Anda.
                            @endif
                        </p>
                    </div>
                </div>

                @if ($wajib)
                    <div class="notice notice-amber mb-4 p-3 rounded"
                        style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; font-size: 14px;">
                        <i class="bi bi-exclamation-triangle me-1"></i>Anda masuk dengan kata sandi sementara. Buat
                        kata sandi baru dulu sebelum melanjutkan.
                    </div>
                @endif

                @if (session('status'))
                    <div class="notice notice-green mb-4 p-3 rounded"
                        style="background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; font-size: 14px;">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2 mb-4" role="alert" style="font-size: 14px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form class="d-flex flex-column gap-3" method="POST" action="{{ route('password.ganti.simpan') }}">
                    @csrf

                    @if ($daftarPengguna->isNotEmpty())
                        <div>
                            <label class="form-label-app fw-semibold" for="targetUser">Ganti kata sandi untuk</label>
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
                            <p class="hint text-muted mt-1" style="font-size: 12px;">Pilih pengguna lain untuk mengatur
                                ulang kata sandinya.</p>
                        </div>
                    @endif

                    <div id="grupPasswordLama">
                        <label class="form-label-app fw-semibold" for="passwordLama">
                            {{ $wajib ? 'Kata sandi sementara' : 'Kata sandi saat ini' }}
                        </label>
                        <input class="form-control @error('password_lama') is-invalid @enderror" id="passwordLama"
                            name="password_lama" type="password" autocomplete="current-password"
                            @if (!$daftarPengguna->isNotEmpty() || $terpilih === 0) required @endif>
                        @error('password_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="hint text-muted mt-1" style="font-size: 12px;">Hanya diperlukan saat mengganti kata sandi
                            akun sendiri.</p>
                    </div>

                    <div>
                        <label class="form-label-app fw-semibold" for="passwordBaru">Kata sandi baru</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="passwordBaru"
                            name="password" type="password" autocomplete="new-password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="hint text-muted mt-1" style="font-size: 12px;">Minimal 8 karakter, mengandung huruf dan
                            angka.</p>
                    </div>

                    <div>
                        <label class="form-label-app fw-semibold" for="passwordKonfirmasi">Ulangi kata sandi baru</label>
                        <input class="form-control" id="passwordKonfirmasi" name="password_confirmation" type="password"
                            autocomplete="new-password" required>
                    </div>

                    <button class="btn btn-brand w-100 mt-3" type="submit" style="min-height:48px; border-radius: 8px;">
                        <i class="bi bi-check2 me-1"></i>Simpan kata sandi
                    </button>

                    <a class="link-app text-center mt-2 text-decoration-none" href="{{ route('dashboard') }}"
                        style="color: var(--ks-green);">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard
                    </a>
                </form>

            </div>
        </div>
    </div>

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
