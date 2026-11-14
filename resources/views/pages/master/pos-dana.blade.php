@extends('layouts.app')

@section('title', 'Master Pos Dana - Kas Desa Babatan')

@section('topbarTitle', 'Master data')
@section('topbarSubtitle', 'Pos dana')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Master data</h1>
        <p class="page-subtitle">Data acuan yang jarang berubah: wilayah, pos dana, dan kategori.</p>
    </div>

    <div class="subnav mb-4">
        <a class="tab-app" href="{{ route('master-wilayah') }}">Wilayah RW/RT</a>
        <a class="tab-app active" href="{{ route('master-pos-dana') }}" aria-current="page">Pos dana</a>
        <a class="tab-app" href="{{ route('master-kategori') }}">Kategori</a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Jumlah pos dana</span>
                    <span class="stat-value" id="statJumlahPos">{{ number_format($totalPos, 0, ',', '.') }}</span>
                    <span class="stat-sub">Pos aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total saldo berjalan</span>
                    <span class="stat-value" id="statSaldoBerjalan">Rp{{ number_format($totalSaldoBerjalan, 0, ',', '.') }}</span>
                    <span class="stat-sub">Dari seluruh pos</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Saldo awal</span>
                    <span class="stat-value" id="statSaldoAwal">Rp{{ number_format($totalSaldoAwal, 0, ',', '.') }}</span>
                    <span class="stat-sub">Saat mulai memakai aplikasi</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pos terakhir diubah</span>
                    <span class="stat-value" id="statPosTerakhir" style="font-size:18px">{{ $posTerakhirNama ?? '-' }}</span>
                    <span class="stat-sub" id="statPosTerakhirTanggal">{{ $posTerakhirTanggal ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Pos dana</h2>
                <button class="btn btn-brand" type="button" id="btnTambahPos">
                    <i class="bi bi-plus-lg"></i>Tambah pos
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelPos">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Pos</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th class="text-end">Saldo Awal</th>
                            <th class="text-end">Saldo Berjalan</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Saldo awal dicatat sekali saat mulai memakai aplikasi. Saldo berjalan dihitung otomatis dari transaksi aktif.</p>
        </div>
    </div>

    <div class="modal fade" id="modalPos" tabindex="-1" aria-labelledby="modalPosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalPosLabel">Tambah pos dana</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formPos" autocomplete="off">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="posNama">Nama pos</label>
                            <input class="form-control text-uppercase" id="posNama" name="nama" type="text"
                                placeholder="Contoh: KAS PENDIDIKAN" data-uppercase required>
                            <div class="invalid-feedback" data-error="nama"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="posJenis">Jenis</label>
                            <select class="form-select" id="posJenis" name="jenis" required>
                                <option value="">Pilih jenis</option>
                                @foreach ($daftarJenis as $nilai => $label)
                                    <option value="{{ $nilai }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="jenis"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="posSaldo">Saldo awal (Rp)</label>
                            <input class="form-control" id="posSaldo" name="saldo_awal" type="text"
                                inputmode="numeric" placeholder="Contoh: 120.000" value="0" data-ribuan required>
                            <div class="invalid-feedback" data-error="saldo_awal"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="posTanggal">Tanggal saldo awal</label>
                            <input class="form-control" id="posTanggal" name="tanggal_saldo_awal" type="date">
                            <div class="invalid-feedback" data-error="tanggal_saldo_awal"></div>
                        </div>
                        <div>
                            <label class="form-label-app" for="posKeterangan">Keterangan</label>
                            <textarea class="form-control" id="posKeterangan" name="keterangan" rows="3"
                                placeholder="Untuk apa pos dana ini digunakan"></textarea>
                            <div class="invalid-feedback" data-error="keterangan"></div>
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
            const posBase = "{{ url('master/pos-dana') }}";

            let editPosId = null;

            const modalPos = new bootstrap.Modal(document.getElementById('modalPos'));
            const formPos = document.getElementById('formPos');

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
                $.getJSON("{{ route('pos-dana.statistik') }}", function(data) {
                    $('#statJumlahPos').text(formatAngka(data.total_pos));
                    $('#statSaldoBerjalan').text('Rp' + formatAngka(data.total_saldo_berjalan));
                    $('#statSaldoAwal').text('Rp' + formatAngka(data.total_saldo_awal));
                    $('#statPosTerakhir').text(data.pos_terakhir_nama || '-');
                    $('#statPosTerakhirTanggal').text(data.pos_terakhir_tanggal || '-');
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

            const tabelPos = $('#tabelPos').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pos-dana.data') }}",
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
                        data: 'keterangan',
                        name: 'keterangan',
                        render: function(data) {
                            return data ? escapeHtml(data) : '<span class="hint">Belum diisi</span>';
                        }
                    },
                    {
                        data: 'jenis_label',
                        name: 'jenis_label',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="badge-soft badge-neutral">' + escapeHtml(data) + '</span>';
                        }
                    },
                    {
                        data: 'saldo_awal',
                        name: 'saldo_awal',
                        className: 'text-end',
                        searchable: false,
                        render: function(data) {
                            return 'Rp' + formatAngka(data);
                        }
                    },
                    {
                        data: 'saldo_berjalan',
                        name: 'saldo_berjalan',
                        className: 'text-end',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return 'Rp' + formatAngka(data);
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
                    [7, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            $('#btnTambahPos').on('click', function() {
                editPosId = null;
                formPos.reset();
                bersihkanValidasi(formPos);
                document.getElementById('modalPosLabel').textContent = 'Tambah pos dana';
                modalPos.show();
            });

            $('#tabelPos').on('click', '.btn-edit-pos', function() {
                editPosId = this.getAttribute('data-id');
                formPos.reset();
                bersihkanValidasi(formPos);
                document.getElementById('posNama').value = this.getAttribute('data-nama');
                document.getElementById('posJenis').value = this.getAttribute('data-jenis');
                document.getElementById('posSaldo').value = formatRibuan(this.getAttribute('data-saldo'));
                document.getElementById('posTanggal').value = this.getAttribute('data-tanggal');
                document.getElementById('posKeterangan').value = this.getAttribute('data-keterangan');
                document.getElementById('modalPosLabel').textContent = 'Ubah pos dana';
                modalPos.show();
            });

            formPos.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi(formPos);

                $.ajax({
                    url: editPosId ? posBase + '/' + editPosId : posBase,
                    method: editPosId ? 'PUT' : 'POST',
                    data: {
                        nama: document.getElementById('posNama').value.trim().toUpperCase(),
                        jenis: document.getElementById('posJenis').value,
                        saldo_awal: angkaMentah(document.getElementById('posSaldo').value) || '0',
                        tanggal_saldo_awal: document.getElementById('posTanggal').value,
                        keterangan: document.getElementById('posKeterangan').value.trim(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    modalPos.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                    tabelPos.ajax.reload(null, false);
                    muatStatistik();
                }).fail(function(xhr) {
                    tanganiValidasi(formPos, xhr);
                });
            });

            $('#tabelPos').on('click', '.btn-hapus-pos', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus pos dana?',
                    text: 'Pos ' + nama + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
                        url: posBase + '/' + id,
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
                        tabelPos.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });
        });
    </script>
@endpush
