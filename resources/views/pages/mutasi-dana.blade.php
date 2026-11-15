@extends('layouts.app')

@section('title', 'Mutasi Dana — Kas Desa Babatan')

@section('topbarTitle', 'Mutasi dana')
@section('topbarSubtitle', 'Pindah saldo antar pos')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Mutasi dana antar pos</h1>
        <p class="page-subtitle">Memindahkan saldo dari satu pos ke pos lain. Tidak dihitung sebagai pemasukan atau
            pengeluaran.</p>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total mutasi</span>
                    <span class="stat-value" id="statTotalMutasi">Rp{{ number_format($totalMutasi, 0, ',', '.') }}</span>
                    <span class="stat-sub">Seluruh mutasi aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Jumlah mutasi</span>
                    <span class="stat-value" id="statJumlahMutasi">{{ number_format($jumlahMutasi, 0, ',', '.') }}</span>
                    <span class="stat-sub">Transaksi perpindahan saldo</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Mutasi bulan ini</span>
                    <span class="stat-value" id="statBulanIni">Rp{{ number_format($mutasiBulanIni, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Mutasi terakhir</span>
                    <span class="stat-value" id="statTerakhir" style="font-size:18px">
                        {{ $terakhirMutasi ? ($terakhirMutasi->dariPos?->nama ?? '-') . ' → ' . ($terakhirMutasi->kePos?->nama ?? '-') : '-' }}
                    </span>
                    <span class="stat-sub" id="statTerakhirTanggal">
                        {{ $terakhirMutasi?->tanggal?->copy()->locale('id')->translatedFormat('d M Y') ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <form class="card" id="formMutasi" autocomplete="off">
                <input id="mutasiId" type="hidden">
                <div class="card-body d-flex flex-column gap-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h2 class="section-title mb-0" id="mutasiFormTitle">Catat mutasi dana</h2>
                        <span class="badge-soft badge-neutral" id="mutasiModeLabel">Baru</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label-app" for="mutasiDari">Dari pos</label>
                            <select class="form-select" id="mutasiDari" name="dari_pos_id" required>
                                @foreach ($daftarPos as $pos)
                                    <option value="{{ $pos->id }}" data-saldo="{{ (float) $pos->saldo_terkini }}">
                                        {{ $pos->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="dari_pos_id"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-app" for="mutasiKe">Ke pos</label>
                            <select class="form-select" id="mutasiKe" name="ke_pos_id" required>
                                @foreach ($daftarPos as $pos)
                                    <option value="{{ $pos->id }}" data-saldo="{{ (float) $pos->saldo_terkini }}">
                                        {{ $pos->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="ke_pos_id"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-app" for="mutasiTanggal">Tanggal</label>
                            <input class="form-control" id="mutasiTanggal" name="tanggal" type="date"
                                value="{{ now()->toDateString() }}" required>
                            <div class="invalid-feedback" data-error="tanggal"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-app" for="mutasiNominal">Nominal (Rp)</label>
                            <input class="form-control" id="mutasiNominal" name="nominal" type="text"
                                inputmode="numeric" placeholder="Contoh: 1.000.000" data-ribuan required>
                            <div class="invalid-feedback" data-error="nominal"></div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label-app" for="mutasiKeterangan">Keterangan</label>
                        <input class="form-control" id="mutasiKeterangan" name="keterangan" type="text"
                            placeholder="Contoh: Tambahan dana awal persiapan sedekah bumi">
                        <div class="invalid-feedback" data-error="keterangan"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="balance-preview">
                                <span class="balance-title" id="mutasiDariLabel">-</span>
                                <span><span id="mutasiDariSebelum">Rp0</span> &rarr;
                                    <strong id="mutasiDariSesudah">Rp0</strong></span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="balance-preview">
                                <span class="balance-title" id="mutasiKeLabel">-</span>
                                <span><span id="mutasiKeSebelum">Rp0</span> &rarr;
                                    <strong id="mutasiKeSesudah">Rp0</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                        <button class="btn btn-brand" id="btnSimpanMutasi" type="submit">
                            <i class="bi bi-check-lg"></i>Simpan mutasi
                        </button>
                        <button class="btn btn-outline-brand" id="btnBatalMutasi" type="button">Batal</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-xl-5">
            <div class="d-flex flex-column gap-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-2">
                            <h2 class="section-title mb-0">Saldo tiap pos</h2>
                            <span class="hint">Diperbarui otomatis</span>
                        </div>
                        @foreach ($daftarPos as $pos)
                            <div class="recent-row">
                                <span>{{ $pos->nama }}</span>
                                <strong id="saldoPos-{{ $pos->id }}">Rp{{ number_format((float) $pos->saldo_terkini, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="notice notice-amber">
                    Dana Kematian sebaiknya tidak dipindahkan ke pos lain tanpa persetujuan pengurus. Sistem tetap
                    mencatat setiap mutasi di log audit.
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-2">
                <h2 class="section-title mb-0">Riwayat mutasi</h2>
                <span class="hint">Mutasi tidak mengubah total saldo kas desa</span>
            </div>
            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelMutasi">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th>Keterangan</th>
                            <th>Dibuat oleh</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <p class="hint mt-3">Nominal tidak boleh melebihi saldo pos asal. Data yang dihapus disimpan sebagai arsip
                (soft delete).</p>
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
            const mutasiBase = "{{ url('mutasi-dana') }}";
            const tanggalHariIni = "{{ now()->toDateString() }}";

            const formMutasi = document.getElementById('formMutasi');
            const selectDari = document.getElementById('mutasiDari');
            const selectKe = document.getElementById('mutasiKe');
            const inputNominal = document.getElementById('mutasiNominal');
            let editId = null;

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

            function formatRupiah(value) {
                return 'Rp' + formatAngka(value);
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

            function bersihkanValidasi() {
                formMutasi.querySelectorAll('.is-invalid').forEach(function(element) {
                    element.classList.remove('is-invalid');
                });
                formMutasi.querySelectorAll('[data-error]').forEach(function(element) {
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

            function tanganiValidasi(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let pesanPertama = null;

                    Object.keys(errors).forEach(function(field) {
                        const pesan = errors[field][0];
                        pesanPertama = pesanPertama || pesan;

                        const input = formMutasi.querySelector('[name="' + field + '"]');
                        if (input) {
                            input.classList.add('is-invalid');
                        }

                        const feedback = formMutasi.querySelector('[data-error="' + field + '"]');
                        if (feedback) {
                            feedback.textContent = pesan;
                        }
                    });

                    if (pesanPertama) {
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

            function opsiTerpilih(select) {
                return select.options[select.selectedIndex] || null;
            }

            function pastikanPosBerbeda(sumber) {
                if (selectDari.value !== selectKe.value) {
                    return;
                }

                const target = sumber === selectDari ? selectKe : selectDari;
                const jumlah = target.options.length;

                if (jumlah < 2) {
                    return;
                }

                target.selectedIndex = (target.selectedIndex + 1) % jumlah;
            }

            function renderPreview() {
                const dari = opsiTerpilih(selectDari);
                const ke = opsiTerpilih(selectKe);
                const nominal = Number(angkaMentah(inputNominal.value)) || 0;
                const saldoDari = Number(dari?.getAttribute('data-saldo') || 0);
                const saldoKe = Number(ke?.getAttribute('data-saldo') || 0);

                document.getElementById('mutasiDariLabel').textContent = dari?.text.trim() || '-';
                document.getElementById('mutasiKeLabel').textContent = ke?.text.trim() || '-';
                document.getElementById('mutasiDariSebelum').textContent = formatRupiah(saldoDari);
                document.getElementById('mutasiDariSesudah').textContent = formatRupiah(saldoDari - nominal);
                document.getElementById('mutasiKeSebelum').textContent = formatRupiah(saldoKe);
                document.getElementById('mutasiKeSesudah').textContent = formatRupiah(saldoKe + nominal);
            }

            function terapkanSaldo(daftar) {
                (daftar || []).forEach(function(item) {
                    const label = document.getElementById('saldoPos-' + item.id);
                    if (label) {
                        label.textContent = formatRupiah(item.saldo);
                    }

                    [selectDari, selectKe].forEach(function(select) {
                        const option = select.querySelector('option[value="' + item.id + '"]');
                        if (option) {
                            option.setAttribute('data-saldo', item.saldo);
                        }
                    });
                });

                renderPreview();
            }

            function muatSaldo() {
                $.getJSON("{{ route('mutasi-dana.saldo') }}", function(response) {
                    terapkanSaldo(response.data);
                });
            }

            function muatStatistik() {
                $.getJSON("{{ route('mutasi-dana.statistik') }}", function(data) {
                    $('#statTotalMutasi').text(formatRupiah(data.total_mutasi));
                    $('#statJumlahMutasi').text(formatAngka(data.jumlah_mutasi));
                    $('#statBulanIni').text(formatRupiah(data.mutasi_bulan_ini));
                    $('#statTerakhir').text(data.terakhir || '-');
                    $('#statTerakhirTanggal').text(data.terakhir_tanggal || '-');
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

            const domDataTables =
                "<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>";

            const tabelMutasi = $('#tabelMutasi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('mutasi-dana.data') }}",
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
                        data: 'tanggal_format',
                        name: 'tanggal',
                        searchable: false
                    },
                    {
                        data: 'dari_nama',
                        name: 'dari_nama'
                    },
                    {
                        data: 'ke_nama',
                        name: 'ke_nama'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false
                    },
                    {
                        data: 'dibuat_oleh_nama',
                        name: 'dibuat_oleh_nama',
                        orderable: false
                    },
                    {
                        data: 'nominal_format',
                        name: 'nominal',
                        className: 'text-end',
                        searchable: false,
                        render: function(data) {
                            return 'Rp' + data;
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            function resetForm() {
                editId = null;
                formMutasi.reset();
                document.getElementById('mutasiId').value = '';
                document.getElementById('mutasiTanggal').value = tanggalHariIni;
                selectDari.selectedIndex = 0;
                selectKe.selectedIndex = selectKe.options.length > 1 ? 1 : 0;
                bersihkanValidasi();
                document.getElementById('mutasiFormTitle').textContent = 'Catat mutasi dana';
                document.getElementById('mutasiModeLabel').textContent = 'Baru';
                document.getElementById('btnSimpanMutasi').innerHTML =
                    '<i class="bi bi-check-lg"></i>Simpan mutasi';
                renderPreview();
            }

            selectDari.addEventListener('change', function() {
                pastikanPosBerbeda(selectDari);
                renderPreview();
            });

            selectKe.addEventListener('change', function() {
                pastikanPosBerbeda(selectKe);
                renderPreview();
            });

            inputNominal.addEventListener('input', renderPreview);

            document.getElementById('btnBatalMutasi').addEventListener('click', resetForm);

            $('#tabelMutasi').on('click', '.btn-ubah-mutasi', function() {
                editId = this.getAttribute('data-id');
                bersihkanValidasi();
                document.getElementById('mutasiId').value = editId;
                document.getElementById('mutasiTanggal').value = this.getAttribute('data-tanggal');
                selectDari.value = this.getAttribute('data-dari');
                selectKe.value = this.getAttribute('data-ke');
                inputNominal.value = formatRibuan(this.getAttribute('data-nominal'));
                document.getElementById('mutasiKeterangan').value = this.getAttribute('data-keterangan') || '';
                document.getElementById('mutasiFormTitle').textContent = 'Ubah mutasi dana';
                document.getElementById('mutasiModeLabel').textContent = 'Ubah';
                document.getElementById('btnSimpanMutasi').innerHTML =
                    '<i class="bi bi-check-lg"></i>Simpan perubahan';
                renderPreview();
                formMutasi.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            formMutasi.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi();

                const data = new FormData();
                data.append('dari_pos_id', selectDari.value);
                data.append('ke_pos_id', selectKe.value);
                data.append('tanggal', document.getElementById('mutasiTanggal').value);
                data.append('nominal', angkaMentah(inputNominal.value));
                data.append('keterangan', document.getElementById('mutasiKeterangan').value.trim());

                if (editId) {
                    data.append('_method', 'PUT');
                }

                const tombol = document.getElementById('btnSimpanMutasi');
                tombol.disabled = true;

                $.ajax({
                    url: editId ? mutasiBase + '/' + editId : mutasiBase,
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    resetForm();
                    tabelMutasi.ajax.reload(null, false);
                    terapkanSaldo(response.saldo);
                    muatStatistik();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                }).fail(tanganiValidasi).always(function() {
                    tombol.disabled = false;
                });
            });

            $('#tabelMutasi').on('click', '.btn-hapus-mutasi', function() {
                const id = this.getAttribute('data-id');
                const info = this.getAttribute('data-info');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus mutasi dana?',
                    text: 'Mutasi ' + info +
                        ' akan dihapus (disimpan sebagai arsip, tidak hilang permanen).',
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
                        url: mutasiBase + '/' + id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    }).done(function(response) {
                        tabelMutasi.ajax.reload(null, false);
                        terapkanSaldo(response.saldo);
                        muatStatistik();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            confirmButtonColor: 'var(--ks-green)',
                        });
                    }).fail(tampilkanGagal);
                });
            });

            resetForm();
        });
    </script>
@endpush
