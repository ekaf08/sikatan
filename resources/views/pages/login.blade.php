<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Masuk &mdash; Kas Desa Babatan</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        <div class="login-page">
            <section class="login-brand">
                <div class="login-brand-head">
                    <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
                    <div class="d-flex flex-column">
                        <strong style="font-size:20px;font-weight:700">Kas Desa Babatan</strong>
                        <span style="font-size:13px;color:#bfd3cb">Area bendahara dan admin</span>
                    </div>
                </div>

                <div class="login-brand-copy">
                    <h1>Catat dengan rapi, laporkan dengan terbuka.</h1>
                    <p>Setiap transaksi tercatat siapa yang menginput, dan perubahan apa pun tersimpan di log audit.</p>
                </div>

                <span class="login-brand-foot">Warga tidak perlu login. Laporan terbuka tersedia di halaman
                    publik.</span>
            </section>

            <section class="login-form-wrap">
                <form class="login-form d-flex flex-column gap-3" method="POST" action="{{ route('login.store') }}"
                    novalidate>
                    @csrf

                    <div class="mb-2">
                        <h2>Masuk</h2>
                        <p class="page-subtitle">Gunakan akun yang diberikan admin desa.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-0" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div>
                        {{-- <label class="form-label-app" for="loginEmail">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="loginEmail" name="email"
                            type="email" value="{{ old('email') }}" autocomplete="username" required autofocus> --}}

                        <label class="form-label-app" for="loginUsername">Username</label>
                        <input class="form-control @error('username') is-invalid @enderror" id="loginUsername"
                            name="username" type="text" value="{{ old('username') }}" autocomplete="username"
                            required autofocus>
                    </div>

                    <div>
                        <label class="form-label-app" for="loginPassword">Kata sandi</label>
                        <div class="position-relative">
                            <input class="form-control pe-5 @error('password') is-invalid @enderror" id="loginPassword"
                                name="password" type="password" autocomplete="current-password" required>
                            <button class="btn-icon position-absolute top-50 end-0 translate-middle-y me-1"
                                type="button" id="togglePassword" aria-label="Tampilkan kata sandi"
                                style="width:36px;height:36px">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="form-check d-flex align-items-center gap-1">
                            <input class="form-check-input" id="ingatSaya" name="remember" value="1"
                                type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="ingatSaya">Ingat saya di perangkat ini</label>
                        </div>
                        <a class="link-app" href="#">Lupa kata sandi?</a>
                    </div>

                    <button class="btn btn-brand w-100 mt-2" type="submit" style="min-height:48px">
                        <i class="bi bi-box-arrow-in-right"></i>Masuk
                    </button>

                    @if (Route::has('publik'))
                        <a class="link-app text-center mt-2" href="{{ route('publik') }}">Lihat laporan publik tanpa
                            login</a>
                    @endif
                </form>
            </section>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            (function() {
                var toggle = document.getElementById("togglePassword");
                var input = document.getElementById("loginPassword");

                if (!toggle || !input) {
                    return;
                }

                toggle.addEventListener("click", function() {
                    var visible = input.type === "text";
                    input.type = visible ? "password" : "text";
                    toggle.setAttribute("aria-label", visible ? "Tampilkan kata sandi" : "Sembunyikan kata sandi");
                    toggle.querySelector("i").className = visible ? "bi bi-eye" : "bi bi-eye-slash";
                });
            })();

            //           document.getElementById('togglePassword').addEventListener('click', function () {
            //     const input = document.getElementById('loginPassword');
            //     const tampil = input.type === 'password';
            //     input.type = tampil ? 'text' : 'password';
            //     this.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            //     this.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
            // });
        </script>
    </body>

</html>
