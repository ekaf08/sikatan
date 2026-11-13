<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lupa Kata Sandi &mdash; Kas Desa Babatan</title>
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
                <div class="login-form d-flex flex-column gap-3">
                    <div class="mb-1">
                        <span class="brand-mark mb-3"
                            style="display:flex;background:var(--ks-soft-2);color:var(--ks-green)">
                            <i class="bi bi-key"></i>
                        </span>
                        <h2>Lupa kata sandi?</h2>
                        <p class="page-subtitle mt-1">Kata sandi hanya bisa diatur ulang oleh admin desa.</p>
                    </div>

                    <div class="notice notice-amber">
                        <i class="bi bi-info-circle me-1"></i>Demi keamanan, kami tidak mengirim kata sandi lewat
                        email atau pesan. Hubungi admin desa secara langsung.
                    </div>

                    <ol class="reset-steps my-2">
                        <li>Hubungi admin desa dan sebutkan <strong>username</strong> Anda.</li>
                        <li>Admin akan memberi <strong>kata sandi sementara</strong>.</li>
                        <li>Masuk dengan kata sandi itu, lalu segera <strong>buat kata sandi baru</strong>.</li>
                    </ol>

                    <a class="btn btn-brand w-100" href="{{ route('login') }}" style="min-height:48px">
                        <i class="bi bi-arrow-left"></i>Kembali ke halaman masuk
                    </a>

                    @if (Route::has('publik'))
                        <a class="link-app text-center" href="{{ route('publik') }}">Lihat laporan publik tanpa
                            login</a>
                    @endif
                </div>
            </section>
        </div>
    </body>

</html>
