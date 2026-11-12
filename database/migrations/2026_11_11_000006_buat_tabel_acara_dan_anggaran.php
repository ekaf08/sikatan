<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acara', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedSmallInteger('tahun');
            $table->enum('status', ['rencana', 'berjalan', 'selesai'])->default('rencana');
            $table->timestamps();
        });

        Schema::create('anggaran_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acara_id')->constrained('acara')->cascadeOnDelete();
            $table->string('uraian');
            $table->decimal('nominal_anggaran', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran_acara');
        Schema::dropIfExists('acara');
    }
};
