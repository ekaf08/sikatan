@extends('layouts.app')

@section('title', 'Master Pos Dana &mdash; Kas Desa Babatan')

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
              <span class="stat-value">4</span>
              <span class="stat-sub">Pos aktif</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Total saldo berjalan</span>
              <span class="stat-value">Rp64.645.000</span>
              <span class="stat-sub">Dari seluruh pos</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Saldo awal</span>
              <span class="stat-value">Rp17.200.000</span>
              <span class="stat-sub">Saat mulai memakai aplikasi</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Pos terakhir diubah</span>
              <span class="stat-value" style="font-size:18px">Kas Masjid</span>
              <span class="stat-sub">30 Okt 2026</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="section-title">Pos dana</h2>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalPos">
              <i class="bi bi-plus-lg"></i>Tambah pos
            </button>
          </div>

          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Pos</th>
                  <th>Keterangan</th>
                  <th class="num">Saldo awal</th>
                  <th class="num">Saldo berjalan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Kas Kematian</strong></td>
                  <td>Iuran mingguan warga untuk santunan</td>
                  <td class="num">Rp12.500.000</td>
                  <td class="num">Rp35.240.000</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalPos">Ubah</a></td>
                </tr>
                <tr>
                  <td><strong>Kas Masjid</strong></td>
                  <td>Uang kotak Jumat dan donasi</td>
                  <td class="num">Rp3.200.000</td>
                  <td class="num">Rp8.910.000</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalPos">Ubah</a></td>
                </tr>
                <tr>
                  <td><strong>Kas Sedekah Bumi</strong></td>
                  <td>Anggaran acara tahunan sedekah bumi</td>
                  <td class="num">Rp0</td>
                  <td class="num">Rp18.350.000</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalPos">Ubah</a></td>
                </tr>
                <tr>
                  <td><strong>Kas Lain-lain</strong></td>
                  <td>Perawatan kecil dan pemasukan lain</td>
                  <td class="num">Rp1.500.000</td>
                  <td class="num">Rp2.145.000</td>
                  <td><a class="btn-ghost" href="#" data-bs-toggle="modal" data-bs-target="#modalPos">Ubah</a></td>
                </tr>
              </tbody>
            </table>
          </div>

          <p class="hint mt-3">Saldo awal dicatat sekali saat mulai memakai aplikasi. Saldo berjalan dihitung otomatis dari transaksi.</p>
        </div>
      </div>
    

  <div class="modal fade" id="modalPos" tabindex="-1" aria-labelledby="modalPosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title fs-5" id="modalPosLabel">Tambah / ubah pos dana</h2>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form onsubmit="return false">
          <div class="modal-body d-flex flex-column gap-3">
            <div>
              <label class="form-label-app" for="posNama">Nama pos</label>
              <input class="form-control" id="posNama" type="text" placeholder="Contoh: Kas Pendidikan">
            </div>
            <div>
              <label class="form-label-app" for="posSaldo">Saldo awal (Rp)</label>
              <input class="form-control" id="posSaldo" type="text" value="0">
            </div>
            <div>
              <label class="form-label-app" for="posKeterangan">Keterangan</label>
              <textarea class="form-control" id="posKeterangan" placeholder="Untuk apa pos dana ini digunakan"></textarea>
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
