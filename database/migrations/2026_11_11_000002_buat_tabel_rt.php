<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rw')->restrictOnDelete();
            $table->string('nomor', 10);
            $table->string('nama_penarik')->nullable();
            $table->unsignedInteger('jumlah_kk')->default(0);
            $table->timestamps();

            $table->unique(['rw_id', 'nomor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rt');
    }
};
