@extends('layouts.app')

@section('title', 'Laporan dan Ekspor - Sistem Kampung Babatan')

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
                <form class="card" id="formLaporan" autocomplete="off" onsubmit="return false">
                    <div class="card-body d-flex flex-column gap-3">
                        <div>
                            <label class="form-label-app" for="laporanPeriode">Periode</label>
                            <select class="form-select" id="laporanPeriode" name="periode">
                                @foreach ($opsiPeriode as $opsi)
                                    <option value="{{ $opsi['value'] }}" @selected($filter['periode'] === $opsi['value'])>
                                        {{ $opsi['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 d-none" id="laporanRentang">
                            <div class="col-6">
                                <label class="form-label-app" for="laporanMulai">Dari tanggal</label>
                                <input class="form-control" id="laporanMulai" name="tanggal_mulai" type="date"
                                    value="{{ $filter['tanggal_mulai'] }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-app" for="laporanSelesai">Sampai tanggal</label>
                                <input class="form-control" id="laporanSelesai" name="tanggal_selesai" type="date"
                                    value="{{ $filter['tanggal_selesai'] }}">
                            </div>
                        </div>

                        <div>
                            <label class="form-label-app" for="laporanPos">Pos dana</label>
                            <select class="form-select" id="laporanPos" name="pos_dana_id">
                                <option value="">Semua pos dana</option>
                                @foreach ($daftarPos as $pos)
                                    <option value="{{ $pos->id }}" @selected((int) $filter['pos_dana_id'] === $pos->id)>
                                        {{ $pos->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <span class="form-label-app">Isi laporan</span>
                            <div class="d-flex flex-column gap-1">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" id="laporanKategori" name="isi[]" type="checkbox"
                                        value="kategori" @checked(in_array('kategori', $filter['isi'], true))>
                                    <label class="form-check-label" for="laporanKategori">Ringkasan pemasukan per
                                        kategori</label>
                                </div>
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" id="laporanPengeluaran" name="isi[]" type="checkbox"
                                        value="pengeluaran" @checked(in_array('pengeluaran', $filter['isi'], true))>
                                    <label class="form-check-label" for="laporanPengeluaran">Daftar pengeluaran
                                        lengkap</label>
                                </div>
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" id="laporanAnggaran" name="isi[]" type="checkbox"
                                        value="anggaran" @checked(in_array('anggaran', $filter['isi'], true))>
                                    <label class="form-check-label" for="laporanAnggaran">Anggaran vs realisasi
                                        acara</label>
                                </div>
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" id="laporanInternal" name="isi[]" type="checkbox"
                                        value="internal" @checked(in_array('internal', $filter['isi'], true))>
                                    <label class="form-check-label" for="laporanInternal">Sertakan catatan internal</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2 pt-3 border-top">
                            <button class="btn btn-brand" id="btnUnduhPdf" type="button">
                                <i class="bi bi-file-earmark-pdf"></i>Unduh PDF dengan kop
                            </button>
                            <button class="btn btn-outline-brand" id="btnUnduhExcel" type="button">
                                <i class="bi bi-file-earmark-excel"></i>Unduh Excel (CSV)
                            </button>
                        </div>
                    </div>
                </form>

                <div class="notice notice-green">
                    Kop surat dan nama penandatangan diatur di menu Pengaturan. Catatan internal tidak ikut dicetak kecuali
                    dicentang.
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-2">
                <span class="stat-label">Pratinjau laporan</span>
                <span class="hint" id="laporanStatus"></span>
            </div>

            <div id="laporanPreview">
                @include('pages.laporan-preview', [
                    'data' => $data,
                    'isi' => $filter['isi'],
                    'pengaturan' => $pengaturan,
                ])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formLaporan');
            const periodeSelect = document.getElementById('laporanPeriode');
            const rentangWrap = document.getElementById('laporanRentang');
            const mulaiInput = document.getElementById('laporanMulai');
            const selesaiInput = document.getElementById('laporanSelesai');
            const posSelect = document.getElementById('laporanPos');
            const preview = document.getElementById('laporanPreview');
            const status = document.getElementById('laporanStatus');
            const urlData = "{{ route('laporan.data') }}";
            const urlPdf = "{{ route('laporan.pdf') }}";
            const urlExcel = "{{ route('laporan.excel') }}";

            let timer = null;
            let permintaanTerakhir = 0;

            function sinkronRentang() {
                rentangWrap.classList.toggle('d-none', periodeSelect.value !== 'custom');
            }

            function ambilParams() {
                const params = new URLSearchParams();
                params.set('periode', periodeSelect.value);
                params.set('isi_present', '1');

                if (periodeSelect.value === 'custom') {
                    if (mulaiInput.value) {
                        params.set('tanggal_mulai', mulaiInput.value);
                    }
                    if (selesaiInput.value) {
                        params.set('tanggal_selesai', selesaiInput.value);
                    }
                }

                if (posSelect.value) {
                    params.set('pos_dana_id', posSelect.value);
                }

                form.querySelectorAll('input[name="isi[]"]:checked').forEach(function(checkbox) {
                    params.append('isi[]', checkbox.value);
                });

                return params;
            }

            function perbaruiPratinjau() {
                const params = ambilParams();
                const nomor = ++permintaanTerakhir;

                preview.style.opacity = '.55';
                status.textContent = 'Memuat pratinjau...';

                fetch(urlData + '?' + params.toString(), {
                        headers: {
                            'Accept': 'text/html',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal memuat pratinjau.');
                        }
                        return response.text();
                    })
                    .then(function(html) {
                        if (nomor !== permintaanTerakhir) {
                            return;
                        }
                        preview.innerHTML = html;
                        status.textContent = 'Pratinjau diperbarui.';
                    })
                    .catch(function() {
                        if (nomor === permintaanTerakhir) {
                            status.textContent = 'Pratinjau gagal diperbarui.';
                        }
                    })
                    .finally(function() {
                        if (nomor === permintaanTerakhir) {
                            preview.style.opacity = '1';
                        }
                    });
            }

            function jadwalkanPratinjau() {
                window.clearTimeout(timer);
                timer = window.setTimeout(perbaruiPratinjau, 250);
            }

            form.addEventListener('change', function(event) {
                if (event.target === periodeSelect) {
                    sinkronRentang();
                }
                jadwalkanPratinjau();
            });

            form.addEventListener('input', function(event) {
                if (event.target === mulaiInput || event.target === selesaiInput) {
                    jadwalkanPratinjau();
                }
            });

            document.getElementById('btnUnduhPdf').addEventListener('click', function() {
                window.open(urlPdf + '?' + ambilParams().toString(), '_blank', 'noopener');
            });

            document.getElementById('btnUnduhExcel').addEventListener('click', function() {
                window.location.href = urlExcel + '?' + ambilParams().toString();
            });

            sinkronRentang();
        });
    </script>
@endpush
