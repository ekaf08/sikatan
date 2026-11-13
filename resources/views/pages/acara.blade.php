@extends('layouts.app')

@section('title', 'Acara Sedekah Bumi — Kas Desa Babatan')

@section('topbarTitle', 'Acara sedekah bumi')
@section('topbarSubtitle', 'Anggaran dan realisasi')

@section('content')
<div data-acara>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h1 class="page-title" data-acara-title>Sedekah Bumi 2026</h1>
          <p class="page-subtitle" data-acara-subtitle>Status: berjalan. Anggaran dan realisasi per item.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <select class="form-select" data-acara-select aria-label="Ganti acara" style="min-width:190px">
            <option selected data-title="Sedekah Bumi 2026" data-subtitle="Status: berjalan. Anggaran dan realisasi per item.">Sedekah Bumi 2026</option>
            <option data-title="Sedekah Bumi 2025" data-subtitle="Status: selesai. Laporan sudah ditutup.">Sedekah Bumi 2025</option>
          </select>
          <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#modalAnggaran">
            <i class="bi bi-plus-lg"></i>Tambah item anggaran
          </button>
        </div>
      </div>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Total anggaran</span>
              <span class="stat-value">Rp33.000.000</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Realisasi</span>
              <span class="stat-value is-out">Rp7.000.000</span>
              <span class="stat-sub">21% dari anggaran</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Sisa anggaran</span>
              <span class="stat-value is-in">Rp26.000.000</span>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card stat-card h-100">
            <div class="card-body">
              <span class="stat-label">Saldo kas sedekah bumi</span>
              <span class="stat-value">Rp18.350.000</span>
              <span class="stat-sub">Kurang Rp7.650.000 dari sisa anggaran</span>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-body">
          <h2 class="section-title mb-3">Anggaran vs realisasi</h2>
          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Item</th>
                  <th class="num">Anggaran</th>
                  <th class="num">Realisasi</th>
                  <th class="num">Sisa</th>
                  <th style="min-width:180px">Terpakai</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Konsumsi</td>
                  <td class="num">Rp15.000.000</td>
                  <td class="num">Rp4.500.000</td>
                  <td class="num">Rp10.500.000</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="meter flex-grow-1"><i style="width:30%"></i></div>
                      <span class="fw-semibold nowrap" style="font-size:13px;min-width:36px">30%</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Sewa tenda dan pengeras suara</td>
                  <td class="num">Rp6.000.000</td>
                  <td class="num">Rp2.500.000</td>
                  <td class="num">Rp3.500.000</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="meter flex-grow-1"><i style="width:42%"></i></div>
                      <span class="fw-semibold nowrap" style="font-size:13px;min-width:36px">42%</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Hiburan (karawitan atau wayang)</td>
                  <td class="num">Rp10.000.000</td>
                  <td class="num">Rp0</td>
                  <td class="num">Rp10.000.000</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="meter flex-grow-1"><i style="width:0%"></i></div>
                      <span class="fw-semibold nowrap" style="font-size:13px;min-width:36px">0%</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>Dokumentasi dan lain-lain</td>
                  <td class="num">Rp2.000.000</td>
                  <td class="num">Rp0</td>
                  <td class="num">Rp2.000.000</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="meter flex-grow-1"><i style="width:0%"></i></div>
                      <span class="fw-semibold nowrap" style="font-size:13px;min-width:36px">0%</span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="fw-bold">Total</td>
                  <td class="num fw-bold">Rp33.000.000</td>
                  <td class="num fw-bold">Rp7.000.000</td>
                  <td class="num fw-bold">Rp26.000.000</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-body">
              <h2 class="section-title mb-2">Pengeluaran terkait acara</h2>
              <div class="recent-row">
                <span>06 Nov &middot; Uang muka konsumsi sedekah bumi</span>
                <strong>Rp4.500.000</strong>
              </div>
              <div class="recent-row">
                <span>05 Nov &middot; Uang muka sewa tenda dan pengeras suara</span>
                <strong>Rp2.500.000</strong>
              </div>
              <p class="hint mt-3 mb-0">Setiap pengeluaran dihubungkan ke acara lewat formulir Catat pengeluaran.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-body">
              <h2 class="section-title mb-2">Pemasukan iuran sedekah bumi</h2>
              <div class="recent-row">
                <span>RW 01 (7 RT)</span>
                <strong>Rp7.900.000</strong>
              </div>
              <div class="recent-row">
                <span>RW 02 (8 RT)</span>
                <strong>Rp9.150.000</strong>
              </div>
              <div class="recent-row">
                <span class="fw-semibold">Total terkumpul</span>
                <strong>Rp17.050.000</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    

  <div class="modal fade" id="modalAnggaran" tabindex="-1" aria-labelledby="modalAnggaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title fs-5" id="modalAnggaranLabel">Tambah item anggaran</h2>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <form onsubmit="return false">
          <div class="modal-body d-flex flex-column gap-3">
            <div>
              <label class="form-label-app" for="anggaranItem">Nama item</label>
              <input class="form-control" id="anggaranItem" type="text" placeholder="Contoh: Panggung dan dekorasi">
            </div>
            <div>
              <label class="form-label-app" for="anggaranNominal">Anggaran (Rp)</label>
              <input class="form-control" id="anggaranNominal" type="text" placeholder="Contoh: 3.000.000">
            </div>
            <div>
              <label class="form-label-app" for="anggaranKet">Keterangan</label>
              <textarea class="form-control" id="anggaranKet" placeholder="Catatan singkat item ini"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-brand" type="submit">Simpan item</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
