@extends('layouts.app')

@section('title', 'Daftar Transaksi &mdash; Kas Desa Babatan')

@section('topbarTitle', 'Daftar transaksi')
@section('topbarSubtitle', 'Semua catatan kas')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h1 class="page-title">Daftar transaksi</h1>
          <p class="page-subtitle">Semua pemasukan dan pengeluaran. Transaksi tidak bisa dihapus, hanya dibatalkan dengan alasan.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a class="btn btn-outline-brand" href="{{ route('publik') }}"><i class="bi bi-download"></i>Ekspor</a>
          <a class="btn btn-brand" href="{{ route('pemasukan') }}"><i class="bi bi-plus-lg"></i>Tambah transaksi</a>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-6 col-md-4 col-xl-2">
              <label class="form-label-app" for="filterJenis">Jenis</label>
              <select class="form-select" id="filterJenis">
                <option>Semua</option>
                <option>Masuk</option>
                <option>Keluar</option>
              </select>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <label class="form-label-app" for="filterPos">Pos dana</label>
              <select class="form-select" id="filterPos">
                <option>Semua pos</option>
                <option>Kas Kematian</option>
                <option>Kas Masjid</option>
                <option>Kas Sedekah Bumi</option>
                <option>Kas Lain-lain</option>
              </select>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
              <label class="form-label-app" for="filterStatus">Status</label>
              <select class="form-select" id="filterStatus">
                <option>Semua</option>
                <option>Aktif</option>
                <option>Batal</option>
              </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
              <label class="form-label-app" for="filterDari">Dari</label>
              <input class="form-control" id="filterDari" type="date" value="2026-11-01">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
              <label class="form-label-app" for="filterSampai">Sampai</label>
              <input class="form-control" id="filterSampai" type="date" value="2026-11-12">
            </div>
            <div class="col-12 col-md-6 col-xl-2 d-flex gap-2">
              <button class="btn btn-brand flex-fill" type="button">Terapkan</button>
              <button class="btn btn-outline-brand" type="button" aria-label="Reset filter"><i class="bi bi-arrow-counterclockwise"></i></button>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <h2 class="section-title">Riwayat transaksi</h2>
            <span class="hint">Menampilkan 8 dari 248 transaksi</span>
          </div>

          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jenis</th>
                  <th>Pos dana</th>
                  <th>Keterangan</th>
                  <th class="num">Nominal</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>09 Nov</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td>Kas Lain-lain</td>
                  <td>Pembelian alat kebersihan</td>
                  <td class="num">Rp125.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr class="is-cancelled">
                  <td>08 Nov</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td>Kas Lain-lain</td>
                  <td>
                    Pembelian alat kebersihan
                    <br><span class="hint">Dibatalkan: salah input nominal, dicatat ulang</span>
                  </td>
                  <td class="num amount">Rp250.000</td>
                  <td><span class="badge-soft badge-neutral">Batal</span></td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                    </div>
                  </td>
                </tr>
                <tr class="is-highlight">
                  <td>08 Nov</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td>Kas Kematian</td>
                  <td>Setoran iuran kematian, RW 01 RT 03</td>
                  <td class="num">Rp165.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>08 Nov</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td>Kas Kematian</td>
                  <td>Setoran iuran kematian, RW 02 RT 01</td>
                  <td class="num">Rp185.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>06 Nov</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td>Kas Sedekah Bumi</td>
                  <td>Uang muka konsumsi sedekah bumi</td>
                  <td class="num">Rp4.500.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>06 Nov</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td>Kas Masjid</td>
                  <td>Uang kotak Jumat</td>
                  <td class="num">Rp850.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>05 Nov</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td>Kas Sedekah Bumi</td>
                  <td>Uang muka sewa tenda dan pengeras suara</td>
                  <td class="num">Rp2.500.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>30 Okt</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td>Kas Masjid</td>
                  <td>Ganti kran tempat wudu yang rusak</td>
                  <td class="num">Rp350.000</td>
                  <td>Aktif</td>
                  <td>
                    <div class="actions">
                      <a class="btn-ghost" href="#">Lihat</a>
                      <a class="btn-ghost is-danger" href="#">Batalkan</a>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <nav class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3" aria-label="Navigasi halaman">
            <span class="hint">Halaman 1 dari 31</span>
            <ul class="pagination mb-0">
              <li class="page-item disabled"><a class="page-link" href="#" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></a></li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
          </nav>
        </div>
      </div>
@endsection
