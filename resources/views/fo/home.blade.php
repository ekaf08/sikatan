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
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>

    <body>
        @include('fo.partials.public-header')

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

            <form method="GET" action="{{ route('publik') }}" class="card mb-4" id="filter">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label-app" for="filterPos">Pos dana</label>
                            <select class="form-select" id="filterPos" name="pos_dana_id">
                                <option value="">Semua pos dana</option>
                                @foreach ($posDana as $pos)
                                    <option value="{{ $pos->id }}" @selected((int) $filter['pos_dana_id'] === $pos->id)>
                                        {{ $pos->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label-app" for="filterMulai">Dari tanggal</label>
                            <input class="form-control" id="filterMulai" name="tanggal_mulai" type="text"
                                data-flatpickr value="{{ $filter['tanggal_mulai'] }}" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label-app" for="filterSelesai">Sampai tanggal</label>
                            <input class="form-control" id="filterSelesai" name="tanggal_selesai" type="text"
                                data-flatpickr value="{{ $filter['tanggal_selesai'] }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button class="btn btn-brand flex-fill" type="submit">
                                <i class="bi bi-funnel"></i>Terapkan
                            </button>
                            <a class="btn btn-outline-brand" href="{{ route('publik') }}#pemasukan" title="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row g-4 mb-4" id="pemasukan">
                <div class="col-12 col-xl-6">
                    @include('fo.partials.transaksi-table', [
                        'id' => 'tabelPemasukanPublik',
                        'judul' => 'Pemasukan terbaru',
                        'hint' => $filter['label'],
                        'items' => $pemasukanTerbaru,
                        'kosong' => 'Belum ada data pemasukan tercatat pada periode ini.',
                    ])
                </div>

                <div class="col-12 col-xl-6" id="pengeluaran">
                    @include('fo.partials.transaksi-table', [
                        'id' => 'tabelPengeluaranPublik',
                        'judul' => 'Pengeluaran terbaru',
                        'hint' => $filter['label'],
                        'items' => $pengeluaranTerbaru,
                        'kosong' => 'Belum ada data pengeluaran tercatat pada periode ini.',
                    ])
                </div>
            </div>

            <div class="row g-4 mb-4" id="grafik">
                <div class="col-12 col-xl-6">
                    @include('fo.partials.pie-pos', [
                        'judul' => 'Pemasukan per pos dana',
                        'hint' => $filter['label'],
                        'grafik' => $pemasukanPos,
                        'totalLabel' => 'Total pemasukan periode ini',
                        'kosong' => 'Belum ada pemasukan pada periode ini.',
                    ])
                </div>

                <div class="col-12 col-xl-6">
                    @include('fo.partials.pie-pos', [
                        'judul' => 'Pengeluaran per pos dana',
                        'hint' => $filter['label'],
                        'grafik' => $pengeluaranPos,
                        'totalLabel' => 'Total pengeluaran periode ini',
                        'kosong' => 'Belum ada pengeluaran pada periode ini.',
                    ])
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
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
        <script>
        (function () {
            if (!window.flatpickr) return;
            if (window.flatpickr.l10ns && window.flatpickr.l10ns.id) {
                window.flatpickr.localize(window.flatpickr.l10ns.id);
            }
            document.querySelectorAll('[data-flatpickr]').forEach(function (el) {
                window.flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    disableMobile: true
                });
            });
        })();
        </script>
        <script>
        (function () {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.DataTable) return;

            const $ = window.jQuery;
            const bahasaDataTables = {
                search: 'Cari:',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Lanjut',
                    previous: 'Kembali'
                }
            };
            const domDataTables = "<'row align-items-center mb-3'<'col-sm-12 d-flex justify-content-end'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>";

            $('table[data-datatables]').each(function () {
                $(this).DataTable({
                    dom: domDataTables,
                    language: bahasaDataTables,
                    pageLength: 10,
                    ordering: false,
                    autoWidth: false
                });
            });
        })();
        </script>
    </body>

</html>
