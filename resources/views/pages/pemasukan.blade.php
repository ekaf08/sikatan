@extends('layouts.app')

@section('title', 'Pemasukan &mdash; Kas Desa Babatan')

@section('topbarTitle', 'Pemasukan')
@section('topbarSubtitle', 'Catat uang masuk')

@section('content')
<div class="mb-4">
        <h1 class="page-title">Catat pemasukan</h1>
        <p class="page-subtitle">Pilih jenis pemasukan, formulir menyesuaikan.</p>
      </div>

      <ul class="nav nav-pills flex-wrap gap-2 mb-4" id="tabsPemasukan" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="tab-app active" id="tab-setoran" data-bs-toggle="pill" data-bs-target="#panel-setoran" type="button" role="tab" aria-controls="panel-setoran" aria-selected="true">
            Setoran iuran kematian
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="tab-app" id="tab-jumat" data-bs-toggle="pill" data-bs-target="#panel-jumat" type="button" role="tab" aria-controls="panel-jumat" aria-selected="false">
            Uang kotak Jumat
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="tab-app" id="tab-donasi" data-bs-toggle="pill" data-bs-target="#panel-donasi" type="button" role="tab" aria-controls="panel-donasi" aria-selected="false">
            Donasi
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="tab-app" id="tab-lain" data-bs-toggle="pill" data-bs-target="#panel-lain" type="button" role="tab" aria-controls="panel-lain" aria-selected="false">
            Lain-lain
          </button>
        </li>
      </ul>

      <div class="row g-4">
        <div class="col-12 col-xl-8">
          <div class="tab-content">

            <div class="tab-pane fade show active" id="panel-setoran" role="tabpanel" aria-labelledby="tab-setoran">
              <form class="card" onsubmit="return false">
                <div class="card-body d-flex flex-column gap-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-4">
                      <label class="form-label-app" for="setoranTanggal">Tanggal setoran</label>
                      <input class="form-control" id="setoranTanggal" type="date" value="2026-11-08">
                    </div>
                    <div class="col-6 col-md-4">
                      <label class="form-label-app" for="setoranRw">RW</label>
                      <select class="form-select" id="setoranRw">
                        <option>RW 01</option>
                        <option>RW 02</option>
                      </select>
                    </div>
                    <div class="col-6 col-md-4">
                      <label class="form-label-app" for="setoranRt">RT</label>
                      <select class="form-select" id="setoranRt">
                        <option>RT 04</option>
                        <option>RT 01</option>
                        <option>RT 02</option>
                        <option>RT 03</option>
                      </select>
                    </div>
                  </div>

                  <div data-stepper data-price="5000" data-min="0" data-max="41">
                    <span class="form-label-app">Jumlah KK yang membayar</span>
                    <div class="stepper mb-3">
                      <button type="button" class="btn-step" data-step-minus aria-label="Kurangi jumlah KK">
                        <i class="bi bi-dash-lg"></i>
                      </button>
                      <span class="stepper-value" data-step-value>41</span>
                      <button type="button" class="btn-step" data-step-plus aria-label="Tambah jumlah KK">
                        <i class="bi bi-plus-lg"></i>
                      </button>
                      <span class="hint">dari 41 KK terdata di RT 04</span>
                    </div>

                    <div class="row g-3">
                      <div class="col-12 col-md-6">
                        <span class="form-label-app">Nominal hitungan otomatis</span>
                        <div class="form-static" data-step-total>Rp205.000</div>
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label-app" for="uangDiterima">Uang yang diterima (Rp)</label>
                        <input class="form-control" id="uangDiterima" data-step-input type="text" value="205.000">
                        <span class="hint">Ubah bila uang yang diterima berbeda dari hitungan.</span>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="form-label-app" for="setoranCatatanPublik">Catatan publik</label>
                    <input class="form-control" id="setoranCatatanPublik" type="text" value="Setoran iuran kematian">
                  </div>

                  <div>
                    <label class="form-label-app" for="setoranCatatanInternal">Catatan internal (hanya bendahara dan admin)</label>
                    <textarea class="form-control" id="setoranCatatanInternal" placeholder="Contoh: 3 KK menyusul minggu depan"></textarea>
                  </div>

                  <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan pemasukan</button>
                    <button class="btn btn-outline-brand" type="button"><i class="bi bi-plus-lg"></i>Simpan dan tambah lagi</button>
                  </div>
                </div>
              </form>
            </div>

            <div class="tab-pane fade" id="panel-jumat" role="tabpanel" aria-labelledby="tab-jumat">
              <form class="card" onsubmit="return false">
                <div class="card-body d-flex flex-column gap-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="jumatTanggal">Tanggal</label>
                      <input class="form-control" id="jumatTanggal" type="date" value="2026-11-06">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="jumatTotal">Total uang kotak (Rp)</label>
                      <input class="form-control" id="jumatTotal" type="text" value="850.000">
                    </div>
                  </div>

                  <div>
                    <label class="form-label-app" for="jumatCatatan">Catatan publik</label>
                    <input class="form-control" id="jumatCatatan" type="text" value="Uang kotak setelah salat Jumat">
                  </div>

                  <p class="hint">Dicatat sebagai satu angka total ke Kas Masjid, tanpa rincian per orang.</p>

                  <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan pemasukan</button>
                    <button class="btn btn-outline-brand" type="button"><i class="bi bi-plus-lg"></i>Simpan dan tambah lagi</button>
                  </div>
                </div>
              </form>
            </div>

            <div class="tab-pane fade" id="panel-donasi" role="tabpanel" aria-labelledby="tab-donasi">
              <form class="card" onsubmit="return false">
                <div class="card-body d-flex flex-column gap-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="donasiTanggal">Tanggal</label>
                      <input class="form-control" id="donasiTanggal" type="date" value="2026-11-10">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="donasiNominal">Nominal donasi (Rp)</label>
                      <input class="form-control" id="donasiNominal" type="text" value="1.000.000">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="donasiNama">Nama donatur</label>
                      <input class="form-control" id="donasiNama" type="text" value="Fulan (contoh)">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="donasiTujuan">Tujuan donasi</label>
                      <input class="form-control" id="donasiTujuan" type="text" value="Perawatan masjid">
                    </div>
                  </div>

                  <div>
                    <label class="form-label-app" for="donasiTampil">Tampil di halaman publik sebagai</label>
                    <select class="form-select" id="donasiTampil">
                      <option>Nama asli donatur</option>
                      <option>Hamba Allah</option>
                    </select>
                  </div>

                  <div>
                    <label class="form-label-app" for="donasiCatatan">Catatan publik</label>
                    <input class="form-control" id="donasiCatatan" type="text" value="Donasi untuk perawatan masjid">
                  </div>

                  <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan pemasukan</button>
                    <button class="btn btn-outline-brand" type="button"><i class="bi bi-plus-lg"></i>Simpan dan tambah lagi</button>
                  </div>
                </div>
              </form>
            </div>

            <div class="tab-pane fade" id="panel-lain" role="tabpanel" aria-labelledby="tab-lain">
              <form class="card" onsubmit="return false">
                <div class="card-body d-flex flex-column gap-4">
                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="lainTanggal">Tanggal</label>
                      <input class="form-control" id="lainTanggal" type="date" value="2026-11-12">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="lainNominal">Nominal (Rp)</label>
                      <input class="form-control" id="lainNominal" type="text" value="750.000">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="lainPos">Masuk ke pos dana</label>
                      <select class="form-select" id="lainPos">
                        <option>Kas Lain-lain</option>
                        <option>Kas Masjid</option>
                        <option>Kas Sedekah Bumi</option>
                      </select>
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label-app" for="lainKategori">Kategori</label>
                      <select class="form-select" id="lainKategori">
                        <option>Pemasukan Lain-lain</option>
                        <option>Sewa aset desa</option>
                        <option>Bunga bank</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label class="form-label-app" for="lainCatatan">Catatan publik</label>
                    <input class="form-control" id="lainCatatan" type="text" value="Hasil sewa terop warga">
                  </div>

                  <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan pemasukan</button>
                    <button class="btn btn-outline-brand" type="button"><i class="bi bi-plus-lg"></i>Simpan dan tambah lagi</button>
                  </div>
                </div>
              </form>
            </div>

          </div>
        </div>

        <div class="col-12 col-xl-4">
          <aside class="card">
            <div class="card-body">
              <h2 class="section-title mb-2">Pemasukan terakhir</h2>
              <div class="recent-row">
                <span>08 Nov &middot; Setoran RT 03, RW 01</span>
                <strong>Rp165.000</strong>
              </div>
              <div class="recent-row">
                <span>08 Nov &middot; Setoran RT 01, RW 02</span>
                <strong>Rp185.000</strong>
              </div>
              <div class="recent-row">
                <span>06 Nov &middot; Uang kotak Jumat</span>
                <strong>Rp850.000</strong>
              </div>
              <div class="recent-row">
                <span>30 Okt &middot; Donasi hamba Allah</span>
                <strong>Rp500.000</strong>
              </div>
              <p class="hint mt-3">Salah input? Buka Daftar transaksi dan batalkan dengan alasan, jangan dihapus.</p>
            </div>
          </aside>
        </div>
      </div>
@endsection
