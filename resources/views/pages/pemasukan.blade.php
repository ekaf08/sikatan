@extends('layouts.app')

@section('title', 'Pemasukan - Kas Desa Babatan')

@section('topbarTitle', 'Pemasukan')
@section('topbarSubtitle', 'Catat uang masuk')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Catat pemasukan</h1>
        <p class="page-subtitle">Pilih jenis pemasukan, formulir menyesuaikan.</p>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total pemasukan</span>
                    <span class="stat-value is-in" id="statTotalPemasukan">Rp{{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                    <span class="stat-sub">Seluruh transaksi aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Jumlah transaksi</span>
                    <span class="stat-value" id="statJumlahTransaksi">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</span>
                    <span class="stat-sub">Pemasukan aktif</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pemasukan bulan ini</span>
                    <span class="stat-value is-in" id="statBulanIni">Rp{{ number_format($pemasukanBulanIni, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ now()->locale('id')->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pemasukan terakhir</span>
                    <span class="stat-value" id="statTerakhir" style="font-size:18px">{{ $terakhirKategori ?? '-' }}</span>
                    <span class="stat-sub" id="statTerakhirTanggal">{{ $terakhirTanggal ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="section-title mb-2">Formulir pemasukan</h2>
            <p class="hint mb-3">Pilih tab sesuai jenis pemasukan. Tab donasi dan lain-lain dapat disertai lampiran foto.</p>

            <ul class="nav nav-pills flex-wrap gap-2 mb-4" id="tabsPemasukan" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="tab-app active" id="tab-setoran" data-bs-toggle="pill" data-bs-target="#panel-setoran"
                        data-tipe="setoran" type="button" role="tab" aria-controls="panel-setoran" aria-selected="true">
                        Setoran iuran kematian
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="tab-app" id="tab-jumat" data-bs-toggle="pill" data-bs-target="#panel-jumat"
                        data-tipe="jumat" type="button" role="tab" aria-controls="panel-jumat" aria-selected="false">
                        Uang kotak Jumat
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="tab-app" id="tab-donasi" data-bs-toggle="pill" data-bs-target="#panel-donasi"
                        data-tipe="donasi" type="button" role="tab" aria-controls="panel-donasi" aria-selected="false">
                        Donasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="tab-app" id="tab-lain" data-bs-toggle="pill" data-bs-target="#panel-lain"
                        data-tipe="lain" type="button" role="tab" aria-controls="panel-lain" aria-selected="false">
                        Lain-lain
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="panel-setoran" role="tabpanel" aria-labelledby="tab-setoran">
                    <form id="form-setoran" class="form-pemasukan" data-tipe="setoran" autocomplete="off">
                        <input name="tipe" type="hidden" value="setoran">

                        <div class="alert alert-warning py-2 d-none" data-edit-info>
                            Sedang mengubah data pemasukan. Klik <strong>Bersihkan</strong> untuk membatalkan.
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label-app" for="setoranTanggal">Tanggal setoran</label>
                                <input class="form-control" id="setoranTanggal" name="tanggal" type="date" required>
                                <div class="invalid-feedback" data-error="tanggal"></div>
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label-app" for="setoranRt">RT penyetor</label>
                                <select class="form-select" id="setoranRt" name="rt_id" required>
                                    <option value="">Pilih RT</option>
                                    @foreach ($daftarRt as $rt)
                                        <option value="{{ $rt->id }}" data-jumlah="{{ $rt->jumlah_kk }}">
                                            {{ $rt->rw?->nama ?? '-' }} &middot; RT {{ $rt->nomor }} ({{ $rt->jumlah_kk }} KK)
                                        </option>
                                    @endforeach
                                </select>
                                <span class="hint" id="setoranHint"></span>
                                <div class="invalid-feedback" data-error="rt_id"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label-app" for="setoranKk">Jumlah KK yang membayar</label>
                                <input class="form-control" id="setoranKk" name="jumlah_kk_bayar" type="number" min="1"
                                    step="1" placeholder="0">
                                <div class="invalid-feedback" data-error="jumlah_kk_bayar"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label-app">Nominal hitungan otomatis</label>
                                <div class="form-static" id="setoranTotal">Rp0</div>
                                <span class="hint">Iuran Rp{{ number_format($iuranPerKk, 0, ',', '.') }} per KK.</span>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label-app" for="setoranNominal">Uang yang diterima (Rp)</label>
                                <input class="form-control" id="setoranNominal" name="nominal" type="text"
                                    inputmode="numeric" placeholder="Contoh: 205.000" data-ribuan required>
                                <span class="hint">Ubah bila uang yang diterima berbeda dari hitungan.</span>
                                <div class="invalid-feedback" data-error="nominal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="setoranCatatanPublik">Catatan publik</label>
                                <input class="form-control" id="setoranCatatanPublik" name="catatan_publik" type="text"
                                    placeholder="Contoh: Setoran iuran kematian">
                                <div class="invalid-feedback" data-error="catatan_publik"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="setoranCatatanInternal">Catatan internal</label>
                                <input class="form-control" id="setoranCatatanInternal" name="catatan_internal" type="text"
                                    placeholder="Contoh: 3 KK menyusul minggu depan">
                                <div class="invalid-feedback" data-error="catatan_internal"></div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 mt-3 border-top">
                            <button class="btn btn-brand" type="submit">
                                <i class="bi bi-check-lg"></i><span data-submit-text>Simpan pemasukan</span>
                            </button>
                            <button class="btn btn-outline-brand" type="button" data-reset>Bersihkan</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="panel-jumat" role="tabpanel" aria-labelledby="tab-jumat">
                    <form id="form-jumat" class="form-pemasukan" data-tipe="jumat" autocomplete="off">
                        <input name="tipe" type="hidden" value="jumat">

                        <div class="alert alert-warning py-2 d-none" data-edit-info>
                            Sedang mengubah data pemasukan. Klik <strong>Bersihkan</strong> untuk membatalkan.
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="jumatTanggal">Tanggal</label>
                                <input class="form-control" id="jumatTanggal" name="tanggal" type="date" required>
                                <div class="invalid-feedback" data-error="tanggal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="jumatNominal">Total uang kotak (Rp)</label>
                                <input class="form-control" id="jumatNominal" name="nominal" type="text"
                                    inputmode="numeric" placeholder="Contoh: 850.000" data-ribuan required>
                                <div class="invalid-feedback" data-error="nominal"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label-app" for="jumatCatatanPublik">Catatan publik</label>
                                <input class="form-control" id="jumatCatatanPublik" name="catatan_publik" type="text"
                                    placeholder="Contoh: Uang kotak setelah salat Jumat">
                                <div class="invalid-feedback" data-error="catatan_publik"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label-app" for="jumatCatatanInternal">Catatan internal</label>
                                <textarea class="form-control" id="jumatCatatanInternal" name="catatan_internal" rows="2"
                                    placeholder="Catatan yang tidak tampil ke publik"></textarea>
                                <div class="invalid-feedback" data-error="catatan_internal"></div>
                            </div>
                        </div>

                        <p class="hint mt-3">Dicatat sebagai satu angka total ke Kas Masjid, tanpa rincian per orang.</p>

                        <div class="d-flex flex-wrap gap-2 pt-3 mt-3 border-top">
                            <button class="btn btn-brand" type="submit">
                                <i class="bi bi-check-lg"></i><span data-submit-text>Simpan pemasukan</span>
                            </button>
                            <button class="btn btn-outline-brand" type="button" data-reset>Bersihkan</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="panel-donasi" role="tabpanel" aria-labelledby="tab-donasi">
                    <form id="form-donasi" class="form-pemasukan" data-tipe="donasi" autocomplete="off">
                        <input name="tipe" type="hidden" value="donasi">

                        <div class="alert alert-warning py-2 d-none" data-edit-info>
                            Sedang mengubah data pemasukan. Klik <strong>Bersihkan</strong> untuk membatalkan.
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiTanggal">Tanggal</label>
                                <input class="form-control" id="donasiTanggal" name="tanggal" type="date" required>
                                <div class="invalid-feedback" data-error="tanggal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiNominal">Nominal donasi (Rp)</label>
                                <input class="form-control" id="donasiNominal" name="nominal" type="text"
                                    inputmode="numeric" placeholder="Contoh: 1.000.000" data-ribuan required>
                                <div class="invalid-feedback" data-error="nominal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiNama">Nama donatur</label>
                                <input class="form-control" id="donasiNama" name="nama_donatur" type="text"
                                    placeholder="Contoh: Fulan">
                                <div class="invalid-feedback" data-error="nama_donatur"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiTujuan">Tujuan donasi</label>
                                <input class="form-control" id="donasiTujuan" name="tujuan" type="text"
                                    placeholder="Contoh: Perawatan masjid">
                                <div class="invalid-feedback" data-error="tujuan"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" id="donasiAnonim" name="donatur_anonim" type="checkbox"
                                        value="1">
                                    <label class="form-check-label" for="donasiAnonim">
                                        Tampilkan sebagai <strong>Hamba Allah</strong> di halaman publik
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiCatatanPublik">Catatan publik</label>
                                <input class="form-control" id="donasiCatatanPublik" name="catatan_publik" type="text"
                                    placeholder="Contoh: Donasi untuk perawatan masjid">
                                <div class="invalid-feedback" data-error="catatan_publik"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="donasiCatatanInternal">Catatan internal</label>
                                <input class="form-control" id="donasiCatatanInternal" name="catatan_internal" type="text"
                                    placeholder="Catatan yang tidak tampil ke publik">
                                <div class="invalid-feedback" data-error="catatan_internal"></div>
                            </div>
                        </div>

                        <div class="mt-3 d-none" data-wrapper-lama>
                            <span class="form-label-app">Lampiran tersimpan</span>
                            <div class="d-flex flex-wrap gap-2" data-preview-lama></div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label-app" for="donasiFoto">Lampiran foto</label>
                            <input class="form-control" id="donasiFoto" name="lampiran[]" type="file" accept="image/*"
                                multiple data-input-foto>
                            <span class="hint">Otomatis dikompres maksimal 500 KB (jpg, png, webp). Bisa pilih beberapa foto.</span>
                            <div class="invalid-feedback" data-error="lampiran"></div>
                            <div class="d-flex flex-wrap gap-2 mt-2" data-preview-baru></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 mt-3 border-top">
                            <button class="btn btn-brand" type="submit">
                                <i class="bi bi-check-lg"></i><span data-submit-text>Simpan pemasukan</span>
                            </button>
                            <button class="btn btn-outline-brand" type="button" data-reset>Bersihkan</button>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="panel-lain" role="tabpanel" aria-labelledby="tab-lain">
                    <form id="form-lain" class="form-pemasukan" data-tipe="lain" autocomplete="off">
                        <input name="tipe" type="hidden" value="lain">

                        <div class="alert alert-warning py-2 d-none" data-edit-info>
                            Sedang mengubah data pemasukan. Klik <strong>Bersihkan</strong> untuk membatalkan.
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainTanggal">Tanggal</label>
                                <input class="form-control" id="lainTanggal" name="tanggal" type="date" required>
                                <div class="invalid-feedback" data-error="tanggal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainNominal">Nominal (Rp)</label>
                                <input class="form-control" id="lainNominal" name="nominal" type="text"
                                    inputmode="numeric" placeholder="Contoh: 750.000" data-ribuan required>
                                <div class="invalid-feedback" data-error="nominal"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainPos">Masuk ke pos dana</label>
                                <select class="form-select" id="lainPos" name="pos_dana_id" required>
                                    <option value="">Pilih pos dana</option>
                                    @foreach ($daftarPos as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error="pos_dana_id"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainKategori">Kategori</label>
                                <select class="form-select" id="lainKategori" name="kategori_id" required>
                                    <option value="">Pilih kategori</option>
                                    @foreach ($daftarKategoriLain as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" data-error="kategori_id"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainCatatanPublik">Catatan publik</label>
                                <input class="form-control" id="lainCatatanPublik" name="catatan_publik" type="text"
                                    placeholder="Contoh: Hasil sewa terop warga">
                                <div class="invalid-feedback" data-error="catatan_publik"></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label-app" for="lainCatatanInternal">Catatan internal</label>
                                <input class="form-control" id="lainCatatanInternal" name="catatan_internal" type="text"
                                    placeholder="Catatan yang tidak tampil ke publik">
                                <div class="invalid-feedback" data-error="catatan_internal"></div>
                            </div>
                        </div>

                        <div class="mt-3 d-none" data-wrapper-lama>
                            <span class="form-label-app">Lampiran tersimpan</span>
                            <div class="d-flex flex-wrap gap-2" data-preview-lama></div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label-app" for="lainFoto">Lampiran foto</label>
                            <input class="form-control" id="lainFoto" name="lampiran[]" type="file" accept="image/*"
                                multiple data-input-foto>
                            <span class="hint">Otomatis dikompres maksimal 500 KB (jpg, png, webp). Bisa pilih beberapa foto.</span>
                            <div class="invalid-feedback" data-error="lampiran"></div>
                            <div class="d-flex flex-wrap gap-2 mt-2" data-preview-baru></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 mt-3 border-top">
                            <button class="btn btn-brand" type="submit">
                                <i class="bi bi-check-lg"></i><span data-submit-text>Simpan pemasukan</span>
                            </button>
                            <button class="btn btn-outline-brand" type="button" data-reset>Bersihkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h2 class="section-title">Daftar pemasukan</h2>
            </div>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelMasuk">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Pos dana</th>
                            <th>Kategori</th>
                            <th>Rincian</th>
                            <th class="text-end">Nominal</th>
                            <th>Lampiran</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Data yang dihapus disimpan sebagai arsip (soft delete). Lampiran foto hanya tersedia untuk tab donasi dan lain-lain.</p>
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
            const masukBase = "{{ url('pemasukan') }}";
            const iuranPerKk = {{ (int) $iuranPerKk }};
            const MAKS_BITA = 500 * 1024;

            const formPerTipe = {};
            const statePerForm = new Map();
            let editId = null;
            let editTipe = null;

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

            function stateFor(form) {
                if (!statePerForm.has(form)) {
                    statePerForm.set(form, {
                        baru: [],
                        lama: [],
                        dihapus: []
                    });
                }
                return statePerForm.get(form);
            }

            function ambilFeedback(form, field) {
                return form.querySelector('[data-error="' + field + '"]');
            }

            function bersihkanValidasi(form) {
                form.querySelectorAll('.is-invalid').forEach(function(element) {
                    element.classList.remove('is-invalid');
                });
                form.querySelectorAll('[data-error]').forEach(function(element) {
                    element.textContent = '';
                    element.classList.remove('d-block');
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

                        const dasar = field.split('.')[0];
                        const input = form.querySelector('[name="' + dasar + '"]') ||
                            form.querySelector('[name="' + dasar + '[]"]');
                        if (input) {
                            input.classList.add('is-invalid');
                        }

                        const feedback = ambilFeedback(form, dasar);
                        if (feedback) {
                            feedback.textContent = pesan;
                            feedback.classList.add('d-block');
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

            function renderPreviewBaru(form) {
                const wadah = form.querySelector('[data-preview-baru]');
                if (!wadah) {
                    return;
                }

                const state = stateFor(form);
                wadah.innerHTML = '';

                state.baru.forEach(function(item, index) {
                    const kotak = document.createElement('div');
                    kotak.className = 'position-relative';
                    kotak.innerHTML = '<img src="' + item.url +
                        '" alt="pratinjau" style="width:90px;height:90px;object-fit:cover;border-radius:.5rem;border:1px solid rgba(0,0,0,.15)">' +
                        '<button type="button" class="btn btn-sm btn-danger position-absolute" data-hapus-baru="' + index +
                        '" style="top:-8px;right:-8px;width:24px;height:24px;padding:0;line-height:1;border-radius:50%">&times;</button>';
                    wadah.appendChild(kotak);
                });
            }

            function renderLampiranLama(form) {
                const wadah = form.querySelector('[data-preview-lama]');
                const pembungkus = form.querySelector('[data-wrapper-lama]');
                if (!wadah || !pembungkus) {
                    return;
                }

                const state = stateFor(form);
                wadah.innerHTML = '';

                const tampil = state.lama.filter(function(item) {
                    return state.dihapus.indexOf(item.id) === -1;
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

            function resetLampiran(form) {
                const state = stateFor(form);
                state.baru.forEach(function(item) {
                    URL.revokeObjectURL(item.url);
                });
                state.baru = [];
                state.lama = [];
                state.dihapus = [];
                const input = form.querySelector('[data-input-foto]');
                if (input) {
                    input.value = '';
                }
                renderPreviewBaru(form);
                renderLampiranLama(form);
            }

            function setEditMode(form, aktif) {
                const info = form.querySelector('[data-edit-info]');
                const submitText = form.querySelector('[data-submit-text]');
                if (info) {
                    info.classList.toggle('d-none', !aktif);
                }
                if (submitText) {
                    submitText.textContent = aktif ? 'Perbarui pemasukan' : 'Simpan pemasukan';
                }
            }

            function batalUbah() {
                editId = null;
                editTipe = null;
                Object.keys(formPerTipe).forEach(function(tipe) {
                    const form = formPerTipe[tipe];
                    form.reset();
                    bersihkanValidasi(form);
                    resetLampiran(form);
                    setEditMode(form, false);
                });
            }

            function bukaTab(tipe) {
                const tombol = document.getElementById('tab-' + tipe);
                if (tombol) {
                    bootstrap.Tab.getOrCreateInstance(tombol).show();
                }
            }

            document.querySelectorAll('[data-ribuan]').forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                });
            });

            const rtSelect = document.getElementById('setoranRt');
            const kkInput = document.getElementById('setoranKk');
            const nominalSetoran = document.getElementById('setoranNominal');
            const totalSetoran = document.getElementById('setoranTotal');
            const hintSetoran = document.getElementById('setoranHint');

            function perbaruiSetoran(isiNominal) {
                const option = rtSelect.options[rtSelect.selectedIndex];
                const maksKk = option ? parseInt(option.getAttribute('data-jumlah'), 10) : 0;
                const kk = parseInt(kkInput.value, 10) || 0;

                totalSetoran.textContent = 'Rp' + formatAngka(kk * iuranPerKk);
                hintSetoran.textContent = maksKk ? ('dari ' + maksKk + ' KK terdata di RT ini') : '';

                if (isiNominal) {
                    nominalSetoran.value = kk > 0 ? formatRibuan(kk * iuranPerKk) : '';
                }
            }

            rtSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const maksKk = option ? parseInt(option.getAttribute('data-jumlah'), 10) : 0;
                if (maksKk) {
                    kkInput.value = maksKk;
                }
                perbaruiSetoran(true);
            });

            kkInput.addEventListener('input', function() {
                perbaruiSetoran(true);
            });

            document.querySelectorAll('.form-pemasukan').forEach(function(form) {
                const tipe = form.getAttribute('data-tipe');
                formPerTipe[tipe] = form;

                const inputFoto = form.querySelector('[data-input-foto]');
                if (inputFoto) {
                    inputFoto.addEventListener('change', async function() {
                        const daftar = Array.from(this.files || []);

                        for (const berkas of daftar) {
                            const hasil = await kompresBerkas(berkas);
                            if (hasil) {
                                stateFor(form).baru.push({
                                    file: hasil,
                                    url: URL.createObjectURL(hasil)
                                });
                            }
                        }

                        this.value = '';
                        renderPreviewBaru(form);
                    });
                }

                const previewBaru = form.querySelector('[data-preview-baru]');
                if (previewBaru) {
                    previewBaru.addEventListener('click', function(event) {
                        const tombol = event.target.closest('[data-hapus-baru]');
                        if (!tombol) {
                            return;
                        }

                        const state = stateFor(form);
                        const index = Number(tombol.getAttribute('data-hapus-baru'));
                        if (state.baru[index]) {
                            URL.revokeObjectURL(state.baru[index].url);
                            state.baru.splice(index, 1);
                        }
                        renderPreviewBaru(form);
                    });
                }

                const previewLama = form.querySelector('[data-preview-lama]');
                if (previewLama) {
                    previewLama.addEventListener('click', function(event) {
                        const tombol = event.target.closest('[data-hapus-lama]');
                        if (!tombol) {
                            return;
                        }

                        const state = stateFor(form);
                        const id = Number(tombol.getAttribute('data-hapus-lama'));
                        if (state.dihapus.indexOf(id) === -1) {
                            state.dihapus.push(id);
                        }
                        renderLampiranLama(form);
                    });
                }

                form.querySelector('[data-reset]')?.addEventListener('click', function() {
                    batalUbah();
                });

                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    bersihkanValidasi(form);

                    const tipeForm = form.getAttribute('data-tipe');
                    const data = new FormData(form);
                    const state = stateFor(form);

                    data.set('nominal', angkaMentah(form.querySelector('[name="nominal"]').value));

                    state.baru.forEach(function(item) {
                        data.append('lampiran[]', item.file);
                    });

                    state.dihapus.forEach(function(id) {
                        data.append('hapus_lampiran[]', id);
                    });

                    const sedangUbah = editId !== null && editTipe === tipeForm;

                    if (sedangUbah) {
                        data.append('_method', 'PUT');
                    }

                    $.ajax({
                        url: sedangUbah ? masukBase + '/' + editId : masukBase,
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
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
                        batalUbah();
                        tabelMasuk.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(function(xhr) {
                        tanganiValidasi(form, xhr);
                    });
                });
            });

            document.querySelectorAll('#tabsPemasukan button[data-bs-toggle="pill"]').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    const tipeTarget = tombol.getAttribute('data-tipe');
                    if (editId !== null && editTipe !== tipeTarget) {
                        batalUbah();
                    }
                });
            });

            function muatStatistik() {
                $.getJSON("{{ route('pemasukan.statistik') }}", function(data) {
                    $('#statTotalPemasukan').text('Rp' + formatAngka(data.total_pemasukan));
                    $('#statJumlahTransaksi').text(formatAngka(data.jumlah_transaksi));
                    $('#statBulanIni').text('Rp' + formatAngka(data.pemasukan_bulan_ini));
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

            const tabelMasuk = $('#tabelMasuk').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pemasukan.data') }}",
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
                        data: 'tipe_label',
                        name: 'tipe_label',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.tipe === 'donasi') {
                                return '<span class="badge-soft badge-in">' + escapeHtml(data) + '</span>';
                            }
                            return escapeHtml(data);
                        }
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
                        data: 'rincian',
                        name: 'rincian',
                        orderable: false,
                        searchable: false
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
                        data: 'lampiran',
                        name: 'lampiran',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_label',
                        name: 'status',
                        orderable: false,
                        searchable: false
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

            $('#tabelMasuk').on('click', '.btn-edit-masuk', function() {
                const tombol = this;
                const tipe = tombol.getAttribute('data-tipe');
                const form = formPerTipe[tipe];

                if (!form) {
                    return;
                }

                batalUbah();
                editId = tombol.getAttribute('data-id');
                editTipe = tipe;

                form.querySelector('[name="tanggal"]').value = tombol.getAttribute('data-tanggal') || '';
                form.querySelector('[name="nominal"]').value = formatRibuan(tombol.getAttribute('data-nominal'));
                form.querySelector('[name="catatan_publik"]').value = tombol.getAttribute('data-catatan-publik') || '';
                form.querySelector('[name="catatan_internal"]').value = tombol.getAttribute('data-catatan-internal') || '';

                if (tipe === 'setoran') {
                    const rt = tombol.getAttribute('data-rt');
                    const kk = tombol.getAttribute('data-jumlah-kk');
                    rtSelect.value = rt || '';
                    kkInput.value = kk || '';
                    perbaruiSetoran(false);
                    nominalSetoran.value = formatRibuan(tombol.getAttribute('data-nominal'));
                }

                if (tipe === 'donasi') {
                    form.querySelector('[name="nama_donatur"]').value = tombol.getAttribute('data-nama-donatur') || '';
                    form.querySelector('[name="tujuan"]').value = tombol.getAttribute('data-tujuan') || '';
                    form.querySelector('[name="donatur_anonim"]').checked = tombol.getAttribute('data-anonim') === '1';
                }

                if (tipe === 'lain') {
                    form.querySelector('[name="pos_dana_id"]').value = tombol.getAttribute('data-pos') || '';
                    form.querySelector('[name="kategori_id"]').value = tombol.getAttribute('data-kategori') || '';
                }

                if (tipe === 'donasi' || tipe === 'lain') {
                    const state = stateFor(form);
                    try {
                        state.lama = JSON.parse(tombol.getAttribute('data-lampiran') || '[]');
                    } catch (error) {
                        state.lama = [];
                    }
                    renderLampiranLama(form);
                }

                setEditMode(form, true);
                bukaTab(tipe);
                form.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            $('#tabelMasuk').on('click', '.btn-hapus-masuk', function() {
                const id = this.getAttribute('data-id');
                const info = this.getAttribute('data-info');

                Swal.fire({
                    icon: 'warning',
                    title: 'Batalkan pemasukan?',
                    text: 'Pemasukan ' + info +
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
                        url: masukBase + '/' + id,
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
                        batalUbah();
                        tabelMasuk.ajax.reload(null, false);
                        muatStatistik();
                    }).fail(tampilkanGagal);
                });
            });
        });
    </script>
@endpush
