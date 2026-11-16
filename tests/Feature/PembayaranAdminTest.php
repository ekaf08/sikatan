<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_bendahara_dapat_melihat_rekap_pembayaran(): void
    {
        $bendahara = User::create([
            'name' => 'Bendahara Uji',
            'email' => 'bendahara.uji@babatan.test',
            'username' => 'bendahara.uji',
            'password' => 'password',
            'role' => 'bendahara',
            'aktif' => true,
        ]);

        Pembayaran::create([
            'order_id' => 'DONASI-ADMIN-1',
            'provider' => 'midtrans',
            'channel' => 'qris',
            'nominal' => 75000,
            'status' => 'settlement',
            'nama_donatur' => 'Siti',
            'donatur_anonim' => false,
        ]);

        $this->actingAs($bendahara)
            ->get(route('pembayaran'))
            ->assertOk()
            ->assertSee('Pembayaran online');

        $this->actingAs($bendahara)
            ->getJson(route('pembayaran.data'))
            ->assertOk()
            ->assertJsonPath('data.0.order_id', 'DONASI-ADMIN-1')
            ->assertJsonPath('data.0.status', 'settlement');
    }
}
