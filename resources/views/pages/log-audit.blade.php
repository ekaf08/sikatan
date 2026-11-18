@extends('layouts.app')

@section('title', 'Log Audit - Sistem Kampung Babatan')

@section('topbarTitle', 'Log audit')
@section('topbarSubtitle', 'Riwayat perubahan')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Log audit</h1>
        <p class="page-subtitle">Riwayat siapa mengubah apa dan kapan. Hanya bisa dibaca, tidak bisa diubah atau dihapus.
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-4 col-xl">
                    <label class="form-label-app" for="filterAksi">Aksi</label>
                    <select class="form-select" id="filterAksi">
                        <option value="">Semua aksi</option>
                        <option value="tambah">Tambah</option>
                        <option value="ubah">Ubah</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <label class="form-label-app" for="filterData">Data</label>
                    <select class="form-select" id="filterData">
                        <option value="">Semua data</option>
                        @foreach ($daftarTabel as $tabel => $label)
                            <option value="{{ $tabel }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl">
                    <label class="form-label-app" for="filterPengguna">Pengguna</label>
                    <select class="form-select" id="filterPengguna">
                        <option value="">Semua pengguna</option>
                        @foreach ($daftarPengguna as $pengguna)
                            <option value="{{ $pengguna->id }}">{{ $pengguna->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-6 col-xl">
                    <label class="form-label-app" for="filterDari">Dari</label>
                    <input class="form-control" id="filterDari" type="date">
                </div>
                <div class="col-6 col-md-6 col-xl">
                    <label class="form-label-app" for="filterSampai">Sampai</label>
                    <input class="form-control" id="filterSampai" type="date">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelAudit">
                    <thead>
                        <tr>
                            <th style="min-width: 130px;">Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Data</th>
                            <th>Perubahan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.DataTable) {
                return;
            }

            const $ = window.jQuery;

            const bahasaDataTables = {
                search: 'Cari:',
                lengthMenu: 'Tampil _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ catatan',
                infoEmpty: 'Tidak ada catatan',
                infoFiltered: '(difilter dari _MAX_ total catatan)',
                zeroRecords: 'Catatan tidak ditemukan',
                processing: 'Memuat...',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Lanjut',
                    previous: 'Kembali'
                }
            };

            const domDataTables = "<'row align-items-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row align-items-center mt-3'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 d-flex justify-content-end'p>>";

            const tabel = $('#tabelAudit').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('log-audit.data') }}",
                    type: 'GET',
                    data: function(data) {
                        data.aksi = $('#filterAksi').val();
                        data.tabel = $('#filterData').val();
                        data.pengguna = $('#filterPengguna').val();
                        data.dari = $('#filterDari').val();
                        data.sampai = $('#filterSampai').val();
                    }
                },
                columns: [{
                        data: 'waktu_format',
                        name: 'waktu'
                    },
                    {
                        data: 'pengguna_nama',
                        name: 'pengguna_nama',
                        orderable: false
                    },
                    {
                        data: 'aksi_label',
                        name: 'aksi_label',
                        orderable: false
                    },
                    {
                        data: 'data_label',
                        name: 'data_label',
                        orderable: false
                    },
                    {
                        data: 'perubahan',
                        name: 'perubahan',
                        orderable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            $('#filterAksi, #filterData, #filterPengguna, #filterDari, #filterSampai').on('change', function() {
                tabel.ajax.reload();
            });
        });
    </script>
@endpush
