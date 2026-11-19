<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pengeluaran - Sistem Kampung Babatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    <style>
        body { background: #f4f1e8; padding: 24px; }
        .cetak-wrap { max-width: 1100px; margin: 0 auto; }
        .cetak-aksi { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-bottom: 16px; }
        @page { size: A4 landscape; margin: 12mm; }
        @media print { body { background: #fff; padding: 0; } .cetak-aksi { display: none !important; } .report-paper { border: none; padding: 0; } .table-responsive { overflow: visible !important; } }
    </style>
</head>
<body>
    <div class="cetak-wrap">
        <div class="cetak-aksi">
            <button class="btn btn-outline-secondary" type="button" onclick="window.close()">Tutup</button>
            <button class="btn btn-brand" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
        <div class="report-paper">
            <div class="report-head">
                <div class="gov">SISTEM KAMPUNG BABATAN</div>
                <div class="report-name">DAFTAR PENGELUARAN</div>
                <div class="report-period">Dicetak: {{ $dicetakPada }}</div>
            </div>
            <div class="table-responsive">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th style="width:40px">No</th>
                            <th>Tanggal</th>
                            <th>Pos Dana</th>
                            <th>Kategori</th>
                            <th>Penerima</th>
                            <th>Acara</th>
                            <th>Keterangan</th>
                            <th class="num">Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $t)
                            @php
                                $acara = $t->acara?->nama; $item = $t->anggaranAcara?->uraian;
                                $acaraT = (!$acara && !$item) ? '-' : (($acara && $item) ? ($acara.' - '.$item) : ($acara ?? $item));
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="nowrap">{{ $t->tanggal ? $t->tanggal->format('d/m/Y') : '-' }}</td>
                                <td>{{ $t->posDana?->nama ?? '-' }}</td>
                                <td>{{ $t->kategori?->nama ?? '-' }}</td>
                                <td>{{ $t->penerima ?: '-' }}</td>
                                <td>{{ $acaraT }}</td>
                                <td>{{ $t->catatan_publik ?: '-' }}</td>
                                <td class="num">Rp{{ number_format((float) $t->nominal, 0, ',', '.') }}</td>
                                <td>{{ $t->status === 'batal' ? 'Batal' : 'Aktif' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="hint">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 300); });</script>
</body>
</html>
