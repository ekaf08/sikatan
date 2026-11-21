<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar Transaksi - Sistem Kampung Babatan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #111;
            font-size: 10px;
        }

        .report-head {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 3px double #111;
            margin-bottom: 12px;
        }

        .report-head .gov {
            font-size: 14px;
            font-weight: bold;
        }

        .report-head .report-name {
            font-size: 13px;
            font-weight: bold;
        }

        .report-head .report-period {
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 5px;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .nowrap {
            white-space: nowrap;
        }

        .hint {
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="report-head">
        <div class="gov">SISTEM KAMPUNG BABATAN</div>
        <div class="report-name">DAFTAR TRANSAKSI</div>
        <div class="report-period">Filter: {{ $filterLabel }}</div>
        <div class="report-period">Dicetak: {{ $dicetakPada }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Pos Dana</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th class="num">Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $t)
                @php
                    $bagian = [];
                    if ($t->jenis === 'masuk' && $t->rt) {
                        $bagian[] = 'RT '.$t->rt->nomor.' / '.($t->rt->rw?->nama ?? '-');
                    }
                    if ($t->jenis === 'masuk' && $t->jumlah_kk_bayar) {
                        $bagian[] = $t->jumlah_kk_bayar.' KK';
                    }
                    if ($t->nama_donatur) {
                        $bagian[] = $t->donatur_anonim ? 'Hamba Allah' : $t->nama_donatur;
                    }
                    if ($t->acara) {
                        $bagian[] = $t->acara->nama;
                    }
                    if ($t->catatan_publik) {
                        $bagian[] = \Illuminate\Support\Str::limit($t->catatan_publik, 60, '');
                    }
                    $ket = $bagian ? implode(' - ', $bagian) : '-';
                    $batal = $t->trashed() || $t->status === 'batal';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="nowrap">{{ $t->tanggal ? $t->tanggal->format('d/m/Y') : '-' }}</td>
                    <td>{{ $t->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}</td>
                    <td>{{ $t->posDana?->nama ?? '-' }}</td>
                    <td>{{ $t->kategori?->nama ?? '-' }}</td>
                    <td>{{ $ket }}</td>
                    <td class="num">{{ ($t->jenis === 'masuk' ? '+' : '-') . 'Rp' . number_format((float) $t->nominal, 0, ',', '.') }}</td>
                    <td>{{ $batal ? 'Batal' : 'Aktif' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="hint">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
