<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $data['kop_baris_2'] }} — {{ $data['periode_label'] }}</title>
    <style>
        @page {
            margin: 14mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #111;
            font-size: 11px;
        }

        .report-head {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 3px double #111;
            margin-bottom: 14px;
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
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 5px 6px;
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

        .section-title {
            font-weight: bold;
            margin: 10px 0 4px;
        }

        .hint {
            font-style: italic;
        }

        .sign td {
            border: none;
            text-align: center;
            width: 50%;
            padding-top: 16px;
        }

        .sign .space {
            height: 54px;
        }

        .sign strong {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    @php
        $rupiah = fn ($nilai) => 'Rp' . number_format((float) $nilai, 0, ',', '.');
        $tampilKategori = in_array('kategori', $isi, true);
        $tampilPengeluaran = in_array('pengeluaran', $isi, true);
        $tampilAnggaran = in_array('anggaran', $isi, true);
        $tampilInternal = in_array('internal', $isi, true);
    @endphp

    <div class="report-head">
        <div class="gov">{{ $data['kop_baris_1'] }}</div>
        <div class="report-name">{{ $data['kop_baris_2'] }}</div>
        @if ($data['kop_alamat'])
            <div class="report-period">{{ $data['kop_alamat'] }}</div>
        @endif
        <div class="report-period">{{ $data['periode_label'] }}</div>
        <div class="report-period">{{ $data['pos_label'] }}</div>
    </div>

    <table>
        <tbody>
            <tr>
                <td>Saldo awal ({{ $data['tanggal_mulai_label'] }})</td>
                <td class="num">{{ $rupiah($data['saldo_awal']) }}</td>
            </tr>
            <tr>
                <td>Total pemasukan</td>
                <td class="num">{{ $rupiah($data['total_masuk']) }}</td>
            </tr>
            <tr>
                <td>Total pengeluaran</td>
                <td class="num">{{ $rupiah($data['total_keluar']) }}</td>
            </tr>
            <tr>
                <td><strong>Saldo akhir ({{ $data['tanggal_selesai_label'] }})</strong></td>
                <td class="num"><strong>{{ $rupiah($data['saldo_akhir']) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if ($tampilKategori)
        <div class="section-title">A. Pemasukan per kategori</div>
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="num">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['pemasukan_kategori'] as $kategori)
                    <tr>
                        <td>{{ $kategori['nama'] }}</td>
                        <td class="num">{{ $rupiah($kategori['jumlah']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="hint">Tidak ada pemasukan pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($tampilPengeluaran)
        <div class="section-title">B. Daftar pengeluaran</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pos dana</th>
                    <th>Keterangan</th>
                    <th class="num">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['pengeluaran'] as $item)
                    <tr>
                        <td>{{ $item['tanggal_label'] }}</td>
                        <td>{{ $item['pos'] }}</td>
                        <td>
                            {{ $item['keterangan'] }}
                            @if ($item['penerima'])
                                <br><span class="hint">Penerima: {{ $item['penerima'] }}</span>
                            @endif
                        </td>
                        <td class="num">{{ $rupiah($item['nominal']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="hint">Tidak ada pengeluaran pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($tampilAnggaran)
        <div class="section-title">C. Anggaran vs realisasi acara</div>
        <table>
            <thead>
                <tr>
                    <th>Acara</th>
                    <th class="num">Anggaran</th>
                    <th class="num">Realisasi</th>
                    <th class="num">Selisih</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['anggaran'] as $item)
                    @php($selisih = $item['anggaran'] - $item['realisasi'])
                    <tr>
                        <td>{{ $item['nama'] }} ({{ $item['tahun'] }})</td>
                        <td class="num">{{ $rupiah($item['anggaran']) }}</td>
                        <td class="num">{{ $rupiah($item['realisasi']) }}</td>
                        <td class="num">{{ $rupiah($selisih) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="hint">Tidak ada anggaran acara pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if (!empty($data['mutasi']))
        <div class="section-title">D. Mutasi dana antar pos</div>
        <table>
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
                @foreach ($data['mutasi'] as $item)
                    <tr>
                        <td>{{ $item['tanggal_label'] }}</td>
                        <td>{{ $item['dari'] }}</td>
                        <td>{{ $item['ke'] }}</td>
                        <td>{{ $item['keterangan'] }}</td>
                        <td class="num">{{ $rupiah($item['nominal']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($tampilInternal && !empty($data['internal']))
        <div class="section-title">E. Catatan internal pengeluaran</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Catatan internal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['internal'] as $item)
                    <tr>
                        <td>{{ $item['tanggal_label'] }}</td>
                        <td>{{ $item['kategori'] }}</td>
                        <td>{{ $item['catatan_internal'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="sign">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div>Ketua</div>
                <div class="space"></div>
                <strong>{{ $data['nama_ketua'] }}</strong>
            </td>
            <td>
                <div>Babatan, {{ $data['tanggal_selesai_label'] }}</div>
                <div>Bendahara</div>
                <div class="space"></div>
                <strong>{{ $data['nama_bendahara'] }}</strong>
            </td>
        </tr>
    </table>
</body>

</html>
