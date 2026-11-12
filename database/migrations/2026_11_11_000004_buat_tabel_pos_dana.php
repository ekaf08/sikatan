<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_dana', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['kematian', 'masjid', 'sedekah_bumi', 'umum'])->unique();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->date('tanggal_saldo_awal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_dana');
    }
};
