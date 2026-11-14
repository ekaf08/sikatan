@extends('layouts.app')

@section('title', 'Pengeluaran - Kas Desa Babatan')

@section('topbarTitle', 'Pengeluaran')
@section('topbarSubtitle', 'Catat uang keluar')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Catat pengeluaran</h1>
        <p class="page-subtitle">Catat semua uang keluar kas desa beserta buktinya.</p>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total pengeluaran</span>
                    <span class="stat-value is-out" id="statTotalPengeluaran">Rp{{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                    <span class="stat-sub">Seluruh transaksi aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Jumlah transaksi</span>
                    <span class="stat-value" id="statJumlahTransaksi">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</span>
                    <span class="stat-sub">Pengeluaran aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pengeluaran bulan ini</span>
                    <span class="stat-value is-out" id="statBulanIni">Rp{{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pengeluaran terakhir</span>
                    <span class="stat-value" id="statTerakhir" style="font-size:18px">{{ $terakhirKategori ?? '-' }}</span>
                    <span class="stat-sub" id="statTerakhirTanggal">{{ $terakhirTanggal ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Daftar pengeluaran</h2>
                <button class="btn btn-brand" type="button" id="btnTambahKeluar">
                    <i class="bi bi-plus-lg"></i>Tambah pengeluaran
                </button>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelKeluar">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Pos dana</th>
                            <th>Kategori</th>
                            <th>Acara</th>
                            <th class="text-end">Nominal</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Nominal yang berbeda dari standar kategori wajib disertai alasan. Data yang dihapus disimpan sebagai arsip (soft delete).</p>
        </div>
    </div>

    <div class="modal fade" id="modalKeluar" tabindex="-1" aria-labelledby="modalKeluarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalKeluarLabel">Tambah pengeluaran</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formKeluar" autocomplete="off">
                    <div class="modal-body d-flex flex-column gap-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarTanggal">Tanggal</label>
                                <input class="form-control" id="keluarTanggal" name="tanggal" type="date" required>
                                <div class="invalid-feedback" data-error="tanggal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarPos">Dibayar dari pos dana</label>
                                <select class="form-select" id="keluarPos" name="pos_dana_id" required>
                                    <option value="">Pilih pos dana</option>
                                    @foreach ($daftarPos as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error="pos_dana_id"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarKategori">Kategori</label>
                                <select class="form-select" id="keluarKategori" name="kategori_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($daftarKategori as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            data-standar="{{ $kategori->nominal_standar !== null ? (int) $kategori->nominal_standar : '' }}">
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error="kategori_id"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarAcara">Terkait acara (opsional)</label>
                                <select class="form-select" id="keluarAcara" name="acara_id">
                                    <option value="">Tidak terkait acara</option>
                                    @foreach ($daftarAcara as $acara)
                                        <option value="{{ $acara->id }}">{{ $acara->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error="acara_id"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarNominal">Nominal (Rp)</label>
                                <input class="form-control" id="keluarNominal" name="nominal" type="text"
                                    inputmode="numeric" placeholder="Contoh: 3.500.000" data-ribuan required>
                                <span class="hint" id="keluarStandarHint"></span>
                                <div class="invalid-feedback" data-error="nominal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="keluarAlasan">Alasan nominal berbeda</label>
                                <input class="form-control" id="keluarAlasan" name="alasan_selisih" type="text"
                                    placeholder="Wajib bila nominal berbeda dari standar">
                                <div class="invalid-feedback" data-error="alasan_selisih"></div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label-app" for="keluarCatatanPublik">Catatan publik (tampil ke warga)</label>
                            <input class="form-control" id="keluarCatatanPublik" name="catatan_publik" type="text"
                                placeholder="Contoh: Ganti kran tempat wudu yang rusak">
                            <div class="invalid-feedback" data-error="catatan_publik"></div>
                        </div>

                        <div>
                            <label class="form-label-app" for="keluarCatatanInternal">Catatan internal (hanya bendahara dan admin)</label>
                            <textarea class="form-control" id="keluarCatatanInternal" name="catatan_internal" rows="3"
                                placeholder="Catatan yang tidak tampil ke publik"></textarea>
                            <div class="invalid-feedback" data-error="catatan_internal"></div>
                        </div>

                        <div id="wrapperLampiranLama" class="d-none">
                            <span class="form-label-app">Lampiran tersimpan</span>
                            <div class="d-flex flex-wrap gap-2" id="lampiranLama"></div>
                        </div>

                        <div>
                            <label class="form-label-app" for="keluarFoto">Foto nota atau bukti</label>
                            <input class="form-control" id="keluarFoto" name="lampiran[]" type="file" accept="image/*" multiple>
                            <span class="hint">Otomatis dikompres maksimal 500 KB (jpg, png, webp). Bisa pilih beberapa foto.</span>
                            <div class="invalid-feedback" data-error="lampiran"></div>
                            <div class="d-flex flex-wrap gap-2 mt-2" id="previewLampiranBaru"></div>
                        </div>

                        <div class="form-check d-flex align-items-start gap-2">
                            <input class="form-check-input" id="keluarSembunyikan" name="sembunyikan_lampiran" type="checkbox"
                                value="1">
                            <label class="form-check-label" for="keluarSembunyikan">
                                <strong>Sembunyikan foto nota dari halaman publik</strong>
                                <span class="hint d-block">Disarankan untuk santunan. Nominal dan kategori tetap tampil.</span>
                            </label>
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
            const keluarBase = "{{ url('pengeluaran') }}";
            const MAKS_BITA = 500 * 1024;

            let editKeluarId = null;
            let berkasBaru = [];
            let lampiranLama = [];
            let lampiranDihapus = [];

            const modalKeluar = new bootstrap.Modal(document.getElementById('modalKeluar'));
            const formKeluar = document.getElementById('formKeluar');

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

            function standarTerpilih() {
                const select = document.getElementById('keluarKategori');
                const option = select.options[select.selectedIndex];
                const nilai = option ? option.getAttribute('data-standar') : '';
                return nilai ? Number(nilai) : null;
            }

            function perbaruiHintStandar() {
                const standar = standarTerpilih();
                document.getElementById('keluarStandarHint').textContent = standar !== null ?
                    'Standar kategori: Rp' + formatAngka(standar) :
                    '';
            }

            document.getElementById('keluarKategori').addEventListener('change', perbaruiHintStandar);

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

            function toBlobAsync(canvas, kualitas) {
                return new Promise(function(resolve) {
                    canvas.toBlob(resolve, 'image/jpeg', kualitas);
                });
            }

            async function kompresBerkas(berkas) {
                if (!berkas || !berkas.type || berkas.type.indexOf('image/') !== 0) {
                    return null;
                }

                const dataUrl = await new Promise(function(resolve, reject) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        resolve(reader.result);
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(berkas);
                });

                const gambar = await new Promise(function(resolve, reject) {
                    const img = new Image();
                    img.onload = function() {
                        resolve(img);
                    };
                    img.onerror = reject;
                    img.src = dataUrl;
                });

                const maksDim = 1600;
                let lebar = gambar.width;
                let tinggi = gambar.height;

                if (lebar > maksDim || tinggi > maksDim) {
                    const rasio = Math.min(maksDim / lebar, maksDim / tinggi);
                    lebar = Math.round(lebar * rasio);
                    tinggi = Math.round(tinggi * rasio);
                }

                const kanvas = document.createElement('canvas');
                kanvas.width = lebar;
                kanvas.height = tinggi;

                const ctx = kanvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, lebar, tinggi);
                ctx.drawImage(gambar, 0, 0, lebar, tinggi);

                let kualitas = 0.82;
                let blob = await toBlobAsync(kanvas, kualitas);

                while (blob && blob.size > MAKS_BITA && kualitas > 0.3) {
                    kualitas -= 0.1;
                    blob = await toBlobAsync(kanvas, kualitas);
                }

                const nama = (berkas.name || 'gambar').replace(/\.[^.]+$/, '') + '.jpg';

                return new File([blob], nama, {
                    type: 'image/jpeg'
                });
            }

            function renderPreviewBaru() {
                const wadah = document.getElementById('previewLampiranBaru');
                wadah.innerHTML = '';

                berkasBaru.forEach(function(item, index) {
                    const kotak = document.createElement('div');
                    kotak.className = 'position-relative';
                    kotak.innerHTML = '<img src="' + item.url +
                        '" alt="pratinjau" style="width:90px;height:90px;object-fit:cover;border-radius:.5rem;border:1px solid rgba(0,0,0,.15)">' +
                        '<button type="button" class="btn btn-sm btn-danger position-absolute" data-hapus-baru="' + index +
                        '" style="top:-8px;right:-8px;width:24px;height:24px;padding:0;line-height:1;border-radius:50%">&times;</button>';
                    wadah.appendChild(kotak);
                });
            }

            function renderLampiranLama() {
                const wadah = document.getElementById('lampiranLama');
                const pembungkus = document.getElementById('wrapperLampiranLama');
                wadah.innerHTML = '';

                const tampil = lampiranLama.filter(function(item) {
                    return lampiranDihapus.indexOf(item.id) === -1;
                });

                if (tampil.length === 0) {
                    pembungkus.classList.add('d-none');
                    return;
                }

                pembungkus.classList.remove('d-none');

                tampil.forEach(function(item) {
                    const kotak = document.createElement('div');
                    kotak.className = 'position-relative';
                    kotak.innerHTML = '<a href="' + item.url + '" target="_blank" rel="noopener"><img src="' + item
                        .url +
                        '" alt="lampiran" style="width:90px;height:90px;object-fit:cover;border-radius:.5rem;border:1px solid rgba(0,0,0,.15)"></a>' +
                        '<button type="button" class="btn btn-sm btn-danger position-absolute" data-hapus-lama="' + item
                        .id +
                        '" style="top:-8px;right:-8px;width:24px;height:24px;padding:0;line-height:1;border-radius:50%">&times;</button>';
                    wadah.appendChild(kotak);
                });
            }

            document.getElementById('keluarFoto').addEventListener('change', async function() {
                const daftar = Array.from(this.files || []);

                for (const berkas of daftar) {
                    const hasil = await kompresBerkas(berkas);
                    if (hasil) {
                        berkasBaru.push({
                            file: hasil,
                            url: URL.createObjectURL(hasil)
                        });
                    }
                }

                this.value = '';
                renderPreviewBaru();
            });

            document.getElementById('previewLampiranBaru').addEventListener('click', function(event) {
                const tombol = event.target.closest('[data-hapus-baru]');
                if (!tombol) {
                    return;
                }

                const index = Number(tombol.getAttribute('data-hapus-baru'));
                if (berkasBaru[index]) {
                    URL.revokeObjectURL(berkasBaru[index].url);
                    berkasBaru.splice(index, 1);
                }
                renderPreviewBaru();
            });

            document.getElementById('lampiranLama').addEventListener('click', function(event) {
                const tombol = event.target.closest('[data-hapus-lama]');
                if (!tombol) {
                    return;
                }

                const id = Number(tombol.getAttribute('data-hapus-lama'));
                if (lampiranDihapus.indexOf(id) === -1) {
                    lampiranDihapus.push(id);
                }
                renderLampiranLama();
            });

            function resetLampiran() {
                berkasBaru.forEach(function(item) {
                    URL.revokeObjectURL(item.url);
                });
                berkasBaru = [];
                lampiranLama = [];
                lampiranDihapus = [];
                document.getElementById('keluarFoto').value = '';
                renderPreviewBaru();
                renderLampiranLama();
            }

            function muatStatistik() {
                $.getJSON("{{ route('pengeluaran.statistik') }}", function(data) {
                    $('#statTotalPengeluaran').text('Rp' + formatAngka(data.total_pengeluaran));
                    $('#statJumlahTransaksi').text(formatAngka(data.jumlah_transaksi));
                    $('#statBulanIni').text('Rp' + formatAngka(data.pengeluaran_bulan_ini));
                    $('#statTerakhir').text(data.terakhir_kategori || '-');
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

            const domDataTables = "<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>";

            const tabelKeluar = $('#tabelKeluar').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pengeluaran.data') }}",
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
                        data: 'pos_nama',
                        name: 'pos_nama',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return escapeHtml(data);
                        }
                    },
                    {
                        data: 'kategori_nama',
                        name: 'kategori_nama',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<strong>' + escapeHtml(data) + '</strong>';
                        }
                    },
                    {
                        data: 'acara_nama',
                        name: 'acara_nama',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return data && data !== '-' ? escapeHtml(data) :
                                '<span class="hint">&mdash;</span>';
                        }
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
                        data: 'status_label',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            const kelas = row.status === 'batal' ? 'badge-neutral' : 'badge-out';
                            return '<span class="badge-soft ' + kelas + '">' + escapeHtml(data) +
                                '</span>';
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

            $('#btnTambahKeluar').on('click', function() {
                editKeluarId = null;
                formKeluar.reset();
                bersihkanValidasi(formKeluar);
                resetLampiran();
                perbaruiHintStandar();
                document.getElementById('modalKeluarLabel').textContent = 'Tambah pengeluaran';
                modalKeluar.show();
            });

            $('#tabelKeluar').on('click', '.btn-edit-keluar', function() {
                editKeluarId = this.getAttribute('data-id');
                formKeluar.reset();
                bersihkanValidasi(formKeluar);
                resetLampiran();
                document.getElementById('keluarTanggal').value = this.getAttribute('data-tanggal');
                document.getElementById('keluarPos').value = this.getAttribute('data-pos');
                document.getElementById('keluarKategori').value = this.getAttribute('data-kategori');
                document.getElementById('keluarAcara').value = this.getAttribute('data-acara');
                document.getElementById('keluarNominal').value = formatRibuan(this.getAttribute(
                    'data-nominal'));
                document.getElementById('keluarAlasan').value = this.getAttribute('data-alasan');
                document.getElementById('keluarCatatanPublik').value = this.getAttribute(
                    'data-catatan-publik');
                document.getElementById('keluarCatatanInternal').value = this.getAttribute(
                    'data-catatan-internal');
                document.getElementById('keluarSembunyikan').checked = this.getAttribute(
                    'data-sembunyikan') === '1';

                try {
                    lampiranLama = JSON.parse(this.getAttribute('data-lampiran') || '[]');
                } catch (error) {
                    lampiranLama = [];
                }
                renderLampiranLama();

                perbaruiHintStandar();
                document.getElementById('modalKeluarLabel').textContent = 'Ubah pengeluaran';
                modalKeluar.show();
            });

            formKeluar.addEventListener('submit', function(event) {
                event.preventDefault();
                bersihkanValidasi(formKeluar);

                const data = new FormData();
                data.append('tanggal', document.getElementById('keluarTanggal').value);
                data.append('pos_dana_id', document.getElementById('keluarPos').value);
                data.append('kategori_id', document.getElementById('keluarKategori').value);
                data.append('acara_id', document.getElementById('keluarAcara').value);
                data.append('nominal', angkaMentah(document.getElementById('keluarNominal').value));
                data.append('alasan_selisih', document.getElementById('keluarAlasan').value.trim());
                data.append('catatan_publik', document.getElementById('keluarCatatanPublik').value.trim());
                data.append('catatan_internal', document.getElementById('keluarCatatanInternal').value
                    .trim());
                data.append('sembunyikan_lampiran', document.getElementById('keluarSembunyikan').checked ?
                    '1' : '0');

                berkasBaru.forEach(function(item) {
                    data.append('lampiran[]', item.file);
                });

                lampiranDihapus.forEach(function(id) {
                    data.append('hapus_lampiran[]', id);
                });

                if (editKeluarId) {
                    data.append('_method', 'PUT');
                }

                $.ajax({
                    url: editKeluarId ? keluarBase + '/' + editKeluarId : keluarBase,
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).done(function(response) {
                    modalKeluar.hide();
                    resetLampiran();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonColor: 'var(--ks-green)',
                    });
                    tabelKeluar.ajax.reload(null, false);
                    muatStatistik();
                }).fail(function(xhr) {
                    tanganiValidasi(formKeluar, xhr);
                });
            });

            $('#tabelKeluar').on('click', '.btn-hapus-keluar', function() {
                const id = this.getAttribute('data-id');
                const info = this.getAttribute('data-info');

                Swal.fire({
                    icon: 'warning',
                    title: 'Batalkan pengeluaran?',
                    text: 'Pengeluaran ' + info +
                        ' akan dibatalkan (disimpan sebagai arsip, tidak hilang permanen).',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: keluarBase + '/' + id,
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
                        tabelKeluar.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });
        });
    </script>
@endpush
