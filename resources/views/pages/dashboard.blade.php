@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">{{ $sapaan }}, {{ $namaPengguna }}</h1>
            <p class="page-subtitle">{{ $tanggalHariIni }}</p>
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
                    <span class="stat-value">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ $jumlahPos }} pos dana</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pemasukan bulan ini</span>
                    <span class="stat-value is-in">Rp{{ number_format($pemasukanBulanIni, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ $labelBulanIni }}</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Pengeluaran bulan ini</span>
                    <span class="stat-value is-out">Rp{{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</span>
                    <span class="stat-sub">{{ $labelBulanIni }}</span>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <span class="stat-label">Setoran minggu ini</span>
                    <span class="stat-value">
                        @if ($totalRt > 0)
                            {{ $jumlahRtSetor }} dari {{ $totalRt }} RT
                        @else
                            Belum ada RT
                        @endif
                    </span>
                    <span class="stat-sub">{{ $periodeSetoran }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-3">
                <h2 class="section-title">Setoran iuran kematian, {{ $periodeSetoran }}</h2>
                <a class="link-app" href="{{ route('pemasukan') }}">Catat setoran</a>
            </div>

            @forelse ($setoranRw as $rw)
                <span class="lbl mb-2">{{ $rw['nama'] }}</span>
                <div class="d-flex flex-wrap gap-2 {{ $loop->last ? '' : 'mb-3' }}">
                    @foreach ($rw['rt'] as $rt)
                        <span class="chip {{ $rt['sudah'] ? 'chip-ok' : 'chip-no' }}">
                            RT {{ $rt['nomor'] }}{{ $rt['sudah'] ? '' : ' belum' }}
                        </span>
                    @endforeach
                </div>
            @empty
                <p class="hint mb-0">Data RT belum tersedia. Tambahkan wilayah RW dan RT terlebih dahulu.</p>
            @endforelse
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
                                @forelse ($transaksiTerbaru as $item)
                                    <tr>
                                        <td>{{ $item['tanggal'] }}</td>
                                        <td>
                                            <span class="badge-soft {{ $item['jenis'] === 'masuk' ? 'badge-in' : 'badge-out' }}">
                                                {{ $item['jenis'] === 'masuk' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td>{{ $item['keterangan'] }}</td>
                                        <td class="num"
                                            style="color: {{ $item['jenis'] === 'masuk' ? 'var(--ks-success-text)' : 'var(--ks-warning-text)' }}; white-space: nowrap;">
                                            {{ $item['jenis'] === 'masuk' ? '+' : '-' }}Rp{{ number_format($item['nominal'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="hint">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
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
                        @if ($totalRt === 0)
                            <div class="notice notice-neutral">Data RT belum diisi. Lengkapi wilayah agar setoran bisa
                                dipantau.</div>
                        @elseif ($rtBelumSetor > 0)
                            <div class="notice notice-amber">{{ $rtBelumSetor }} RT belum menyetor iuran kematian minggu
                                ini.</div>
                        @else
                            <div class="notice notice-green">Semua RT sudah menyetor iuran kematian minggu ini.</div>
                        @endif

                        @if ($acara)
                            <div class="notice notice-green">Realisasi {{ $acara->nama }} baru
                                {{ $persenRealisasiAcara }}% dari anggaran.</div>
                        @else
                            <div class="notice notice-neutral">Belum ada acara sedekah bumi yang disusun.</div>
                        @endif

                        <div class="notice notice-neutral">Jumat ini: jangan lupa catat uang kotak masjid.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
