@extends('layouts.app')

@section('title', 'Mutasi Dana — Kas Desa Babatan')

@section('topbarTitle', 'Mutasi dana')
@section('topbarSubtitle', 'Pindah saldo antar pos')

@section('content')
<div class="mb-4">
        <h1 class="page-title">Mutasi dana antar pos</h1>
        <p class="page-subtitle">Memindahkan saldo dari satu pos ke pos lain. Tidak dihitung sebagai pemasukan atau pengeluaran.</p>
      </div>

      <div class="row g-4 mb-4" data-mutation>
        <div class="col-12 col-xl-7">
          <form class="card" onsubmit="return false">
            <div class="card-body d-flex flex-column gap-4">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="mutasiDari">Dari pos</label>
                  <select class="form-select" id="mutasiDari" data-mutation-from>
                    <option data-saldo="2145000">Kas Lain-lain</option>
                    <option data-saldo="35240000">Kas Kematian</option>
                    <option data-saldo="8910000">Kas Masjid</option>
                    <option data-saldo="18350000">Kas Sedekah Bumi</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="mutasiKe">Ke pos</label>
                  <select class="form-select" id="mutasiKe" data-mutation-to>
                    <option data-saldo="18350000">Kas Sedekah Bumi</option>
                    <option data-saldo="35240000">Kas Kematian</option>
                    <option data-saldo="8910000">Kas Masjid</option>
                    <option data-saldo="2145000">Kas Lain-lain</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="mutasiTanggal">Tanggal</label>
                  <input class="form-control" id="mutasiTanggal" type="date" value="2026-11-12">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="mutasiNominal">Nominal (Rp)</label>
                  <input class="form-control" id="mutasiNominal" data-mutation-amount type="text" value="1.000.000">
                </div>
              </div>

              <div>
                <label class="form-label-app" for="mutasiKeterangan">Keterangan</label>
                <input class="form-control" id="mutasiKeterangan" type="text" value="Tambahan dana awal persiapan sedekah bumi">
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="balance-preview">
                    <span class="balance-title" data-mutation-from-label>Kas Lain-lain</span>
                    <span><span data-mutation-from-before>Rp2.145.000</span> &rarr; <strong data-mutation-from-after>Rp1.145.000</strong></span>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="balance-preview">
                    <span class="balance-title" data-mutation-to-label>Kas Sedekah Bumi</span>
                    <span><span data-mutation-to-before>Rp18.350.000</span> &rarr; <strong data-mutation-to-after>Rp19.350.000</strong></span>
                  </div>
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan mutasi</button>
                <button class="btn btn-outline-brand" type="button">Batal</button>
              </div>
            </div>
          </form>
        </div>

        <div class="col-12 col-xl-5">
          <div class="d-flex flex-column gap-4">
            <div class="card">
              <div class="card-body">
                <h2 class="section-title mb-2">Saldo tiap pos</h2>
                <div class="recent-row">
                  <span>Kas Kematian</span>
                  <strong>Rp35.240.000</strong>
                </div>
                <div class="recent-row">
                  <span>Kas Masjid</span>
                  <strong>Rp8.910.000</strong>
                </div>
                <div class="recent-row">
                  <span>Kas Sedekah Bumi</span>
                  <strong>Rp18.350.000</strong>
                </div>
                <div class="recent-row">
                  <span>Kas Lain-lain</span>
                  <strong>Rp2.145.000</strong>
                </div>
              </div>
            </div>

            <div class="notice notice-amber">
              Dana Kematian sebaiknya tidak dipindahkan ke pos lain tanpa persetujuan pengurus. Sistem tetap mencatat setiap mutasi di log audit.
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-2">
            <h2 class="section-title">Riwayat mutasi</h2>
            <span class="hint">Mutasi tidak mengubah total saldo kas desa</span>
          </div>
          <div class="table-responsive">
            <table class="table-app">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Dari</th>
                  <th>Ke</th>
                  <th>Keterangan</th>
                  <th class="num">Nominal</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>08 Okt</td>
                  <td>Kas Lain-lain</td>
                  <td>Kas Sedekah Bumi</td>
                  <td>Tambahan dana awal persiapan sedekah bumi</td>
                  <td class="num">Rp1.000.000</td>
                </tr>
                <tr>
                  <td>14 Agu</td>
                  <td>Kas Masjid</td>
                  <td>Kas Lain-lain</td>
                  <td>Pinjaman sementara untuk perbaikan jalan, sudah dikembalikan</td>
                  <td class="num">Rp500.000</td>
                </tr>
                <tr>
                  <td>29 Agu</td>
                  <td>Kas Lain-lain</td>
                  <td>Kas Masjid</td>
                  <td>Pengembalian pinjaman</td>
                  <td class="num">Rp500.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
@endsection
