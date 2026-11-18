<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Beranda - Sistem Kampung Babatan</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        @php
            $menuLayanan = app(\App\Services\MenuLayanan::class);
            $menuPublik = $menuLayanan->ambil('publik', auth()->user());
        @endphp
        <header class="public-header">
            <div class="public-header-inner">
                <div class="public-brand">
                    <strong>Sistem Kampung Babatan</strong>
                    <span>Portal informasi dan transparansi kampung</span>
                </div>

                <nav class="public-nav" aria-label="Navigasi publik">
                    @if ($menuPublik->isNotEmpty())
                        @foreach ($menuPublik as $menu)
                            @php
                                $href = $menuLayanan->href($menu);
                                $isActive = $menu->tipe === 'route' && $menu->route_name === 'publik' && request()->routeIs('publik');
                            @endphp
                            <a href="{{ $href }}" class="{{ $isActive ? 'active' : '' }}"
                                @if ($isActive) aria-current="page" @endif
                                @if ($menu->target === '_blank') target="_blank" rel="noopener" @endif>{{ $menu->judul }}</a>
                        @endforeach
                    @else
                        <a class="active" href="{{ route('publik') }}" aria-current="page">Beranda</a>
                        <a href="#pengeluaran">Pengeluaran</a>
                        <a href="#pemasukan">Pemasukan</a>
                        <a href="#donasi">Donasi</a>
                        <a href="{{ route('login') }}">Masuk bendahara</a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="public-main">
            <section class="d-flex flex-wrap align-items-end justify-content-between gap-4 mb-4">
                <div>
                    <span class="lbl mb-2">Total saldo kas kampung</span>
                    <h1 class="saldo-value">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</h1>
                    <p class="page-subtitle mt-1">
                        @if ($terakhirDiperbarui)
                            Diperbarui {{ $terakhirDiperbarui }} WIB. Data dari pencatatan kas.
                        @else
                            Belum ada data transaksi tercatat.
                        @endif
                    </p>
                </div>
            </section>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
                @forelse ($posDana as $pos)
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column gap-1">
                                <span class="stat-label">{{ $pos->nama }}</span>
                                <span class="stat-value">Rp{{ number_format((float) $pos->saldo_terkini, 0, ',', '.') }}</span>
                                <span class="stat-sub">Saldo terkini pos dana</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card"><div class="card-body hint">Belum ada data pos dana.</div></div>
                    </div>
                @endforelse
            </div>

            <div class="row g-4 mb-4" id="pemasukan">
                <div class="col-12 col-xl-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline gap-2 mb-3">
                                <h2 class="section-title">Pemasukan per bulan</h2>
                                <span class="hint">6 bulan terakhir (juta/rb)</span>
                            </div>

                            <div class="chart">
                                @foreach ($pemasukanBulanan as $item)
                                    <div class="chart-col">
                                        <span>{{ $item['tampil'] }}</span>
                                        <div class="chart-bar {{ $item['berjalan'] ? 'is-current' : '' }}" style="height:{{ $item['tinggi'] }}px" title="Rp{{ number_format($item['total'], 0, ',', '.') }}"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="chart-labels mt-2">
                                @foreach ($pemasukanBulanan as $item)
                                    <span>{{ $item['label'] }}{{ $item['berjalan'] ? ' (berjalan)' : '' }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7" id="pengeluaran">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                                <h2 class="section-title">Pengeluaran terbaru</h2>
                                <span class="hint">Transparansi pengeluaran kas</span>
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
                                        @forelse ($pengeluaranTerbaru as $item)
                                            <tr>
                                                <td style="white-space:nowrap">{{ $item['tanggal'] }}</td>
                                                <td>{{ $item['pos'] }}</td>
                                                <td>
                                                    <div>{{ $item['keterangan'] }}</div>
                                                    @if (!empty($item['lampiran']))
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            @foreach (array_slice($item['lampiran'], 0, 3) as $url)
                                                                <a href="{{ $url }}" target="_blank" rel="noopener" title="Lihat bukti">
                                                                    <img src="{{ $url }}" alt="Bukti" style="width:36px;height:36px;object-fit:cover;border-radius:.35rem;border:1px solid rgba(0,0,0,.12)">
                                                                </a>
                                                            @endforeach
                                                            @if (count($item['lampiran']) > 3)
                                                                <span class="hint">+{{ count($item['lampiran']) - 3 }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="num">Rp{{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center hint py-4">Belum ada data pengeluaran tercatat.</td>
                                            </tr>
                                        @endforelse
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
                    <h2 class="section-title" style="font-size:16px;color:#17443a">Laporan terbuka, perubahan tercatat</h2>
                    <p class="mt-1" style="font-size:14px;line-height:1.6;color:#3f4a44">
                        Data di halaman ini bersumber dari pencatatan kas kampung. Transaksi yang salah tidak dihapus, tetapi ditandai batal beserta alasannya. Informasi sensitif dan lampiran yang disembunyikan tidak ditampilkan kepada publik.
                    </p>
                </div>
                <a class="btn btn-brand" href="#pengeluaran">Lihat pengeluaran</a>
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
