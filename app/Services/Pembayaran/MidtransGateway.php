<?php

namespace App\Services\Pembayaran;

use App\Models\Pembayaran;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;

class MidtransGateway
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$clientKey = (string) config('services.midtrans.client_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function clientKey(): string
    {
        return (string) config('services.midtrans.client_key');
    }

    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    /**
     * @return array{token: ?string, redirect_url: ?string, response: mixed}
     */
    public function buatSnap(Pembayaran $pembayaran): array
    {
        $this->pastikanKonfigurasi();

        $params = [
            'transaction_details' => [
                'order_id' => $pembayaran->order_id,
                'gross_amount' => (int) round((float) $pembayaran->nominal),
            ],
            'item_details' => [[
                'id' => 'donasi',
                'price' => (int) round((float) $pembayaran->nominal),
                'quantity' => 1,
                'name' => 'Donasi Kas Desa',
            ]],
            'customer_details' => array_filter([
                'first_name' => $pembayaran->donatur_anonim ? 'Hamba Allah' : ($pembayaran->nama_donatur ?: 'Donatur'),
                'email' => $pembayaran->email,
                'phone' => $pembayaran->no_hp,
            ]),
        ];

        $snap = Snap::createTransaction($params);

        return [
            'token' => $snap->token ?? null,
            'redirect_url' => $snap->redirect_url ?? null,
            'response' => $snap,
        ];
    }

    public function signatureValid(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signature = (string) ($payload['signature_key'] ?? '');
        $serverKey = (string) config('services.midtrans.server_key');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signature === '' || $serverKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signature);
    }

    /**
     * @return array{status:string, dibayar:bool}
     */
    public function petakanStatus(array $payload): array
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                return ['status' => 'pending', 'dibayar' => false];
            }

            return ['status' => 'settlement', 'dibayar' => true];
        }

        if ($transactionStatus === 'settlement') {
            return ['status' => 'settlement', 'dibayar' => true];
        }

        if ($transactionStatus === 'pending') {
            return ['status' => 'pending', 'dibayar' => false];
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            return ['status' => $transactionStatus, 'dibayar' => false];
        }

        return ['status' => 'gagal', 'dibayar' => false];
    }

    private function pastikanKonfigurasi(): void
    {
        if (config('services.midtrans.server_key') === null || config('services.midtrans.server_key') === '') {
            throw new RuntimeException('Konfigurasi Midtrans (server key) belum diatur.');
        }
    }
}
