<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Beranda Publik &mdash; Kas Desa Babatan</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        <header class="public-header">
            <div class="public-header-inner">
                <div class="public-brand">
                    <strong>Kas Desa Babatan</strong>
                    <span>Laporan keuangan terbuka untuk warga</span>
                </div>

                <nav class="public-nav" aria-label="Navigasi publik">
                    <a class="active" href="{{ route('publik') }}" aria-current="page">Beranda</a>
                    {{-- TODO: halaman publik Pengeluaran / Pemasukan / Laporan --}}
                    <a href="#">Pengeluaran</a>
                    <a href="#">Pemasukan</a>
                    <a href="#">Laporan</a>
                    <a href="{{ route('login') }}">Masuk bendahara</a>
                </nav>
            </div>
        </header>

        <main class="public-main">
            <section class="d-flex flex-wrap align-items-end justify-content-between gap-4 mb-4">
                <div>
                    <span class="lbl mb-2">Total saldo kas desa</span>
                    <h1 class="saldo-value">Rp64.645.000</h1>
                    <p class="page-subtitle mt-1">Diperbarui 12 November 2026, 08.30 WIB. Data contoh.</p>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <label class="form-label-app" for="publikRw">RW</label>
                        <select class="form-select" id="publikRw" style="min-width:150px">
                            <option>Semua RW</option>
                            <option>RW 01</option>
                            <option>RW 02</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-app" for="publikRt">RT</label>
                        <select class="form-select" id="publikRt" style="min-width:150px">
                            <option>Semua RT</option>
                            <option>RT 01</option>
                            <option>RT 02</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-app" for="publikPeriode">Periode</label>
                        <select class="form-select" id="publikPeriode" style="min-width:170px">
                            <option>Triwulan 4 2026</option>
                            <option>Triwulan 3 2026</option>
                            <option>Tahun 2026</option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Kas kematian</span>
                            <span class="stat-value">Rp35.240.000</span>
                            <span class="stat-sub">Iuran mingguan warga untuk santunan</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Kas masjid</span>
                            <span class="stat-value">Rp8.910.000</span>
                            <span class="stat-sub">Uang kotak Jumat dan donasi</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Kas sedekah bumi</span>
                            <span class="stat-value">Rp18.350.000</span>
                            <span class="stat-sub">Anggaran acara tahun ini: Rp33.000.000</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Kas lain-lain</span>
                            <span class="stat-value">Rp2.145.000</span>
                            <span class="stat-sub">Perawatan kecil dan pemasukan lain</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-xl-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline gap-2 mb-3">
                                <h2 class="section-title">Pemasukan per bulan</h2>
                                <span class="hint">dalam jutaan rupiah</span>
                            </div>

                            <div class="chart">
                                <div class="chart-col">
                                    <span>4,1</span>
                                    <div class="chart-bar" style="height:123px"></div>
                                </div>
                                <div class="chart-col">
                                    <span>3,8</span>
                                    <div class="chart-bar" style="height:114px"></div>
                                </div>
                                <div class="chart-col">
                                    <span>4,4</span>
                                    <div class="chart-bar" style="height:132px"></div>
                                </div>
                                <div class="chart-col">
                                    <span>3,9</span>
                                    <div class="chart-bar" style="height:117px"></div>
                                </div>
                                <div class="chart-col">
                                    <span>4,6</span>
                                    <div class="chart-bar" style="height:138px"></div>
                                </div>
                                <div class="chart-col">
                                    <span>2,7</span>
                                    <div class="chart-bar is-current" style="height:81px"></div>
                                </div>
                            </div>
                            <div class="chart-labels mt-2">
                                <span>Jun</span>
                                <span>Jul</span>
                                <span>Agu</span>
                                <span>Sep</span>
                                <span>Okt</span>
                                <span>Nov (berjalan)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                                <h2 class="section-title">Pengeluaran terbaru</h2>
                                <a class="link-app" href="#">Lihat semua</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table-app">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Pos dana</th>
                                            <th>Keterangan</th>
                                            <th class="num">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>09 Nov</td>
                                            <td>Kas Lain-lain</td>
                                            <td>Pembelian alat kebersihan</td>
                                            <td class="num">Rp125.000</td>
                                        </tr>
                                        <tr>
                                            <td>06 Nov</td>
                                            <td>Kas Sedekah Bumi</td>
                                            <td>Uang muka konsumsi sedekah bumi</td>
                                            <td class="num">Rp4.500.000</td>
                                        </tr>
                                        <tr>
                                            <td>05 Nov</td>
                                            <td>Kas Sedekah Bumi</td>
                                            <td>Uang muka sewa tenda dan pengeras suara</td>
                                            <td class="num">Rp2.500.000</td>
                                        </tr>
                                        <tr>
                                            <td>30 Okt</td>
                                            <td>Kas Masjid</td>
                                            <td>Ganti kran tempat wudu yang rusak</td>
                                            <td class="num">Rp350.000</td>
                                        </tr>
                                        <tr>
                                            <td>28 Okt</td>
                                            <td>Kas Lain-lain</td>
                                            <td>Ganti 4 lampu jalan di RW 01</td>
                                            <td class="num">Rp480.000</td>
                                        </tr>
                                        <tr>
                                            <td>24 Okt</td>
                                            <td>Kas Kematian</td>
                                            <td>Santunan kematian warga, dengan tambahan untuk kebutuhan pendidikan ahli
                                                waris</td>
                                            <td class="num">Rp3.500.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4" id="donasi">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="section-title" style="font-size:18px;color:#17443a">Donasi Online (Kas Masjid)</h2>
                            <p class="hint mt-1" style="max-width:720px">Donasi diproses melalui Midtrans. Status pembayaran yang sah ditentukan dari notifikasi server; setelah berhasil, transaksi pemasukan dicatat otomatis. Pembatalan dana yang sudah masuk hanya dapat dilakukan oleh bendahara.</p>
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

            <div class="soft-panel p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div style="max-width:720px">
                    <h2 class="section-title" style="font-size:16px;color:#17443a">Laporan terbuka, perubahan tercatat
                    </h2>
                    <p class="mt-1" style="font-size:14px;line-height:1.6;color:#3f4a44">
                        Transaksi yang salah tidak dihapus, tetapi ditandai Batal beserta alasannya. Untuk menjaga
                        privasi keluarga yang berduka, nama penerima santunan dan foto nota tertentu tidak ditampilkan.
                    </p>
                </div>
                <a class="btn btn-brand" href="#">Lihat pengeluaran</a>
            </div>

            <footer class="d-flex flex-wrap justify-content-between gap-2 py-4 mt-2 hint">
                <span>&copy; 2026 Kas Desa Babatan. Data contoh untuk template.</span>
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
