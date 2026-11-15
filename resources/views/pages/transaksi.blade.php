@extends('layouts.app')

@section('title', 'Daftar Transaksi - Kas Desa Babatan')

@section('topbarTitle', 'Daftar transaksi')
@section('topbarSubtitle', 'Semua catatan kas')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Daftar transaksi</h1>
        <p class="page-subtitle">Rekapan seluruh pemasukan dan pengeluaran kas desa.</p>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title mb-3">Riwayat transaksi</h2>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelTransaksi">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Pos dana</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th class="text-end">Nominal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Rekapan otomatis dari menu pemasukan dan pengeluaran. Data yang dibatalkan tetap tampil sebagai arsip.</p>
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

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    } [character];
                });
            }

            const bahasaDataTables = {
                search: 'Cari:',
                lengthMenu: 'Tampil _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
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

            $('#tabelTransaksi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tanggal_format',
                        name: 'tanggal'
                    },
                    {
                        data: 'jenis_label',
                        name: 'jenis_label',
                        orderable: false
                    },
                    {
                        data: 'pos_nama',
                        name: 'pos_nama',
                        orderable: false
                    },
                    {
                        data: 'kategori_nama',
                        name: 'kategori_nama',
                        orderable: false,
                        render: function(data) {
                            return '<strong>' + escapeHtml(data) + '</strong>';
                        }
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false
                    },
                    {
                        data: 'nominal_format',
                        name: 'nominal',
                        className: 'text-end',
                        searchable: false
                    },
                    {
                        data: 'status_label',
                        name: 'status_nilai',
                        orderable: false,
                        searchable: false
                    }
                ],
                createdRow: function(row, data) {
                    if (data.status_nilai === 'batal') {
                        row.classList.add('is-cancelled');
                    }
                },
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });
        });
    </script>
@endpush
