@extends('layouts.app')

@section('title', 'Master Kategori &mdash; Kas Desa Babatan')

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
              <span class="stat-value">8</span>
              <span class="stat-sub">Kategori aktif</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Kategori pemasukan</span>
              <span class="stat-value is-in">4</span>
              <span class="stat-sub">Uang masuk</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Kategori pengeluaran</span>
              <span class="stat-value is-out">4</span>
              <span class="stat-sub">Uang keluar</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Kategori sensitif</span>
              <span class="stat-value">1</span>
              <span class="stat-sub">Sembunyikan foto nota</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="section-title">Kategori</h2>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalKategori">
              <i class="bi bi-plus-lg"></i>Tambah kategori
            </button>
          </div>

          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Jenis</th>
                  <th class="num">Nominal standar</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Iuran Kematian</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td class="num">Rp5.000 per KK</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Uang Kotak Jumat</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Donasi Masjid</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Iuran Sedekah Bumi</td>
                  <td><span class="badge-soft badge-in">Masuk</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Santunan Kematian <span class="tag">Sensitif</span></td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td class="num">Rp2.000.000</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Perawatan Masjid</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Lampu Jalan</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
                <tr>
                  <td>Konsumsi Sedekah Bumi</td>
                  <td><span class="badge-soft badge-out">Keluar</span></td>
                  <td class="num">&mdash;</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalKategori">Ubah</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <p class="hint mt-3">Kategori bertanda Sensitif otomatis menyembunyikan foto nota dari publik.</p>
        </div>
      </div>
    

  <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title fs-5" id="modalKategoriLabel">Tambah / ubah kategori</h2>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form onsubmit="return false">
          <div class="modal-body d-flex flex-column gap-3">
            <div>
              <label class="form-label-app" for="kategoriNama">Nama kategori</label>
              <input class="form-control" id="kategoriNama" type="text" placeholder="Contoh: Kas Pendidikan">
            </div>
            <div>
              <label class="form-label-app" for="kategoriJenis">Jenis</label>
              <select class="form-select" id="kategoriJenis">
                <option>Masuk</option>
                <option>Keluar</option>
              </select>
            </div>
            <div>
              <label class="form-label-app" for="kategoriNominal">Nominal standar (Rp)</label>
              <input class="form-control" id="kategoriNominal" type="text" placeholder="Kosongkan bila tidak ada standar">
            </div>
            <div class="form-check d-flex align-items-center">
              <input class="form-check-input" id="kategoriSensitif" type="checkbox">
              <label class="form-check-label" for="kategoriSensitif">Kategori sensitif, sembunyikan foto nota dari publik</label>
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
