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

            <h1>Ganti kata sandi</h1>

            @if ($wajib)
                <div class="alert alert-warning">
                    Anda masuk dengan kata sandi sementara. Buat kata sandi baru dulu.
                </div>
            @endif

            <form method="POST" action="{{ route('password.ganti.simpan') }}">
                @csrf

                <label for="passwordLama">{{ $wajib ? 'Kata sandi sementara' : 'Kata sandi saat ini' }}</label>
                <input class="form-control @error('password_lama') is-invalid @enderror" id="passwordLama"
                    name="password_lama" type="password" required>
                @error('password_lama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="passwordBaru">Kata sandi baru</label>
                <input class="form-control @error('password') is-invalid @enderror" id="passwordBaru" name="password"
                    type="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <label for="passwordKonfirmasi">Ulangi kata sandi baru</label>
                <input class="form-control" id="passwordKonfirmasi" name="password_confirmation" type="password"
                    required>

                <button class="btn btn-brand" type="submit">Simpan kata sandi</button>
            </form>
        </div>
    </body>

</html>
