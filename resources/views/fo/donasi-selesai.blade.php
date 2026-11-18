<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Status Donasi - Sistem Kampung Babatan</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('sikatan/assets/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        @include('fo.partials.public-header')

        <main class="public-main">
            <div class="card" style="max-width:720px;margin:0 auto;">
                <div class="card-body">
                    <h1 class="section-title" style="font-size:20px;color:#17443a">Status Donasi</h1>

                    @if (! $pembayaran)
                        <p class="mt-3">Data pembayaran tidak ditemukan. Jika Anda baru saja membayar, status dapat diperbarui beberapa saat setelah notifikasi diterima.</p>
                    @else
                        @php
                            $status = $pembayaran->status;
                            $label = match ($status) {
                                'settlement' => 'Berhasil',
                                'pending' => 'Menunggu Pembayaran',
                                'expire' => 'Kedaluwarsa',
                                'deny', 'cancel', 'gagal' => 'Gagal/Dibatalkan',
                                default => ucfirst($status),
                            };
                            $alert = $status === 'settlement' ? 'success' : ($status === 'pending' ? 'warning' : 'danger');
                        @endphp
                        <div class="alert alert-{{ $alert }} mt-3">
                            <strong>Status: {{ $label }}</strong>
                        </div>

                        <table class="table-app mt-2">
                            <tbody>
                                <tr><th style="width:220px">Order ID</th><td>{{ $pembayaran->order_id }}</td></tr>
                                <tr><th>Nominal</th><td>Rp{{ number_format((float) $pembayaran->nominal, 0, ',', '.') }}</td></tr>
                                <tr><th>Donatur</th><td>{{ $pembayaran->donatur_anonim ? 'Hamba Allah' : ($pembayaran->nama_donatur ?: '-') }}</td></tr>
                                <tr><th>Tujuan</th><td>{{ $pembayaran->tujuan ?: '-' }}</td></tr>
                                <tr><th>Waktu Dibayar</th><td>{{ $pembayaran->dibayar_pada ? $pembayaran->dibayar_pada->format('d M Y H:i') : '-' }}</td></tr>
                                <tr><th>Transaksi Kas</th><td>{{ $pembayaran->transaksi_id ? 'Tercatat (#'.$pembayaran->transaksi_id.')' : 'Belum tercatat' }}</td></tr>
                            </tbody>
                        </table>

                        @if ($status === 'settlement')
                            <p class="hint mt-3">Pembayaran berhasil diverifikasi dan telah dicatat otomatis ke kas (pemasukan). Terima kasih atas donasi Anda.</p>
                        @elseif ($status === 'pending')
                            <p class="hint mt-3">Silakan selesaikan pembayaran. Halaman ini menampilkan status lokal; status final mengikuti notifikasi dari Midtrans.</p>
                        @else
                            <p class="hint mt-3">Pembayaran tidak berhasil atau kedaluwarsa. Tidak ada transaksi kas yang dibuat. Anda dapat mencoba membuat donasi baru.</p>
                        @endif
                    @endif

                    <div class="mt-4">
                        <a class="btn btn-brand" href="{{ route('donasi') }}">Kembali ke Donasi</a>
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
