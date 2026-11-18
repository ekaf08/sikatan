<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iuran Kematian - Sistem Kampung Babatan</title>
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
                <span class="lbl mb-2">Transparansi iuran</span>
                <h1 class="page-title">Iuran Kematian</h1>
                <p class="page-subtitle mt-1">Daftar RT yang belum menyetor iuran kematian pada periode terpilih.</p>
            </div>

            <form method="GET" action="{{ route('iuran-kematian') }}" class="card mb-4" id="formFilterIuran">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="form-label-app" for="filterMinggu">Minggu ke</label>
                            <select class="form-select" id="filterMinggu" name="minggu">
                                <option value="0" @selected($filter['minggu'] === 0)>Semua minggu</option>
                                @foreach (range(1, 5) as $minggu)
                                    <option value="{{ $minggu }}" @selected($filter['minggu'] === $minggu)>
                                        Minggu {{ $minggu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label-app" for="filterBulan">Bulan</label>
                            <select class="form-select" id="filterBulan" name="bulan">
                                @foreach ($opsiBulan as $opsi)
                                    <option value="{{ $opsi['value'] }}" @selected($filter['bulan'] === $opsi['value'])>
                                        {{ $opsi['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label-app" for="filterTahun">Tahun</label>
                            <select class="form-select" id="filterTahun" name="tahun">
                                @foreach ($opsiTahun as $tahun)
                                    <option value="{{ $tahun }}" @selected($filter['tahun'] === $tahun)>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-brand w-100" type="submit">
                                <i class="bi bi-funnel"></i>Terapkan
                            </button>
                        </div>
                    </div>

                    @if ($peringatanMinggu)
                        <div class="alert alert-warning py-2 mt-3 mb-0">{{ $peringatanMinggu }}</div>
                    @endif
                </div>
            </form>

            <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Total RT</span>
                            <span class="stat-value">{{ $jumlahRt }}</span>
                            <span class="stat-sub">RT terdaftar</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Sudah setor</span>
                            <span class="stat-value">{{ $jumlahSudahSetor }}</span>
                            <span class="stat-sub">{{ $labelPeriode }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column gap-1">
                            <span class="stat-label">Belum setor</span>
                            <span class="stat-value is-out">{{ $jumlahBelumSetor }}</span>
                            <span class="stat-sub">{{ $labelPeriode }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline gap-2 mb-3">
                        <h2 class="section-title">Status iuran kematian RT</h2>
                        <span class="hint">{{ $labelPeriode }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table-app">
                            <thead>
                                <tr>
                                    <th>RW</th>
                                    <th>RT</th>
                                    <th>Nama Penarik</th>
                                    <th class="num">Jumlah KK</th>
                                    <th class="num">Nominal Setor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftarIuran as $baris)
                                    <tr>
                                        <td>{{ $baris['rw'] }}</td>
                                        <td>RT {{ $baris['rt'] }}</td>
                                        <td>{{ $baris['penarik'] }}</td>
                                        <td class="num">{{ $baris['jumlah_kk'] }}</td>
                                        <td class="num">
                                            @if ($baris['nominal'] > 0)
                                                Rp {{ number_format($baris['nominal'], 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($baris['status'] === 'lunas')
                                                <span class="badge-soft badge-in">Lunas</span>
                                            @elseif ($baris['status'] === 'sebagian')
                                                <span class="badge-soft badge-warn">
                                                    Sebagian ({{ $baris['total_kk'] }}/{{ $baris['jumlah_kk'] }} KK)
                                                </span>
                                            @else
                                                <span class="badge-soft badge-out">Belum setor</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center hint py-4">Belum ada data RT.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="d-flex flex-wrap justify-content-between gap-2 py-4 mt-2 hint">
                <span>&copy; {{ date('Y') }} Sistem Kampung Babatan.</span>
                <span>Transparan, rapi, dan dapat diaudit.</span>
            </footer>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        (function () {
            const form = document.getElementById('formFilterIuran');
            const bulan = document.getElementById('filterBulan');
            const tahun = document.getElementById('filterTahun');
            const minggu = document.getElementById('filterMinggu');
            if (!form || !bulan || !tahun || !minggu) return;

            function maksMinggu() {
                const hari = new Date(parseInt(tahun.value, 10), parseInt(bulan.value, 10), 0).getDate();
                return Math.ceil(hari / 7);
            }

            function namaBulan() {
                return bulan.options[bulan.selectedIndex].text.trim();
            }

            form.addEventListener('submit', function (e) {
                const maks = maksMinggu();
                const terpilih = parseInt(minggu.value, 10);

                if (terpilih > maks) {
                    e.preventDefault();

                    const pesan = 'Bulan ' + namaBulan() + ' ' + tahun.value + ' hanya memiliki '
                        + maks + ' minggu, sehingga minggu ke-' + terpilih + ' tidak tersedia.';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minggu tidak tersedia',
                            text: pesan,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#17443a'
                        });
                    } else {
                        window.alert(pesan);
                    }
                }
            });
        })();
        </script>
    </body>

</html>
