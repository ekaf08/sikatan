<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\Pembayaran\MidtransGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PembayaranPublikController extends Controller
{
    public function buat(Request $request, MidtransGateway $gateway): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'nama_donatur' => ['nullable', 'string', 'max:255'],
            'donatur_anonim' => ['nullable', 'boolean'],
            'tujuan' => ['nullable', 'string', 'max:255'],
            'catatan_publik' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
        ], [
            'nominal.required' => 'Nominal donasi wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Nominal minimal Rp1.000.',
            'nominal.max' => 'Nominal maksimal Rp100.000.000.',
        ]);

        $anonim = $request->boolean('donatur_anonim');
        $orderId = 'DONASI-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

        $pembayaran = Pembayaran::create([
            'order_id' => $orderId,
            'provider' => 'midtrans',
            'nominal' => $data['nominal'],
            'status' => 'pending',
            'nama_donatur' => $anonim ? null : ($data['nama_donatur'] ?? null),
            'donatur_anonim' => $anonim,
            'tujuan' => $data['tujuan'] ?? null,
            'catatan_publik' => $data['catatan_publik'] ?? null,
            'email' => $data['email'] ?? null,
            'no_hp' => $data['no_hp'] ?? null,
            'expired_at' => now()->addDay(),
        ]);

        try {
            $hasil = $gateway->buatSnap($pembayaran);
            $pembayaran->update([
                'snap_token' => $hasil['token'],
                'redirect_url' => $hasil['redirect_url'],
                'payload_request' => ['params_sent' => 'ok'],
            ]);
        } catch (\Throwable $e) {
            $pembayaran->update([
                'status' => 'gagal',
                'meta' => ['error' => $e->getMessage()],
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Gagal membuat pembayaran: '.$e->getMessage()], 422);
            }

            return redirect()->to(route('publik').'#donasi')->with('donasi_error', 'Gagal memproses pembayaran. Pastikan konfigurasi Midtrans sudah benar.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'order_id' => $pembayaran->order_id,
                'snap_token' => $pembayaran->snap_token,
                'redirect_url' => $pembayaran->redirect_url,
                'client_key' => $gateway->clientKey(),
                'finish_url' => route('donasi.selesai', ['order_id' => $pembayaran->order_id]),
            ]);
        }

        if ($pembayaran->redirect_url) {
            return redirect()->away($pembayaran->redirect_url);
        }

        return redirect()->route('donasi.selesai', ['order_id' => $pembayaran->order_id]);
    }

    public function selesai(Request $request): View
    {
        $orderId = (string) $request->query('order_id', '');
        $pembayaran = $orderId !== '' ? Pembayaran::query()->where('order_id', $orderId)->first() : null;

        return view('fo.donasi-selesai', [
            'pembayaran' => $pembayaran,
        ]);
    }
}
