<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\Pembayaran\MidtransGateway;
use App\Services\Pembayaran\PencatatPembayaranSukses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransGateway $gateway, PencatatPembayaranSukses $pencatat): JsonResponse
    {
        $payload = $request->all();
        $orderId = (string) ($payload['order_id'] ?? '');

        if ($orderId === '' || ! $gateway->signatureValid($payload)) {
            Log::warning('Midtrans webhook signature invalid', ['order_id' => $orderId]);
            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        $pembayaran = Pembayaran::query()->where('order_id', $orderId)->first();
        if (! $pembayaran) {
            Log::warning('Midtrans webhook order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $hasil = $gateway->petakanStatus($payload);

        try {
            if ($hasil['dibayar']) {
                $pencatat->proses($pembayaran, $payload, (string) ($payload['payment_type'] ?? ''));
            } else {
                $pencatat->prosesStatusNonSukses($pembayaran, $hasil['status'], $payload);
            }
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook processing error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            // Tetap 200 untuk hindari retry tak terkendali pada error logika; status/meta tersimpan untuk investigasi.
            // Jika ingin retry Midtrans pada kegagalan sementara (mis. master belum ada), ubah ke 500.
            return response()->json(['message' => 'Diproses dengan catatan'], 200);
        }

        return response()->json(['message' => 'OK']);
    }
}
