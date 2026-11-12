<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_tabel');
            $table->unsignedBigInteger('id_data');
            $table->enum('aksi', ['tambah', 'ubah', 'batal']);
            $table->json('nilai_lama')->nullable();
            $table->json('nilai_baru')->nullable();
            $table->timestamp('waktu')->useCurrent();

            $table->index(['nama_tabel', 'id_data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_audit');
    }
};
