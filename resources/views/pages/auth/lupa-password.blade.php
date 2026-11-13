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
                <h2>Lupa kata sandi?</h2>
                <p>Kata sandi diatur ulang oleh admin desa.</p>
                <ol>
                    <li>Hubungi admin dan sebutkan username Anda.</li>
                    <li>Admin akan memberi kata sandi sementara.</li>
                    <li>Masuk dengan kata sandi itu, lalu buat kata sandi baru.</li>
                </ol>
                <a href="{{ route('login') }}">Kembali ke halaman masuk</a>
            </section>
        </div>
    </body>

</html>
