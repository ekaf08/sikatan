@extends('layouts.app')

@section('title', 'Acara Sedekah Bumi - Kas Desa Babatan')

@section('topbarTitle', 'Acara sedekah bumi')
@section('topbarSubtitle', 'Anggaran dan realisasi')

@section('content')
    <div id="acara-state"
        data-sukses="{{ session('sukses') }}"
        data-galat="{{ session('galat') }}"
        data-acara-error="{{ $errors->hasAny(['nama', 'tahun', 'status']) ? '1' : '0' }}"
        data-anggaran-error="{{ $errors->hasAny(['uraian', 'nominal_anggaran']) ? '1' : '0' }}"
        style="display: none;"></div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">{{ $acara?->nama ?? 'Sedekah Bumi' }}</h1>
            <p class="page-subtitle">
                @if ($acara)
                    Status: {{ $statusAcara[$acara->status] ?? $acara->status }}. Anggaran dan realisasi per item.
                @else
                    Belum ada acara. Tambahkan acara untuk mulai menyusun anggaran.
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($daftarAcara->isNotEmpty())
                <form method="GET" action="{{ route('acara') }}" class="d-flex gap-2">
                    <select class="form-select" name="acara" aria-label="Ganti acara" style="min-width: 190px"
                        onchange="this.form.submit()">
                        @foreach ($daftarAcara as $pilihan)
                            <option value="{{ $pilihan->id }}" @selected($acara?->id === $pilihan->id)>
                                {{ $pilihan->nama }}
                            </option>
                        @endforeach
                    </select>
                    <noscript><button class="btn btn-outline-brand" type="submit">Pilih</button></noscript>
                </form>
            @endif
            <button class="btn btn-brand" type="button" id="btnTambahAcara">
                <i class="bi bi-plus-lg"></i>Tambah acara
            </button>
            @if ($acara)
                <button class="btn btn-outline-brand" type="button" id="btnUbahAcara"
                    data-id="{{ $acara->id }}" data-nama="{{ $acara->nama }}" data-tahun="{{ $acara->tahun }}"
                    data-status="{{ $acara->status }}"
                    data-update="{{ route('acara.update', $acara) }}">
                    <i class="bi bi-pencil"></i>Ubah acara
                </button>
                <button class="btn btn-outline-brand" type="button" data-bs-toggle="modal"
                    data-bs-target="#modalAnggaran" id="btnTambahAnggaran">
                    <i class="bi bi-plus-lg"></i>Tambah item anggaran
                </button>
            @endif
        </div>
    </div>

    @if (! $acara)
        <div class="card">
            <div class="card-body text-center py-5">
                <h2 class="section-title mb-2">Belum ada acara sedekah bumi</h2>
                <p class="hint mb-3">Buat acara terlebih dahulu, lalu tambahkan item anggaran dan tautkan pengeluaran.</p>
                <button class="btn btn-brand" type="button" id="btnTambahAcaraKosong">
                    <i class="bi bi-plus-lg"></i>Tambah acara
                </button>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
            <div class="col">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <span class="stat-label">Total anggaran</span>
                        <span class="stat-value">Rp{{ number_format($totalAnggaran, 0, ',', '.') }}</span>
                        <span class="stat-sub">{{ $anggaran->count() }} item anggaran</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <span class="stat-label">Realisasi</span>
                        <span class="stat-value is-out">Rp{{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                        <span class="stat-sub">{{ number_format($persenRealisasi, 0, ',', '.') }}% dari anggaran</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <span class="stat-label">Sisa anggaran</span>
                        <span class="stat-value {{ $sisaAnggaran < 0 ? 'is-out' : 'is-in' }}">
                            Rp{{ number_format($sisaAnggaran, 0, ',', '.') }}
                        </span>
                        <span class="stat-sub">{{ $sisaAnggaran < 0 ? 'Melebihi anggaran' : 'Masih tersedia' }}</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <span class="stat-label">Saldo kas sedekah bumi</span>
                        <span class="stat-value">Rp{{ number_format($saldoSedekahBumi, 0, ',', '.') }}</span>
                        <span class="stat-sub">Saldo berjalan pos sedekah bumi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="section-title mb-0">Anggaran vs realisasi</h2>
                    <span class="badge-soft {{ $acara->status === 'berjalan' ? 'badge-in' : ($acara->status === 'rencana' ? 'badge-warn' : 'badge-neutral') }}">
                        {{ $statusAcara[$acara->status] ?? $acara->status }}
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table-app w-100 align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Anggaran</th>
                                <th class="text-end">Realisasi</th>
                                <th class="text-end">Sisa</th>
                                <th style="min-width: 180px;">Terpakai</th>
                                <th class="text-end" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($anggaran as $item)
                                <tr>
                                    <td>{{ $item->uraian }}</td>
                                    <td class="text-end">Rp{{ number_format($item->nominal_anggaran, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($item->realisasi, 0, ',', '.') }}</td>
                                    <td class="text-end {{ $item->sisa_item < 0 ? 'text-danger' : '' }}">
                                        Rp{{ number_format($item->sisa_item, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="meter flex-grow-1">
                                                <i style="width: {{ $item->persen_item }}%"></i>
                                            </div>
                                            <span class="fw-semibold nowrap" style="font-size: 13px; min-width: 36px;">
                                                {{ number_format($item->persen_item, 0, ',', '.') }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-anggaran"
                                            data-id="{{ $item->id }}" data-uraian="{{ $item->uraian }}"
                                            data-nominal="{{ (int) $item->nominal_anggaran }}"
                                            data-update="{{ route('acara.anggaran.update', [$acara, $item]) }}" title="Ubah">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST"
                                            action="{{ route('acara.anggaran.destroy', [$acara, $item]) }}"
                                            class="form-hapus-anggaran d-inline m-0"
                                            data-info="{{ $item->uraian }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger tombol-hapus-anggaran"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="hint">Belum ada item anggaran untuk acara ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($anggaran->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold">Rp{{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Rp{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold {{ $sisaAnggaran < 0 ? 'text-danger' : '' }}">
                                        Rp{{ number_format($sisaAnggaran, 0, ',', '.') }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                <p class="hint mt-3 mb-0">
                    Realisasi per item dihitung dari pengeluaran yang ditautkan ke item anggaran lewat formulir Catat
                    pengeluaran. Pengeluaran yang hanya ditautkan ke acara tetap masuk ke total realisasi.
                </p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <h2 class="section-title mb-0">Pengeluaran terkait acara</h2>
                            <a class="btn btn-sm btn-outline-brand" href="{{ route('pengeluaran') }}">Catat pengeluaran</a>
                        </div>
                        @forelse ($pengeluaranAcara as $keluar)
                            <div class="recent-row">
                                <span>
                                    {{ $keluar->tanggal?->locale('id')->translatedFormat('d M') }} &middot;
                                    {{ $keluar->catatan_publik ?: ($keluar->kategori?->nama ?? 'Pengeluaran') }}
                                    @if ($keluar->anggaranAcara)
                                        <span class="hint">&middot; {{ $keluar->anggaranAcara->uraian }}</span>
                                    @endif
                                </span>
                                <strong>Rp{{ number_format($keluar->nominal, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <p class="hint mb-0">Belum ada pengeluaran yang ditautkan ke acara ini.</p>
                        @endforelse
                        @if ($pengeluaranAcara->count() >= 50)
                            <p class="hint mt-3 mb-0">Menampilkan 50 pengeluaran terbaru.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="section-title mb-2">Pemasukan iuran sedekah bumi</h2>
                        @forelse ($pemasukanRw as $rw)
                            <div class="recent-row">
                                <span>{{ $rw['nama'] }} ({{ $rw['jumlah_rt'] }} RT)</span>
                                <strong>Rp{{ number_format($rw['jumlah'], 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <p class="hint mb-2">Belum ada pemasukan iuran sedekah bumi.</p>
                        @endforelse
                        <div class="recent-row">
                            <span class="fw-semibold">Total terkumpul</span>
                            <strong>Rp{{ number_format($totalPemasukanSedekahBumi, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <form method="POST" action="{{ route('acara.destroy', $acara) }}" id="formHapusAcara"
                data-info="{{ $acara->nama }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger" type="button" id="btnHapusAcara">
                    <i class="bi bi-trash"></i>Hapus acara
                </button>
            </form>
        </div>
    @endif

    <div class="modal fade" id="modalAcara" tabindex="-1" aria-labelledby="modalAcaraLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalAcaraLabel">Tambah acara</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="POST" action="{{ route('acara.simpan') }}" id="formAcara">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="acaraMethod">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="acaraNama">Nama acara</label>
                            <input class="form-control @error('nama') is-invalid @enderror" id="acaraNama"
                                name="nama" type="text" value="{{ old('nama') }}"
                                placeholder="Contoh: Sedekah Bumi {{ now()->year }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="acaraTahun">Tahun</label>
                            <input class="form-control @error('tahun') is-invalid @enderror" id="acaraTahun"
                                name="tahun" type="number" min="2000" max="2100"
                                value="{{ old('tahun', $acara->tahun ?? now()->year) }}" required>
                            @error('tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label-app" for="acaraStatus">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="acaraStatus"
                                name="status" required>
                                @foreach ($statusAcara as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected(old('status', 'rencana') === $nilai)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-brand" type="submit">Simpan acara</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($acara)
        <div class="modal fade" id="modalAnggaran" tabindex="-1" aria-labelledby="modalAnggaranLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="modalAnggaranLabel">Tambah item anggaran</h2>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form method="POST" action="{{ route('acara.anggaran.simpan', $acara) }}" id="formAnggaran">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="anggaranMethod">
                        <div class="modal-body d-flex flex-column gap-3">
                            <div>
                                <label class="form-label-app" for="anggaranUraian">Nama item</label>
                                <input class="form-control @error('uraian') is-invalid @enderror" id="anggaranUraian"
                                    name="uraian" type="text" value="{{ old('uraian') }}"
                                    placeholder="Contoh: Panggung dan dekorasi" required>
                                @error('uraian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label-app" for="anggaranNominal">Anggaran (Rp)</label>
                                <input class="form-control @error('nominal_anggaran') is-invalid @enderror"
                                    id="anggaranNominal" name="nominal_anggaran" type="text" inputmode="numeric"
                                    value="{{ old('nominal_anggaran') }}" placeholder="Contoh: 3.000.000"
                                    data-ribuan-acara required>
                                @error('nominal_anggaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-brand" type="submit">Simpan item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const state = document.getElementById('acara-state');
            const modalAcaraEl = document.getElementById('modalAcara');
            const modalAnggaranEl = document.getElementById('modalAnggaran');
            const modalAcara = modalAcaraEl ? new bootstrap.Modal(modalAcaraEl) : null;
            const modalAnggaran = modalAnggaranEl ? new bootstrap.Modal(modalAnggaranEl) : null;
            const formAcara = document.getElementById('formAcara');
            const formAnggaran = document.getElementById('formAnggaran');
            const storeAcara = "{{ route('acara.simpan') }}";

            function formatRibuan(value) {
                const angka = String(value ?? '').replace(/\D/g, '');
                return angka ? new Intl.NumberFormat('id-ID').format(parseInt(angka, 10)) : '';
            }

            document.querySelectorAll('[data-ribuan-acara]').forEach(function(input) {
                if (input.value) {
                    input.value = formatRibuan(input.value);
                }
                input.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                });
            });

            function bukaTambahAcara() {
                formAcara.reset();
                formAcara.action = storeAcara;
                document.getElementById('acaraMethod').value = 'POST';
                document.getElementById('modalAcaraLabel').textContent = 'Tambah acara';
                formAcara.querySelectorAll('.is-invalid').forEach(function(el) {
                    el.classList.remove('is-invalid');
                });
                modalAcara.show();
            }

            ['btnTambahAcara', 'btnTambahAcaraKosong'].forEach(function(id) {
                const tombol = document.getElementById(id);
                if (tombol) {
                    tombol.addEventListener('click', bukaTambahAcara);
                }
            });

            const btnUbahAcara = document.getElementById('btnUbahAcara');
            if (btnUbahAcara) {
                btnUbahAcara.addEventListener('click', function() {
                    formAcara.reset();
                    formAcara.action = this.getAttribute('data-update');
                    document.getElementById('acaraMethod').value = 'PUT';
                    document.getElementById('acaraNama').value = this.getAttribute('data-nama');
                    document.getElementById('acaraTahun').value = this.getAttribute('data-tahun');
                    document.getElementById('acaraStatus').value = this.getAttribute('data-status');
                    document.getElementById('modalAcaraLabel').textContent = 'Ubah acara';
                    modalAcara.show();
                });
            }

            const btnTambahAnggaran = document.getElementById('btnTambahAnggaran');
            if (btnTambahAnggaran && formAnggaran) {
                btnTambahAnggaran.addEventListener('click', function() {
                    formAnggaran.reset();
                    formAnggaran.action = "{{ $acara ? route('acara.anggaran.simpan', $acara) : '' }}";
                    document.getElementById('anggaranMethod').value = 'POST';
                    document.getElementById('modalAnggaranLabel').textContent = 'Tambah item anggaran';
                    formAnggaran.querySelectorAll('.is-invalid').forEach(function(el) {
                        el.classList.remove('is-invalid');
                    });
                });
            }

            document.querySelectorAll('.btn-edit-anggaran').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    if (!formAnggaran) {
                        return;
                    }

                    formAnggaran.reset();
                    formAnggaran.action = this.getAttribute('data-update');
                    document.getElementById('anggaranMethod').value = 'PUT';
                    document.getElementById('anggaranUraian').value = this.getAttribute('data-uraian');
                    document.getElementById('anggaranNominal').value = formatRibuan(this.getAttribute('data-nominal'));
                    document.getElementById('modalAnggaranLabel').textContent = 'Ubah item anggaran';
                    modalAnggaran.show();
                });
            });

            document.querySelectorAll('.tombol-hapus-anggaran').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    const form = this.closest('.form-hapus-anggaran');
                    const info = form.getAttribute('data-info');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus item anggaran?',
                        text: 'Item ' + info + ' akan dihapus dari anggaran acara.',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            const btnHapusAcara = document.getElementById('btnHapusAcara');
            if (btnHapusAcara) {
                btnHapusAcara.addEventListener('click', function() {
                    const form = document.getElementById('formHapusAcara');
                    const info = form.getAttribute('data-info');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus acara?',
                        text: 'Acara ' + info + ' beserta item anggarannya akan dihapus. Acara yang sudah dipakai transaksi tidak dapat dihapus.',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }

            if (state.dataset.sukses) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: state.dataset.sukses,
                    confirmButtonColor: 'var(--ks-green)',
                });
            } else if (state.dataset.galat) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak dapat diproses',
                    text: state.dataset.galat,
                    confirmButtonColor: '#d33',
                });
            }

            if (state.dataset.acaraError === '1' && modalAcara) {
                document.getElementById('modalAcaraLabel').textContent = 'Tambah acara';
                modalAcara.show();
            }

            if (state.dataset.anggaranError === '1' && modalAnggaran) {
                document.getElementById('modalAnggaranLabel').textContent = 'Tambah item anggaran';
                modalAnggaran.show();
            }
        });
    </script>
@endpush
