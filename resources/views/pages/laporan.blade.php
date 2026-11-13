@extends('layouts.app')

@section('title', 'Laporan dan Ekspor — Kas Desa Babatan')

@section('topbarTitle', 'Laporan dan ekspor')
@section('topbarSubtitle', 'Unduh laporan kas')

@section('content')
<div class="mb-4">
        <h1 class="page-title">Laporan dan ekspor</h1>
        <p class="page-subtitle">Untuk rapat triwulan. Pilih periode dan pos dana, lalu unduh.</p>
      </div>

      <div class="row g-4">
        <div class="col-12 col-xl-4">
          <div class="d-flex flex-column gap-4">
            <form class="card" onsubmit="return false">
              <div class="card-body d-flex flex-column gap-3">
                <div>
                  <label class="form-label-app" for="laporanPeriode">Periode</label>
                  <select class="form-select" id="laporanPeriode" data-report-period>
                    <option value="Triwulan 4 Tahun 2026 (1 Oktober sampai 12 November 2026)" selected>Triwulan 4 2026 (Okt-Des)</option>
                    <option value="Triwulan 3 Tahun 2026 (1 Juli sampai 30 September 2026)">Triwulan 3 2026 (Jul-Sep)</option>
                    <option value="Triwulan 2 Tahun 2026 (1 April sampai 30 Juni 2026)">Triwulan 2 2026 (Apr-Jun)</option>
                    <option value="Triwulan 1 Tahun 2026 (1 Januari sampai 31 Maret 2026)">Triwulan 1 2026 (Jan-Mar)</option>
                    <option value="Tahun 2026 (1 Januari sampai 31 Desember 2026)">Tahun 2026</option>
                    <option value="Rentang tanggal sendiri">Rentang tanggal sendiri</option>
                  </select>
                </div>

                <div>
                  <label class="form-label-app" for="laporanPos">Pos dana</label>
                  <select class="form-select" id="laporanPos">
                    <option>Semua pos dana</option>
                    <option>Kas Kematian</option>
                    <option>Kas Masjid</option>
                    <option>Kas Sedekah Bumi</option>
                    <option>Kas Lain-lain</option>
                  </select>
                </div>

                <div>
                  <span class="form-label-app">Isi laporan</span>
                  <div class="d-flex flex-column gap-1">
                    <div class="form-check d-flex align-items-center">
                      <input class="form-check-input" id="laporanKategori" type="checkbox" checked>
                      <label class="form-check-label" for="laporanKategori">Ringkasan pemasukan per kategori</label>
                    </div>
                    <div class="form-check d-flex align-items-center">
                      <input class="form-check-input" id="laporanPengeluaran" type="checkbox" checked>
                      <label class="form-check-label" for="laporanPengeluaran">Daftar pengeluaran lengkap</label>
                    </div>
                    <div class="form-check d-flex align-items-center">
                      <input class="form-check-input" id="laporanAnggaran" type="checkbox" checked>
                      <label class="form-check-label" for="laporanAnggaran">Anggaran vs realisasi acara</label>
                    </div>
                    <div class="form-check d-flex align-items-center">
                      <input class="form-check-input" id="laporanInternal" type="checkbox">
                      <label class="form-check-label" for="laporanInternal">Sertakan catatan internal</label>
                    </div>
                  </div>
                </div>

                <div class="d-flex flex-column gap-2 pt-3 border-top">
                  <button class="btn btn-brand" type="button"><i class="bi bi-file-earmark-pdf"></i>Unduh PDF dengan kop</button>
                  <button class="btn btn-outline-brand" type="button"><i class="bi bi-file-earmark-excel"></i>Unduh Excel</button>
                </div>
              </div>
            </form>

            <div class="notice notice-green">
              Kop surat dan nama penandatangan diatur di menu Pengaturan. Catatan internal tidak ikut dicetak kecuali dicentang.
            </div>
          </div>
        </div>

        <div class="col-12 col-xl-8">
          <span class="stat-label mb-2">Pratinjau laporan PDF</span>
          <div class="report-paper">
            <div class="report-head">
              <div class="gov">PEMERINTAH DESA BABATAN</div>
              <div class="report-name">LAPORAN KEUANGAN KAS WARGA</div>
              <div class="report-period" data-report-period-label>Triwulan 4 Tahun 2026 (1 Oktober sampai 12 November 2026)</div>
            </div>

            <div class="table-responsive">
              <table class="table-app">
                <tbody>
                  <tr>
                    <td>Saldo awal (1 Oktober 2026)</td>
                    <td class="num">Rp70.800.000</td>
                  </tr>
                  <tr>
                    <td>Total pemasukan</td>
                    <td class="num">Rp7.300.000</td>
                  </tr>
                  <tr>
                    <td>Total pengeluaran</td>
                    <td class="num">Rp13.455.000</td>
                  </tr>
                  <tr>
                    <td><strong>Saldo akhir</strong></td>
                    <td class="num"><strong>Rp64.645.000</strong></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div>
              <div class="report-section-title">A. Pemasukan per kategori</div>
              <div class="table-responsive">
                <table class="table-app">
                  <thead>
                    <tr>
                      <th>Kategori</th>
                      <th class="num">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Iuran Kematian</td>
                      <td class="num">Rp3.350.000</td>
                    </tr>
                    <tr>
                      <td>Uang Kotak Jumat</td>
                      <td class="num">Rp2.450.000</td>
                    </tr>
                    <tr>
                      <td>Donasi Masjid</td>
                      <td class="num">Rp1.500.000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div>
              <div class="report-section-title">B. Daftar pengeluaran</div>
              <div class="table-responsive">
                <table class="table-app">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Pos dana</th>
                      <th>Keterangan</th>
                      <th class="num">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>08 Okt</td>
                      <td>Kematian</td>
                      <td>Santunan kematian warga</td>
                      <td class="num">Rp2.000.000</td>
                    </tr>
                    <tr>
                      <td>24 Okt</td>
                      <td>Kematian</td>
                      <td>Santunan kematian warga, tambahan kebutuhan pendidikan ahli waris</td>
                      <td class="num">Rp3.500.000</td>
                    </tr>
                    <tr>
                      <td>28 Okt</td>
                      <td>Lain-lain</td>
                      <td>Ganti 4 lampu jalan di RW 01</td>
                      <td class="num">Rp480.000</td>
                    </tr>
                    <tr>
                      <td>30 Okt</td>
                      <td>Masjid</td>
                      <td>Ganti kran tempat wudu yang rusak</td>
                      <td class="num">Rp350.000</td>
                    </tr>
                    <tr>
                      <td>05 Nov</td>
                      <td>Sedekah Bumi</td>
                      <td>Uang muka sewa tenda dan pengeras suara</td>
                      <td class="num">Rp2.500.000</td>
                    </tr>
                    <tr>
                      <td>06 Nov</td>
                      <td>Sedekah Bumi</td>
                      <td>Uang muka konsumsi sedekah bumi</td>
                      <td class="num">Rp4.500.000</td>
                    </tr>
                    <tr>
                      <td>09 Nov</td>
                      <td>Lain-lain</td>
                      <td>Pembelian alat kebersihan</td>
                      <td class="num">Rp125.000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="report-sign">
              <div>
                <div>Mengetahui,</div>
                <div>Ketua</div>
                <div class="sign-space"></div>
                <strong>Ketua (dummy)</strong>
              </div>
              <div>
                <div>Babatan, 12 November 2026</div>
                <div>Bendahara</div>
                <div class="sign-space"></div>
                <strong>Bendahara (dummy)</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
