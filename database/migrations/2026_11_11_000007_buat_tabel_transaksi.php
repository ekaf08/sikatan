<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_dana_id')->constrained('pos_dana')->restrictOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rt')->restrictOnDelete();
            $table->foreignId('acara_id')->nullable()->constrained('acara')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();

            $table->enum('jenis', ['masuk', 'keluar']);
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);

            // khusus pemasukan
            $table->unsignedInteger('jumlah_kk_bayar')->nullable();
            $table->string('nama_donatur')->nullable();
            $table->boolean('donatur_anonim')->default(false);
            $table->string('tujuan')->nullable();

            // keterangan: internal hanya untuk bendahara/admin, publik tampil ke warga
            $table->text('catatan_internal')->nullable();
            $table->text('catatan_publik')->nullable();

            // khusus pengeluaran
            $table->text('alasan_selisih')->nullable();
            $table->boolean('sembunyikan_lampiran')->default(false);

            // pembatalan (void), transaksi tidak pernah dihapus
            $table->enum('status', ['aktif', 'batal'])->default('aktif');
            $table->text('alasan_batal')->nullable();

            $table->timestamps();

            $table->index('tanggal');
            $table->index(['pos_dana_id', 'tanggal']);
            $table->index(['jenis', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
