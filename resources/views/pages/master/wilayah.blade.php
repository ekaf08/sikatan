@extends('layouts.app')

@section('title', 'Master Wilayah RW/RT - Kas Desa Babatan')

@section('topbarTitle', 'Master data')
@section('topbarSubtitle', 'Wilayah RW/RT')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Master data</h1>
        <p class="page-subtitle">Data acuan yang jarang berubah: wilayah, pos dana, dan kategori.</p>
    </div>

    <div class="subnav mb-4">
        <a class="tab-app active" href="{{ route('master-wilayah') }}" aria-current="page">Wilayah RW/RT</a>
        <a class="tab-app" href="{{ route('master-pos-dana') }}">Pos dana</a>
        <a class="tab-app" href="{{ route('master-kategori') }}">Kategori</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total RW</span>
                    <span class="stat-value" id="statRw">{{ number_format($totalRw, 0, ',', '.') }}</span>
                    <span class="stat-sub">Wilayah terdata</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total RT</span>
                    <span class="stat-value" id="statRt">{{ number_format($totalRt, 0, ',', '.') }}</span>
                    <span class="stat-sub">Tersebar di seluruh RW</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total KK</span>
                    <span class="stat-value" id="statKk">{{ number_format($totalKk, 0, ',', '.') }}</span>
                    <span class="stat-sub">Kepala keluarga terdata</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Wilayah RW</h2>
                <button class="btn btn-brand" type="button" id="btnTambahRw">
                    <i class="bi bi-plus-lg"></i>Tambah RW
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelRw">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Wilayah</th>
                            <th>Ketua RW</th>
                            <th class="text-center">Jumlah RT</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Wilayah RT</h2>
                <button class="btn btn-brand" type="button" id="btnTambahRt">
                    <i class="bi bi-plus-lg"></i>Tambah RT
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelRt">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>RW</th>
                            <th>RT</th>
                            <th>Penarik iuran</th>
                            <th class="text-end">Jumlah KK</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Nama penarik hanya catatan, bukan akun. Nomor telepon tidak disimpan.</p>
        </div>
    </div>

    <div class="modal fade" id="modalRw" tabindex="-1" aria-labelledby="modalRwLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalRwLabel">Tambah RW</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formRw" autocomplete="off">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="rwNama">Nama RW</label>
                            <input class="form-control text-uppercase" id="rwNama" name="nama" type="text"
                                placeholder="Contoh: RW 01" data-uppercase required>
                            <div class="invalid-feedback" data-error="nama"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="rwKetua">Nama ketua RW</label>
                            <input class="form-control text-uppercase" id="rwKetua" name="nama_ketua" type="text"
                                placeholder="Opsional" data-uppercase>
                            <div class="invalid-feedback" data-error="nama_ketua"></div>
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

    <div class="modal fade" id="modalRt" tabindex="-1" aria-labelledby="modalRtLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalRtLabel">Tambah RT</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formRt" autocomplete="off">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="rtRw">RW</label>
                            <select class="form-select" id="rtRw" name="rw_id" required>
                                <option value="">Pilih RW</option>
                                @foreach ($daftarRw as $rw)
                                    <option value="{{ $rw->id }}">{{ $rw->nama }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="rw_id"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="rtNomor">Nomor RT</label>
                            <input class="form-control text-uppercase" id="rtNomor" name="nomor" type="text"
                                placeholder="Contoh: 08" data-uppercase required>
                            <div class="invalid-feedback" data-error="nomor"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="rtPenarik">Penarik iuran</label>
                            <input class="form-control text-uppercase" id="rtPenarik" name="nama_penarik" type="text"
                                placeholder="Nama penarik" data-uppercase>
                            <div class="invalid-feedback" data-error="nama_penarik"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="rtKk">Jumlah KK</label>
                            <input class="form-control" id="rtKk" name="jumlah_kk" type="text" inputmode="numeric"
                                placeholder="Contoh: 120.000" value="0" data-ribuan required>
                            <div class="invalid-feedback" data-error="jumlah_kk"></div>
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
            const rwBase = "{{ url('master/wilayah/rw') }}";
            const rtBase = "{{ url('master/wilayah/rt') }}";

            let editRwId = null;
            let editRtId = null;

            const modalRw = new bootstrap.Modal(document.getElementById('modalRw'));
            const modalRt = new bootstrap.Modal(document.getElementById('modalRt'));
            const formRw = document.getElementById('formRw');
            const formRt = document.getElementById('formRt');

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

            document.querySelectorAll('[data-uppercase]').forEach(function(input) {
                input.addEventListener('input', function() {
                    const awal = this.selectionStart;
                    const akhir = this.selectionEnd;
                    this.value = this.value.toUpperCase();
                    this.setSelectionRange(awal, akhir);
                });
            });

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
                $.getJSON("{{ route('wilayah.statistik') }}", function(data) {
                    $('#statRw').text(formatAngka(data.total_rw));
                    $('#statRt').text(formatAngka(data.total_rt));
                    $('#statKk').text(formatAngka(data.total_kk));
                });
            }

            function muatOpsiRw(nilaiTerpilih) {
                $.getJSON("{{ route('wilayah.rw.opsi') }}", function(data) {
                    const select = document.getElementById('rtRw');
                    const nilai = nilaiTerpilih || select.value;

                    select.innerHTML = '<option value="">Pilih RW</option>';
                    data.forEach(function(rw) {
                        const option = document.createElement('option');
                        option.value = rw.id;
                        option.textContent = rw.nama;
                        select.appendChild(option);
                    });

                    if (nilai && data.some(function(rw) {
                            return String(rw.id) === String(nilai);
                        })) {
                        select.value = nilai;
                    }
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

            const tabelRw = $('#tabelRw').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('wilayah.rw.data') }}",
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
                        data: 'nama_ketua',
                        name: 'nama_ketua',
                        render: function(data) {
                            return data ? escapeHtml(data) : '<span class="hint">Belum diisi</span>';
                        }
                    },
                    {
                        data: 'rt_count',
                        name: 'rt_count',
                        className: 'text-center',
                        searchable: false
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

            const tabelRt = $('#tabelRt').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('wilayah.rt.data') }}",
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
                        data: 'rw_nama',
                        name: 'rw_nama'
                    },
                    {
                        data: 'nomor',
                        name: 'nomor',
                        render: function(data) {
                            return 'RT ' + escapeHtml(data);
                        }
                    },
                    {
                        data: 'nama_penarik',
                        name: 'nama_penarik',
                        render: function(data) {
                            return data ? escapeHtml(data) : '<span class="hint">Belum diisi</span>';
                        }
                    },
                    {
                        data: 'jumlah_kk',
                        name: 'jumlah_kk',
                        className: 'text-end',
                        render: function(data) {
                            return formatAngka(data);
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
                    [6, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            $('#btnTambahRw').on('click', function() {
                editRwId = null;
                formRw.reset();
                bersihkanValidasi(formRw);
                document.getElementById('modalRwLabel').textContent = 'Tambah RW';
                modalRw.show();
            });

            $('#tabelRw').on('click', '.btn-edit-rw', function() {
                editRwId = this.getAttribute('data-id');
                formRw.reset();
                bersihkanValidasi(formRw);
                document.getElementById('rwNama').value = this.getAttribute('data-nama');
                document.getElementById('rwKetua').value = this.getAttribute('data-ketua');
                document.getElementById('modalRwLabel').textContent = 'Ubah RW';
                modalRw.show();
            });

            formRw.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi(formRw);

                $.ajax({
                    url: editRwId ? rwBase + '/' + editRwId : rwBase,
                    method: editRwId ? 'PUT' : 'POST',
                    data: {
                        nama: document.getElementById('rwNama').value.trim().toUpperCase(),
                        nama_ketua: document.getElementById('rwKetua').value.trim().toUpperCase(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    modalRw.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                    tabelRw.ajax.reload(null, false);
                    tabelRt.ajax.reload(null, false);
                    muatOpsiRw();
                    muatStatistik();
                }).fail(function(xhr) {
                    tanganiValidasi(formRw, xhr);
                });
            });

            $('#tabelRw').on('click', '.btn-hapus-rw', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus wilayah RW?',
                    text: 'Wilayah ' + nama + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
                        url: rwBase + '/' + id,
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
                        tabelRw.ajax.reload(null, false);
                        tabelRt.ajax.reload(null, false);
                        muatOpsiRw();
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });

            $('#btnTambahRt').on('click', function() {
                editRtId = null;
                formRt.reset();
                bersihkanValidasi(formRt);
                document.getElementById('modalRtLabel').textContent = 'Tambah RT';
                modalRt.show();
            });

            $('#tabelRt').on('click', '.btn-edit-rt', function() {
                editRtId = this.getAttribute('data-id');
                formRt.reset();
                bersihkanValidasi(formRt);
                document.getElementById('rtRw').value = this.getAttribute('data-rw-id');
                document.getElementById('rtNomor').value = this.getAttribute('data-nomor');
                document.getElementById('rtPenarik').value = this.getAttribute('data-penarik');
                document.getElementById('rtKk').value = formatRibuan(this.getAttribute('data-kk'));
                document.getElementById('modalRtLabel').textContent = 'Ubah RT';
                modalRt.show();
            });

            formRt.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi(formRt);

                $.ajax({
                    url: editRtId ? rtBase + '/' + editRtId : rtBase,
                    method: editRtId ? 'PUT' : 'POST',
                    data: {
                        rw_id: document.getElementById('rtRw').value,
                        nomor: document.getElementById('rtNomor').value.trim().toUpperCase(),
                        nama_penarik: document.getElementById('rtPenarik').value.trim().toUpperCase(),
                        jumlah_kk: angkaMentah(document.getElementById('rtKk').value),
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    modalRt.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                    tabelRt.ajax.reload(null, false);
                    tabelRw.ajax.reload(null, false);
                    muatStatistik();
                }).fail(function(xhr) {
                    tanganiValidasi(formRt, xhr);
                });
            });

            $('#tabelRt').on('click', '.btn-hapus-rt', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus wilayah RT?',
                    text: 'Wilayah ' + nama + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
                        url: rtBase + '/' + id,
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
                        tabelRt.ajax.reload(null, false);
                        tabelRw.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });
        });
    </script>
@endpush
