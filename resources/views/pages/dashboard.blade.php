@extends('layouts.app')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Selamat pagi, Bendahara</h1>
            <p class="page-subtitle">Kamis, 12 November 2026</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-brand" href="{{ route('pemasukan') }}"><i class="bi bi-plus-lg"></i>Catat pemasukan</a>
            <a class="btn btn-outline-brand" href="{{ route('pengeluaran') }}"><i class="bi bi-dash-lg"></i>Catat
                pengeluaran</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Total saldo</span>
                    <span class="stat-value">Rp64.645.000</span>
                    <span class="stat-sub">4 pos dana</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pemasukan bulan ini</span>
                    <span class="stat-value is-in">Rp2.710.000</span>
                    <span class="stat-sub">Per 12 Nov</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pengeluaran bulan ini</span>
                    <span class="stat-value is-out">Rp7.125.000</span>
                    <span class="stat-sub">Per 12 Nov</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Setoran minggu ini</span>
                    <span class="stat-value">13 dari 15 RT</span>
                    <span class="stat-sub">Minggu, 8 Nov</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-3">
                <h2 class="section-title">Setoran iuran kematian, Minggu 8 Nov</h2>
                <a class="link-app" href="{{ route('pemasukan') }}">Catat setoran</a>
            </div>

            <span class="lbl mb-2">RW 01</span>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="chip chip-ok">RT 01</span>
                <span class="chip chip-ok">RT 02</span>
                <span class="chip chip-ok">RT 03</span>
                <span class="chip chip-no">RT 04 belum</span>
                <span class="chip chip-ok">RT 05</span>
                <span class="chip chip-ok">RT 06</span>
                <span class="chip chip-ok">RT 07</span>
            </div>

            <span class="lbl mb-2">RW 02</span>
            <div class="d-flex flex-wrap gap-2">
                <span class="chip chip-ok">RT 01</span>
                <span class="chip chip-ok">RT 02</span>
                <span class="chip chip-ok">RT 03</span>
                <span class="chip chip-ok">RT 04</span>
                <span class="chip chip-no">RT 05 belum</span>
                <span class="chip chip-ok">RT 06</span>
                <span class="chip chip-ok">RT 07</span>
                <span class="chip chip-ok">RT 08</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                        <h2 class="section-title">Transaksi terbaru</h2>
                        <a class="link-app" href="{{ route('transaksi') }}">Lihat semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table-app">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th class="num">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09 Nov</td>
                                    <td><span class="badge-soft badge-out">Keluar</span></td>
                                    <td>Pembelian alat kebersihan</td>
                                    <td class="num">Rp125.000</td>
                                </tr>
                                <tr>
                                    <td>08 Nov</td>
                                    <td><span class="badge-soft badge-in">Masuk</span></td>
                                    <td>Setoran iuran kematian, RW 01 RT 03</td>
                                    <td class="num">Rp165.000</td>
                                </tr>
                                <tr>
                                    <td>08 Nov</td>
                                    <td><span class="badge-soft badge-in">Masuk</span></td>
                                    <td>Setoran iuran kematian, RW 02 RT 01</td>
                                    <td class="num">Rp185.000</td>
                                </tr>
                                <tr>
                                    <td>06 Nov</td>
                                    <td><span class="badge-soft badge-out">Keluar</span></td>
                                    <td>Uang muka konsumsi sedekah bumi</td>
                                    <td class="num">Rp4.500.000</td>
                                </tr>
                                <tr>
                                    <td>06 Nov</td>
                                    <td><span class="badge-soft badge-in">Masuk</span></td>
                                    <td>Uang kotak Jumat</td>
                                    <td class="num">Rp850.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title mb-3">Perlu perhatian</h2>
                    <div class="d-flex flex-column gap-3">
                        <div class="notice notice-amber">2 RT belum menyetor iuran kematian minggu ini.
                        </div>
                        <div class="notice notice-green">Realisasi sedekah bumi baru 21% dari anggaran.
                        </div>
                        <div class="notice notice-neutral">Jumat ini: jangan lupa catat uang kotak masjid.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
