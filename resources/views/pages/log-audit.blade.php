@extends('layouts.app')

@section('title', 'Log Audit — Kas Desa Babatan')

@section('topbarTitle', 'Log audit')
@section('topbarSubtitle', 'Riwayat perubahan')

@section('content')
<div data-audit>
<div class="mb-4">
        <h1 class="page-title">Log audit</h1>
        <p class="page-subtitle">Riwayat siapa mengubah apa dan kapan. Hanya bisa dibaca, tidak bisa diubah atau dihapus.</p>
      </div>

      <div class="card mb-4">
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-6 col-md-4 col-xl">
              <label class="form-label-app" for="filterAksi">Aksi</label>
              <select class="form-select" id="filterAksi" data-audit-filter="data-audit-action">
                <option>Semua aksi</option>
                <option>Tambah</option>
                <option>Ubah</option>
                <option>Batal</option>
              </select>
            </div>
            <div class="col-6 col-md-4 col-xl">
              <label class="form-label-app" for="filterData">Data</label>
              <select class="form-select" id="filterData" data-audit-filter="data-audit-data">
                <option>Semua data</option>
                <option>Transaksi</option>
                <option>Mutasi dana</option>
                <option>Pengaturan</option>
              </select>
            </div>
            <div class="col-6 col-md-4 col-xl">
              <label class="form-label-app" for="filterPengguna">Pengguna</label>
              <select class="form-select" id="filterPengguna" data-audit-filter="data-audit-user">
                <option>Semua pengguna</option>
                <option>Bendahara Babatan</option>
                <option>Admin Babatan</option>
              </select>
            </div>
            <div class="col-6 col-md-6 col-xl">
              <label class="form-label-app" for="filterDari">Dari</label>
              <input class="form-control" id="filterDari" type="date" value="2026-11-01">
            </div>
            <div class="col-6 col-md-6 col-xl">
              <label class="form-label-app" for="filterSampai">Sampai</label>
              <input class="form-control" id="filterSampai" type="date" value="2026-11-12">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Pengguna</th>
                  <th>Aksi</th>
                  <th>Data</th>
                  <th>Perubahan</th>
                </tr>
              </thead>
              <tbody>
                <tr data-audit-action="Tambah" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">09 Nov 14.06</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-in">Tambah</span></td>
                  <td>Transaksi &middot; keluar</td>
                  <td class="audit-diff">Pembelian alat kebersihan<br>nominal: <span class="diff-new">125.000</span></td>
                </tr>
                <tr data-audit-action="Batal" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">08 Nov 14.05</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-neutral">Batal</span></td>
                  <td>Transaksi &middot; keluar</td>
                  <td class="audit-diff">Pembelian alat kebersihan (250.000)<br>status: <span class="diff-old">aktif</span> &rarr; <span class="diff-new">batal</span><br>alasan: Salah input, nominal seharusnya 125.000</td>
                </tr>
                <tr data-audit-action="Tambah" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">08 Nov 14.01</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-in">Tambah</span></td>
                  <td>Transaksi &middot; keluar</td>
                  <td class="audit-diff">Pembelian alat kebersihan<br>nominal: <span class="diff-new">250.000</span></td>
                </tr>
                <tr data-audit-action="Tambah" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">08 Nov 09.40</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-in">Tambah</span></td>
                  <td>Transaksi &middot; masuk</td>
                  <td class="audit-diff">Setoran iuran kematian, RW 01 RT 03<br>jumlah KK: <span class="diff-new">33</span>, nominal: <span class="diff-new">165.000</span></td>
                </tr>
                <tr data-audit-action="Ubah" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">06 Nov 11.02</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-warn">Ubah</span></td>
                  <td>Transaksi &middot; keluar</td>
                  <td class="audit-diff">Uang muka konsumsi sedekah bumi<br>nominal: <span class="diff-old">4.000.000</span> &rarr; <span class="diff-new">4.500.000</span></td>
                </tr>
                <tr data-audit-action="Ubah" data-audit-data="Transaksi" data-audit-user="Bendahara Babatan">
                  <td class="nowrap">24 Okt 16.30</td>
                  <td>Bendahara Babatan</td>
                  <td><span class="badge-soft badge-warn">Ubah</span></td>
                  <td>Transaksi &middot; keluar</td>
                  <td class="audit-diff">Santunan kematian warga<br>catatan_publik: <span class="diff-old">(kosong)</span> &rarr; <span class="diff-new">dengan tambahan untuk kebutuhan pendidikan ahli waris</span></td>
                </tr>
                <tr data-audit-action="Ubah" data-audit-data="Pengaturan" data-audit-user="Admin Babatan">
                  <td class="nowrap">20 Okt 10.15</td>
                  <td>Admin Babatan</td>
                  <td><span class="badge-soft badge-warn">Ubah</span></td>
                  <td>Pengaturan</td>
                  <td class="audit-diff">nama_bendahara: <span class="diff-old">(kosong)</span> &rarr; <span class="diff-new">Bendahara (contoh)</span></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3">
            <span class="hint" data-audit-count>Menampilkan 7 dari 7 catatan</span>
            <a class="link-app" href="{{ route('log-audit') }}">Berikutnya</a>
          </div>
        </div>
      </div>
</div>
@endsection
