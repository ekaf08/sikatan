@extends('layouts.app')

@section('title', 'Pembayaran Online - Sistem Kampung Babatan')

@section('topbarTitle', 'Pembayaran online')
@section('topbarSubtitle', 'Rekap donasi Midtrans')

@section('content')
    <div class="mb-4">
        <h1 class="page-title">Pembayaran online</h1>
        <p class="page-subtitle">Rekap pembayaran donasi melalui Midtrans. Hanya bisa dibaca, tidak bisa diubah atau dihapus.
        </p>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3 align-items-end" id="formFilterPembayaran">
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label-app" for="filterStatus">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua status</option>
                        <option value="pending">Pending</option>
                        <option value="settlement">Berhasil</option>
                        <option value="expire">Kedaluwarsa</option>
                        <option value="deny">Ditolak</option>
                        <option value="cancel">Dibatalkan</option>
                        <option value="gagal">Gagal</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label-app" for="filterDari">Dari tanggal</label>
                    <input class="form-control" id="filterDari" type="date">
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label-app" for="filterSampai">Sampai tanggal</label>
                    <input class="form-control" id="filterSampai" type="date">
                </div>

                <div class="col-12 col-md-6 col-xl-3 d-flex gap-2">
                    <button class="btn btn-brand flex-fill" id="btnTerapkanFilter" type="submit">
                        <i class="bi bi-search me-1"></i>Terapkan
                    </button>
                    <button class="btn btn-outline-brand" id="btnResetFilter" type="button" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="section-title mb-3">Riwayat pembayaran</h2>

            <div class="table-responsive">
                <table class="table-app w-100 align-middle" id="tabelPembayaran">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="min-width: 130px;">Dibuat</th>
                            <th>Order ID</th>
                            <th>Donatur</th>
                            <th>Channel</th>
                            <th class="text-end">Nominal</th>
                            <th>Status</th>
                            <th style="width: 110px;">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="hint mt-3">Transaksi kas dibuat otomatis hanya setelah pembayaran terverifikasi berhasil (webhook).</p>
        </div>
    </div>

    <div class="modal fade" id="modalDetailTransaksi" tabindex="-1" aria-labelledby="modalDetailTransaksiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="modalDetailTransaksiLabel">Detail transaksi</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="detailTransaksiBody">
                    <div class="text-center text-muted py-4">Memuat detail...</div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.DataTable) {
                return;
            }

            const $ = window.jQuery;
            const detailUrl = "{{ route('transaksi.detail', ['id' => '__ID__']) }}";
            const modalDetail = new bootstrap.Modal(document.getElementById('modalDetailTransaksi'));
            const detailBody = document.getElementById('detailTransaksiBody');

            const bahasaDataTables = {
                search: 'Cari:',
                lengthMenu: 'Tampil _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ pembayaran',
                infoEmpty: 'Tidak ada pembayaran',
                infoFiltered: '(difilter dari _MAX_ total pembayaran)',
                zeroRecords: 'Pembayaran tidak ditemukan',
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

            const tabel = $('#tabelPembayaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pembayaran.data') }}",
                    type: 'GET',
                    data: function(data) {
                        data.status = $('#filterStatus').val();
                        data.dari = $('#filterDari').val();
                        data.sampai = $('#filterSampai').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'dibuat_format',
                        name: 'created_at'
                    },
                    {
                        data: 'order_id',
                        name: 'order_id'
                    },
                    {
                        data: 'donatur_label',
                        name: 'donatur_label',
                        orderable: false
                    },
                    {
                        data: 'channel_label',
                        name: 'channel_label',
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
                        name: 'status_label',
                        orderable: false
                    },
                    {
                        data: 'transaksi_label',
                        name: 'transaksi_label',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                language: bahasaDataTables,
                dom: domDataTables
            });

            $('#formFilterPembayaran').on('submit', function(event) {
                event.preventDefault();

                const dari = $('#filterDari').val();
                const sampai = $('#filterSampai').val();

                if (dari && sampai && dari > sampai) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rentang tanggal tidak valid',
                        text: 'Tanggal "Dari" tidak boleh melebihi tanggal "Sampai".'
                    });

                    return;
                }

                tabel.ajax.reload();
            });

            $('#btnResetFilter').on('click', function() {
                document.getElementById('formFilterPembayaran').reset();

                $('#filterDari, #filterSampai').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.clear();
                    }
                });

                tabel.ajax.reload();
            });

            $('#tabelPembayaran').on('click', '.btn-detail-transaksi', function() {
                const id = this.dataset.id;

                detailBody.innerHTML = '<div class="text-center text-muted py-4">Memuat detail...</div>';
                modalDetail.show();

                fetch(detailUrl.replace('__ID__', id), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal memuat detail transaksi.');
                        }

                        return response.text();
                    })
                    .then(function(html) {
                        detailBody.innerHTML = html;
                    })
                    .catch(function() {
                        detailBody.innerHTML =
                            '<div class="alert alert-danger mb-0">Detail transaksi tidak dapat dimuat.</div>';
                    });
            });
        });
    </script>
@endpush
