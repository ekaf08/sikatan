@php
    $rupiah = fn ($nilai) => 'Rp' . number_format((float) $nilai, 0, ',', '.');
    $tampilKategori = in_array('kategori', $isi, true);
    $tampilPengeluaran = in_array('pengeluaran', $isi, true);
    $tampilAnggaran = in_array('anggaran', $isi, true);
    $tampilInternal = in_array('internal', $isi, true);
@endphp

<div class="report-paper">
    <div class="report-head">
        <div class="gov">{{ $data['kop_baris_1'] }}</div>
        <div class="report-name">{{ $data['kop_baris_2'] }}</div>
        @if ($data['kop_alamat'])
            <div class="report-period">{{ $data['kop_alamat'] }}</div>
        @endif
        <div class="report-period">{{ $data['periode_label'] }}</div>
        <div class="report-period">{{ $data['pos_label'] }}</div>
    </div>

    <div class="table-responsive">
        <table class="table-app">
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
    </div>

    @if ($tampilKategori)
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
            </div>
        </div>
    @endif

    @if ($tampilPengeluaran)
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
                        @forelse ($data['pengeluaran'] as $item)
                            <tr>
                                <td class="nowrap">{{ $item['tanggal_label'] }}</td>
                                <td>{{ $item['pos'] }}</td>
                                <td>
                                    {{ $item['keterangan'] }}
                                    @if ($item['penerima'])
                                        <span class="hint d-block">Penerima: {{ $item['penerima'] }}</span>
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
            </div>
        </div>
    @endif

    @if ($tampilAnggaran)
        <div>
            <div class="report-section-title">C. Anggaran vs realisasi acara</div>
            <div class="table-responsive">
                <table class="table-app">
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
                            @php
                                $selisih = $item['anggaran'] - $item['realisasi'];
                            @endphp
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
            </div>
        </div>
    @endif

    @if (!empty($data['mutasi']))
        <div>
            <div class="report-section-title">D. Mutasi dana antar pos</div>
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
                        @foreach ($data['mutasi'] as $item)
                            <tr>
                                <td class="nowrap">{{ $item['tanggal_label'] }}</td>
                                <td>{{ $item['dari'] }}</td>
                                <td>{{ $item['ke'] }}</td>
                                <td>{{ $item['keterangan'] }}</td>
                                <td class="num">{{ $rupiah($item['nominal']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($tampilInternal && !empty($data['internal']))
        <div>
            <div class="report-section-title">E. Catatan internal pengeluaran</div>
            <div class="table-responsive">
                <table class="table-app">
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
                                <td class="nowrap">{{ $item['tanggal_label'] }}</td>
                                <td>{{ $item['kategori'] }}</td>
                                <td>{{ $item['catatan_internal'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="report-sign">
        <div>
            <div>Mengetahui,</div>
            <div>Ketua</div>
            <div class="sign-space"></div>
            <strong>{{ $data['nama_ketua'] }}</strong>
        </div>
        <div>
            <div>Babatan, {{ $data['tanggal_selesai_label'] }}</div>
            <div>Bendahara</div>
            <div class="sign-space"></div>
            <strong>{{ $data['nama_bendahara'] }}</strong>
        </div>
    </div>
</div>
