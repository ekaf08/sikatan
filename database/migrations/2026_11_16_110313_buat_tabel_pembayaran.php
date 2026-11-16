<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('provider')->default('midtrans');
            $table->string('channel')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->string('status')->default('pending'); // pending, settlement, expire, deny, cancel, gagal
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->nullOnDelete();
            $table->string('nama_donatur')->nullable();
            $table->boolean('donatur_anonim')->default(false);
            $table->string('tujuan')->nullable();
            $table->text('catatan_publik')->nullable();
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('snap_token')->nullable();
            $table->text('redirect_url')->nullable();
            $table->json('payload_request')->nullable();
            $table->json('payload_notifikasi')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('transaksi_id');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
