@extends('layouts.app')

@section('title', 'Master Kategori - Kas Desa Babatan')

@section('topbarTitle', 'Master data')
@section('topbarSubtitle', 'Kategori transaksi')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Master data</h1>
        <p class="page-subtitle">Data acuan yang jarang berubah: wilayah, pos dana, dan kategori.</p>
    </div>

    <div class="subnav mb-4">
        <a class="tab-app" href="{{ route('master-wilayah') }}">Wilayah RW/RT</a>
        <a class="tab-app" href="{{ route('master-pos-dana') }}">Pos dana</a>
        <a class="tab-app active" href="{{ route('master-kategori') }}" aria-current="page">Kategori</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total kategori</span>
                    <span class="stat-value" id="statTotalKategori">{{ number_format($totalKategori, 0, ',', '.') }}</span>
                    <span class="stat-sub">Kategori aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Kategori pemasukan</span>
                    <span class="stat-value is-in" id="statTotalMasuk">{{ number_format($totalMasuk, 0, ',', '.') }}</span>
                    <span class="stat-sub">Uang masuk</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Kategori pengeluaran</span>
                    <span class="stat-value is-out" id="statTotalKeluar">{{ number_format($totalKeluar, 0, ',', '.') }}</span>
                    <span class="stat-sub">Uang keluar</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Kategori sensitif</span>
                    <span class="stat-value" id="statTotalSensitif">{{ number_format($totalSensitif, 0, ',', '.') }}</span>
                    <span class="stat-sub">Sembunyikan foto nota</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Kategori</h2>
                <button class="btn btn-brand" type="button" id="btnTambahKategori">
                    <i class="bi bi-plus-lg"></i>Tambah kategori
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelKategori">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th class="text-end">Nominal standar</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Kategori bertanda Sensitif otomatis menyembunyikan foto nota dari publik.</p>
        </div>
    </div>

    <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalKategoriLabel">Tambah kategori</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formKategori" autocomplete="off">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="kategoriNama">Nama kategori</label>
                            <input class="form-control" id="kategoriNama" name="nama" type="text"
                                placeholder="Contoh: Kas Pendidikan" required>
                            <div class="invalid-feedback" data-error="nama"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="kategoriJenis">Jenis</label>
                            <select class="form-select" id="kategoriJenis" name="jenis" required>
                                <option value="">Pilih jenis</option>
                                <option value="masuk">Masuk</option>
                                <option value="keluar">Keluar</option>
                            </select>
                            <div class="invalid-feedback" data-error="jenis"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="kategoriNominal">Nominal standar (Rp)</label>
                            <input class="form-control" id="kategoriNominal" name="nominal_standar" type="text"
                                inputmode="numeric" placeholder="Kosongkan bila tidak ada standar" data-ribuan>
                            <div class="invalid-feedback" data-error="nominal_standar"></div>
                        </div>
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input" id="kategoriSensitif" name="sensitif" type="checkbox"
                                value="1">
                            <label class="form-check-label" for="kategoriSensitif">Kategori sensitif, sembunyikan foto
                                nota dari publik</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-brand" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.DataTable) {
                return;
            }

            const $ = window.jQuery;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
            const kategoriBase = "{{ url('master/kategori') }}";

            let editKategoriId = null;

            const modalKategori = new bootstrap.Modal(document.getElementById('modalKategori'));
            const formKategori = document.getElementById('formKategori');

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    } [character];
                });
            }

            function formatAngka(value) {
                return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
            }

            function formatRibuan(value) {
                const angka = String(value ?? '').replace(/\D/g, '');
                return angka ? new Intl.NumberFormat('id-ID').format(parseInt(angka, 10)) : '';
            }

            function angkaMentah(value) {
                return String(value ?? '').replace(/\D/g, '');
            }

            document.querySelectorAll('[data-ribuan]').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                });
            });

            function bersihkanValidasi(form) {
                form.querySelectorAll('.is-invalid').forEach(function(element) {
                    element.classList.remove('is-invalid');
                });
                form.querySelectorAll('[data-error]').forEach(function(element) {
                    element.textContent = '';
                });
            }

            function tampilkanGagal(xhr) {
                const pesan = xhr.responseJSON && xhr.responseJSON.message ?
                    xhr.responseJSON.message :
                    'Terjadi kesalahan. Silakan coba lagi.';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: pesan,
                    confirmButtonColor: '#d33',
                });
            }

            function tanganiValidasi(form, xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let pesanPertama = null;

                    Object.keys(errors).forEach(function(field) {
                        const pesan = errors[field][0];
                        pesanPertama = pesanPertama || pesan;

                        const input = form.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                        }

                        const feedback = form.querySelector('[data-error="' + field + '"]');
                        if (feedback) {
                            feedback.textContent = pesan;
                        }
                    });

                    if (pesanPertama && !form.querySelector('.is-invalid')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Periksa kembali',
                            text: pesanPertama,
                            confirmButtonColor: 'var(--ks-green)',
                        });
                    }

                    return;
                }

                tampilkanGagal(xhr);
            }

            function muatStatistik() {
                $.getJSON("{{ route('kategori.statistik') }}", function(data) {
                    $('#statTotalKategori').text(formatAngka(data.total_kategori));
                    $('#statTotalMasuk').text(formatAngka(data.total_masuk));
                    $('#statTotalKeluar').text(formatAngka(data.total_keluar));
                    $('#statTotalSensitif').text(formatAngka(data.total_sensitif));
                });
            }

            const bahasaDataTables = {
                search: 'Cari:',
                lengthMenu: 'Tampil _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                processing: 'Memuat...',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Lanjut',
                    previous: 'Kembali'
                }
            };

            const domDataTables = "<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>";

            const tabelKategori = $('#tabelKategori').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kategori.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        render: function(data) {
                            return '<strong>' + escapeHtml(data) + '</strong>';
                        }
                    },
                    {
                        data: 'jenis_label',
                        name: 'jenis_label',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const kelas = row.jenis === 'masuk' ? 'badge-in' : 'badge-out';
                            return '<span class="badge-soft ' + kelas + '">' + escapeHtml(data) +
                                '</span>';
                        }
                    },
                    {
                        data: 'nominal_format',
                        name: 'nominal_standar',
                        className: 'text-end',
                        render: function(data) {
                            return data ? 'Rp' + data : '<span class="hint">&mdash;</span>';
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: true,
                        searchable: false,
                        visible: false
                    }
                ],
                order: [
                    [5, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            $('#btnTambahKategori').on('click', function() {
                editKategoriId = null;
                formKategori.reset();
                bersihkanValidasi(formKategori);
                document.getElementById('modalKategoriLabel').textContent = 'Tambah kategori';
                modalKategori.show();
            });

            $('#tabelKategori').on('click', '.btn-edit-kategori', function() {
                editKategoriId = this.getAttribute('data-id');
                formKategori.reset();
                bersihkanValidasi(formKategori);
                document.getElementById('kategoriNama').value = this.getAttribute('data-nama');
                document.getElementById('kategoriJenis').value = this.getAttribute('data-jenis');
                document.getElementById('kategoriNominal').value = formatRibuan(this.getAttribute(
                    'data-nominal'));
                document.getElementById('kategoriSensitif').checked = this.getAttribute('data-sensitif') ===
                    '1';
                document.getElementById('modalKategoriLabel').textContent = 'Ubah kategori';
                modalKategori.show();
            });

            formKategori.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi(formKategori);

                $.ajax({
                    url: editKategoriId ? kategoriBase + '/' + editKategoriId : kategoriBase,
                    method: editKategoriId ? 'PUT' : 'POST',
                    data: {
                        nama: document.getElementById('kategoriNama').value.trim(),
                        jenis: document.getElementById('kategoriJenis').value,
                        nominal_standar: angkaMentah(document.getElementById('kategoriNominal')
                            .value),
                        sensitif: document.getElementById('kategoriSensitif').checked ? 1 : 0,
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    modalKategori.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                    tabelKategori.ajax.reload(null, false);
                    muatStatistik();
                }).fail(function(xhr) {
                    tanganiValidasi(formKategori, xhr);
                });
            });

            $('#tabelKategori').on('click', '.btn-hapus-kategori', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus kategori?',
                    text: 'Kategori ' + nama + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: kategoriBase + '/' + id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    }).done(function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            confirmButtonColor: 'var(--ks-green)',
                        });
                        tabelKategori.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });
        });
    </script>
@endpush
