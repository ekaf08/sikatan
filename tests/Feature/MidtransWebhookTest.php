<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Database\Seeders\KategoriSeeder;
use Database\Seeders\PenggunaSeeder;
use Database\Seeders\PosDanaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'SB-Mid-server-TEST';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => self::SERVER_KEY,
            'services.midtrans.client_key' => 'SB-Mid-client-TEST',
            'services.midtrans.is_production' => false,
        ]);

        $this->seed([
            PenggunaSeeder::class,
            PosDanaSeeder::class,
            KategoriSeeder::class,
        ]);
    }

    public function test_webhook_menolak_signature_tidak_valid(): void
    {
        $pembayaran = $this->buatPembayaran();
        $payload = $this->payload($pembayaran);
        $payload['signature_key'] = 'signature-palsu';

        $this->postJson(route('webhook.midtrans'), $payload)->assertStatus(403);

        $this->assertSame('pending', $pembayaran->fresh()->status);
        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_settlement_membuat_transaksi_dan_menautkan_pembayaran(): void
    {
        $pembayaran = $this->buatPembayaran();

        $this->postJson(route('webhook.midtrans'), $this->payload($pembayaran))->assertOk();

        $pembayaran->refresh();

        $this->assertSame('settlement', $pembayaran->status);
        $this->assertNotNull($pembayaran->transaksi_id);
        $this->assertNotNull($pembayaran->dibayar_pada);
        $this->assertSame('qris', $pembayaran->channel);

        $transaksi = Transaksi::query()->findOrFail($pembayaran->transaksi_id);

        $this->assertSame('masuk', $transaksi->jenis);
        $this->assertSame('aktif', $transaksi->status);
        $this->assertEquals(50000, (float) $transaksi->nominal);
        $this->assertSame('Budi', $transaksi->nama_donatur);
        $this->assertFalse($transaksi->donatur_anonim);
        $this->assertSame('masjid', $transaksi->posDana->jenis);
        $this->assertSame('Donasi Masjid', $transaksi->kategori->nama);
        $this->assertStringContainsString('Donasi online', (string) $transaksi->catatan_publik);
        $this->assertStringContainsString($pembayaran->order_id, (string) $transaksi->catatan_internal);

        $this->assertDatabaseHas('log_audit', [
            'nama_tabel' => 'transaksi',
            'id_data' => $transaksi->id,
            'aksi' => 'tambah',
        ]);
    }

    public function test_notifikasi_duplikat_tidak_menggandakan_transaksi(): void
    {
        $pembayaran = $this->buatPembayaran();
        $payload = $this->payload($pembayaran);

        $this->postJson(route('webhook.midtrans'), $payload)->assertOk();
        $transaksiId = $pembayaran->fresh()->transaksi_id;

        $this->postJson(route('webhook.midtrans'), $payload)->assertOk();

        $this->assertSame(1, Transaksi::query()->count());
        $this->assertSame($transaksiId, $pembayaran->fresh()->transaksi_id);
        $this->assertSame(1, DB::table('log_audit')
            ->where('nama_tabel', 'transaksi')
            ->where('aksi', 'tambah')
            ->count());
    }

    public function test_status_pending_tidak_membuat_transaksi(): void
    {
        $pembayaran = $this->buatPembayaran();

        $this->postJson(route('webhook.midtrans'), $this->payload($pembayaran, [
            'transaction_status' => 'pending',
        ]))->assertOk();

        $this->assertSame('pending', $pembayaran->fresh()->status);
        $this->assertNull($pembayaran->fresh()->transaksi_id);
        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_status_expire_tidak_membuat_transaksi(): void
    {
        $pembayaran = $this->buatPembayaran();

        $this->postJson(route('webhook.midtrans'), $this->payload($pembayaran, [
            'transaction_status' => 'expire',
        ]))->assertOk();

        $this->assertSame('expire', $pembayaran->fresh()->status);
        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_nominal_tidak_cocok_menandai_gagal_tanpa_transaksi(): void
    {
        $pembayaran = $this->buatPembayaran();

        $this->postJson(route('webhook.midtrans'), $this->payload($pembayaran, [
            'gross_amount' => '60000.00',
        ]))->assertOk();

        $pembayaran->refresh();

        $this->assertSame('gagal', $pembayaran->status);
        $this->assertNull($pembayaran->transaksi_id);
        $this->assertSame('Nominal notifikasi tidak cocok.', $pembayaran->meta['error_terakhir'] ?? null);
        $this->assertSame(0, Transaksi::query()->count());
    }

    public function test_master_donasi_hilang_mencatat_kegagalan_tanpa_transaksi(): void
    {
        $pembayaran = $this->buatPembayaran();

        Kategori::query()->where('jenis', 'masuk')->where('nama', 'Donasi Masjid')->delete();

        $this->postJson(route('webhook.midtrans'), $this->payload($pembayaran))
            ->assertOk()
            ->assertJson(['message' => 'Diproses dengan catatan']);

        $pembayaran->refresh();

        $this->assertSame('pending', $pembayaran->status);
        $this->assertNull($pembayaran->transaksi_id);
        $this->assertStringContainsString('Master', (string) ($pembayaran->meta['error_terakhir'] ?? ''));
        $this->assertSame(0, Transaksi::query()->count());
    }

    private function buatPembayaran(array $override = []): Pembayaran
    {
        return Pembayaran::create(array_merge([
            'order_id' => 'DONASI-TEST-'.now()->format('YmdHis'),
            'provider' => 'midtrans',
            'nominal' => 50000,
            'status' => 'pending',
            'nama_donatur' => 'Budi',
            'donatur_anonim' => false,
            'tujuan' => 'Renovasi masjid',
            'catatan_publik' => 'Semoga berkah',
        ], $override));
    }

    private function payload(Pembayaran $pembayaran, array $override = []): array
    {
        $payload = array_merge([
            'order_id' => $pembayaran->order_id,
            'status_code' => '200',
            'gross_amount' => number_format((float) $pembayaran->nominal, 2, '.', ''),
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ], $override);

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].self::SERVER_KEY
        );

        return $payload;
    }
}
