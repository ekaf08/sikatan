<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dari_pos_id')->constrained('pos_dana')->restrictOnDelete();
            $table->foreignId('ke_pos_id')->constrained('pos_dana')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_dana');
    }
};
