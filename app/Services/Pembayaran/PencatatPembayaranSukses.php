<?php

namespace App\Services\Pembayaran;

use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\PosDana;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\PencatatAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PencatatPembayaranSukses
{
    public function __construct(private readonly PencatatAudit $audit) {}

    /**
     * Proses notifikasi sukses secara idempotent. Return pembayaran terbaru.
     *
     * @param array<string,mixed> $payload
     */
    public function proses(Pembayaran $pembayaran, array $payload, string $channel = null): Pembayaran
    {
        return DB::transaction(function () use ($pembayaran, $payload, $channel): Pembayaran {
            /** @var Pembayaran $terkunci */
            $terkunci = Pembayaran::query()->whereKey($pembayaran->id)->lockForUpdate()->firstOrFail();

            $meta = $terkunci->meta ?? [];
            $meta['notifikasi_terakhir'] = now()->toIso8601String();
            $terkunci->payload_notifikasi = $payload;
            $terkunci->channel = $channel ?: ($payload['payment_type'] ?? $terkunci->channel);
            $terkunci->meta = $meta;

            if ($terkunci->status === 'settlement' && $terkunci->transaksi_id) {
                $terkunci->save();
                return $terkunci->refresh();
            }

            $grossAmount = (string) ($payload['gross_amount'] ?? '');
            $nominalOrder = number_format((float) $terkunci->nominal, 2, '.', '');
            $nominalNotif = is_numeric($grossAmount) ? number_format((float) $grossAmount, 2, '.', '') : $grossAmount;
            if ($nominalNotif !== $nominalOrder) {
                $meta['error_terakhir'] = 'Nominal notifikasi tidak cocok.';
                $terkunci->status = 'gagal';
                $terkunci->meta = $meta;
                $terkunci->save();
                Log::warning('Midtrans nominal mismatch', ['order_id' => $terkunci->order_id, 'order' => $nominalOrder, 'notif' => $nominalNotif]);
                return $terkunci->refresh();
            }

            try {
                $transaksi = $this->buatTransaksiDonasi($terkunci);
            } catch (\Throwable $e) {
                $meta['error_terakhir'] = $e->getMessage();
                $terkunci->meta = $meta;
                $terkunci->save();
                Log::error('Gagal auto-posting transaksi pembayaran', ['order_id' => $terkunci->order_id, 'error' => $e->getMessage()]);
                throw $e;
            }

            $terkunci->status = 'settlement';
            $terkunci->transaksi_id = $transaksi->id;
            $terkunci->dibayar_pada = now();
            $terkunci->save();

            $this->audit->catat($this->userSistem(), 'transaksi', $transaksi->id, 'tambah', null, $this->nilaiAudit($transaksi));
            $this->audit->catat($this->userSistem(), 'pembayaran', $terkunci->id, 'ubah', ['status' => 'pending'], ['status' => 'settlement', 'transaksi_id' => $transaksi->id]);

            return $terkunci->refresh();
        });
    }

    /**
     * @param array<string,mixed> $payload
     */
    public function prosesStatusNonSukses(Pembayaran $pembayaran, string $status, array $payload): Pembayaran
    {
        return DB::transaction(function () use ($pembayaran, $status, $payload): Pembayaran {
            /** @var Pembayaran $terkunci */
            $terkunci = Pembayaran::query()->whereKey($pembayaran->id)->lockForUpdate()->firstOrFail();
            $terkunci->payload_notifikasi = $payload;
            $terkunci->channel = $payload['payment_type'] ?? $terkunci->channel;

            // Jangan menurunkan status jika sudah settlement & punya transaksi (uang sudah masuk kas)
            if ($terkunci->status === 'settlement' && $terkunci->transaksi_id) {
                $terkunci->save();
                return $terkunci->refresh();
            }

            $sebelum = $terkunci->status;
            $terkunci->status = $status;
            $terkunci->save();
            $this->audit->catat($this->userSistem(), 'pembayaran', $terkunci->id, 'ubah', ['status' => $sebelum], ['status' => $status]);

            return $terkunci->refresh();
        });
    }

    private function buatTransaksiDonasi(Pembayaran $pembayaran): Transaksi
    {
        $kategori = Kategori::query()->where('jenis', 'masuk')->where('nama', 'Donasi Masjid')->first();
        $pos = PosDana::query()->where('jenis', 'masjid')->first();

        if (! $kategori || ! $pos) {
            throw new RuntimeException('Master pos/kategori donasi (Kas Masjid / Donasi Masjid) belum tersedia.');
        }

        $user = $this->userSistem();

        return Transaksi::create([
            'pos_dana_id' => $pos->id,
            'kategori_id' => $kategori->id,
            'rt_id' => null,
            'acara_id' => null,
            'dibuat_oleh' => $user->id,
            'jenis' => 'masuk',
            'tanggal' => now()->toDateString(),
            'nominal' => $pembayaran->nominal,
            'nama_donatur' => $pembayaran->nama_donatur,
            'donatur_anonim' => (bool) $pembayaran->donatur_anonim,
            'tujuan' => $pembayaran->tujuan,
            'catatan_publik' => $this->catatanPublik($pembayaran),
            'catatan_internal' => 'Otomatis dari pembayaran online Midtrans (order: '.$pembayaran->order_id.').',
            'status' => 'aktif',
        ]);
    }

    private function catatanPublik(Pembayaran $pembayaran): ?string
    {
        $catatan = trim((string) ($pembayaran->catatan_publik ?? ''));
        $prefix = 'Donasi online';

        if ($catatan === '') {
            return $prefix;
        }

        return $prefix.' - '.$catatan;
    }

    private function userSistem(): User
    {
        $user = User::query()->where('email', 'sistem@babatan.test')->first()
            ?? User::query()->where('username', 'sistem')->first();

        if (! $user) {
            throw new RuntimeException('User sistem untuk auto-posting tidak ditemukan. Jalankan seeder pengguna.');
        }

        return $user;
    }

    private function nilaiAudit(Transaksi $transaksi): array
    {
        $transaksi->loadMissing(['posDana', 'kategori']);

        return [
            'tanggal' => $transaksi->tanggal?->format('Y-m-d'),
            'jenis' => 'masuk',
            'pos' => $transaksi->posDana?->nama,
            'kategori' => $transaksi->kategori?->nama,
            'nominal' => (float) $transaksi->nominal,
            'donatur' => $transaksi->donatur_anonim ? 'Hamba Allah' : $transaksi->nama_donatur,
            'tujuan' => $transaksi->tujuan,
            'catatan_publik' => $transaksi->catatan_publik,
            'status' => $transaksi->status,
            'sumber' => 'midtrans',
        ];
    }
}
