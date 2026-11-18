<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Donasi - Sistem Kampung Babatan</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        @include('fo.partials.public-header')

        <main class="public-main">
            <div class="mb-4">
                <span class="lbl mb-2">Donasi online</span>
                <h1 class="page-title">Donasi Kas Masjid</h1>
                <p class="page-subtitle mt-1">Donasi diproses melalui Midtrans dan dicatat otomatis ke kas setelah
                    pembayaran berhasil.</p>
            </div>

            <div class="card mb-4" id="donasi">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="section-title" style="font-size:18px;color:#17443a">Formulir Donasi</h2>
                            <p class="hint mt-1" style="max-width:720px">Status pembayaran yang sah ditentukan dari
                                notifikasi server. Pembatalan dana yang sudah masuk hanya dapat dilakukan oleh
                                bendahara.</p>
                        </div>
                    </div>

                    @if (session('donasi_error'))
                        <div class="alert alert-danger py-2">{{ session('donasi_error') }}</div>
                    @endif
                    <div id="donasiAlert" class="alert d-none py-2"></div>

                    <form id="formDonasi" action="{{ route('donasi.buat') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="nominal">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" id="nominal" name="nominal" min="1000" step="1000" required placeholder="contoh: 50000">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="nama_donatur">Nama Donatur</label>
                            <input class="form-control" type="text" id="nama_donatur" name="nama_donatur" maxlength="255" placeholder="Nama (opsional)">
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="donatur_anonim" name="donatur_anonim" value="1">
                                <label class="form-check-label" for="donatur_anonim">Sembunyikan nama (Hamba Allah)</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="tujuan">Tujuan Donasi</label>
                            <input class="form-control" type="text" id="tujuan" name="tujuan" maxlength="255" placeholder="Mis. operasional masjid">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="email">Email (opsional)</label>
                            <input class="form-control" type="email" id="email" name="email" maxlength="255" placeholder="untuk data pembayaran">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="no_hp">No. HP (opsional)</label>
                            <input class="form-control" type="text" id="no_hp" name="no_hp" maxlength="30" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label-app" for="catatan_publik">Catatan (opsional)</label>
                            <textarea class="form-control" id="catatan_publik" name="catatan_publik" rows="2" maxlength="1000" placeholder="Catatan singkat"></textarea>
                        </div>
                        <div class="col-12 d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-brand" id="btnDonasi">Bayar Donasi</button>
                            <span class="hint" id="donasiHint"></span>
                        </div>
                    </form>
                </div>
            </div>

            <footer class="d-flex flex-wrap justify-content-between gap-2 py-4 mt-2 hint">
                <span>&copy; {{ date('Y') }} Sistem Kampung Babatan.</span>
                <span>Transparan, rapi, dan dapat diaudit.</span>
            </footer>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @php
            $midtransClientKey = config('services.midtrans.client_key');
            $midtransSnapUrl = config('services.midtrans.is_production')
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
        @endphp
        @if ($midtransClientKey)
            <script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
        @endif
        <script>
        (function () {
            const form = document.getElementById('formDonasi');
            if (!form) return;
            const btn = document.getElementById('btnDonasi');
            const hint = document.getElementById('donasiHint');
            const alertBox = document.getElementById('donasiAlert');
            const anonim = document.getElementById('donatur_anonim');
            const nama = document.getElementById('nama_donatur');

            function showAlert(type, msg) {
                alertBox.className = 'alert py-2 alert-' + type;
                alertBox.textContent = msg;
                alertBox.classList.remove('d-none');
            }

            anonim.addEventListener('change', function () {
                nama.disabled = anonim.checked;
                if (anonim.checked) nama.value = '';
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                alertBox.classList.add('d-none');
                btn.disabled = true;
                hint.textContent = 'Memproses...';
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('[name=_token]').value
                        },
                        body: new FormData(form)
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Gagal memproses pembayaran.');
                        showAlert('danger', msg);
                        return;
                    }
                    if (data.snap_token && window.snap) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: function () { window.location = data.finish_url; },
                            onPending: function () { window.location = data.finish_url; },
                            onError: function () { showAlert('danger', 'Pembayaran gagal atau dibatalkan.'); },
                            onClose: function () { hint.textContent = 'Anda menutup popup pembayaran.'; }
                        });
                    } else if (data.redirect_url) {
                        window.location = data.redirect_url;
                    } else if (data.finish_url) {
                        window.location = data.finish_url;
                    } else {
                        showAlert('warning', 'Pembayaran dibuat, namun token Snap tidak tersedia. Periksa konfigurasi Midtrans.');
                    }
                } catch (err) {
                    showAlert('danger', 'Terjadi kesalahan jaringan.');
                } finally {
                    btn.disabled = false;
                    hint.textContent = '';
                }
            });
        })();
        </script>
    </body>

</html>
