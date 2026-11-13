@extends('layouts.app')

@section('title', 'Pengeluaran &mdash; Kas Desa Babatan')

@section('topbarTitle', 'Pengeluaran')
@section('topbarSubtitle', 'Catat uang keluar')

@section('content')
<div class="mb-4">
        <h1 class="page-title">Catat pengeluaran</h1>
        <p class="page-subtitle">Contoh pengisian: santunan kematian dengan nominal di atas standar.</p>
      </div>

      <div class="row g-4" data-expense-preview>
        <div class="col-12 col-xl-8">
          <form class="card" onsubmit="return false">
            <div class="card-body d-flex flex-column gap-4">
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarTanggal">Tanggal</label>
                  <input class="form-control" id="keluarTanggal" type="date" value="2026-10-24">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarPos">Dibayar dari pos dana</label>
                  <select class="form-select" id="keluarPos" data-expense-pos>
                    <option data-saldo="35240000">Kas Kematian</option>
                    <option data-saldo="8910000">Kas Masjid</option>
                    <option data-saldo="18350000">Kas Sedekah Bumi</option>
                    <option data-saldo="2145000">Kas Lain-lain</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarKategori">Kategori</label>
                  <select class="form-select" id="keluarKategori">
                    <option>Santunan Kematian</option>
                    <option>Perawatan Masjid</option>
                    <option>Lampu Jalan</option>
                    <option>Konsumsi Sedekah Bumi</option>
                    <option>Pengeluaran Lain-lain</option>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarAcara">Terkait acara (opsional)</label>
                  <select class="form-select" id="keluarAcara">
                    <option>Tidak terkait acara</option>
                    <option>Sedekah Bumi 2026</option>
                  </select>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarNominal">Nominal (Rp)</label>
                  <input class="form-control" id="keluarNominal" data-expense-amount type="text" value="3.500.000">
                  <span class="hint">Standar santunan: Rp2.000.000</span>
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label-app" for="keluarAlasan">Alasan nominal berbeda (wajib)</label>
                  <input class="form-control" id="keluarAlasan" type="text" value="Ahli waris anak yatim piatu yang masih bersekolah">
                </div>
              </div>

              <div class="notice notice-amber">
                Nominal berbeda dari standar. Alasan ini akan tampil di halaman publik dalam bahasa umum, tanpa nama keluarga.
              </div>

              <div>
                <label class="form-label-app" for="keluarCatatanPublik">Catatan publik (tampil ke warga)</label>
                <input class="form-control" id="keluarCatatanPublik" data-expense-desc type="text" value="Santunan kematian warga, dengan tambahan untuk kebutuhan pendidikan ahli waris">
              </div>

              <div>
                <label class="form-label-app" for="keluarCatatanInternal">Catatan internal (hanya bendahara dan admin)</label>
                <textarea class="form-control" id="keluarCatatanInternal">Santunan keluarga almarhum Bpk. Dummy B, RT 05, tambahan biaya pendidikan.</textarea>
              </div>

              <div>
                <span class="form-label-app">Foto nota atau bukti</span>
                <div class="upload-grid">
                  <div class="upload-tile">[foto bukti 1]</div>
                  <label class="upload-add mb-0" for="keluarFoto">
                    <span><i class="bi bi-plus-lg d-block mb-1"></i>Tambah foto</span>
                  </label>
                  <input class="d-none" id="keluarFoto" type="file" accept="image/*" multiple>
                </div>
              </div>

              <div class="form-check d-flex align-items-start gap-2">
                <input class="form-check-input" id="keluarSembunyikan" type="checkbox" checked>
                <label class="form-check-label" for="keluarSembunyikan">
                  <strong>Sembunyikan foto nota dari halaman publik</strong>
                  <span class="hint d-block">Disarankan untuk santunan. Nominal dan kategori tetap tampil.</span>
                </label>
              </div>

              <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                <button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i>Simpan pengeluaran</button>
                <button class="btn btn-outline-brand" type="button">Batal</button>
              </div>
            </div>
          </form>
        </div>

        <div class="col-12 col-xl-4">
          <div class="d-flex flex-column gap-4">
            <div class="card">
              <div class="card-body d-flex flex-column gap-1">
                <span class="stat-label">Saldo pos dana dipilih</span>
                <div class="recent-row">
                  <span>Saat ini</span>
                  <strong data-expense-saldo>Rp35.240.000</strong>
                </div>
                <div class="recent-row">
                  <span>Sisa setelah disimpan</span>
                  <strong data-expense-remaining>Rp31.740.000</strong>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <span class="stat-label mb-2">Tampilan di halaman publik</span>
                <div class="preview-card">
                  <span class="preview-meta">24 Okt &middot; <span data-expense-public-pos>Kas Kematian</span></span>
                  <span class="preview-desc" data-expense-public-desc>Santunan kematian warga, dengan tambahan untuk kebutuhan pendidikan ahli waris</span>
                  <span class="preview-amount" data-expense-public>Rp3.500.000</span>
                  <span class="preview-meta">Foto nota tidak ditampilkan</span>
                </div>
                <p class="hint mt-2">Catatan internal dan nama keluarga tidak pernah tampil di halaman publik.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
