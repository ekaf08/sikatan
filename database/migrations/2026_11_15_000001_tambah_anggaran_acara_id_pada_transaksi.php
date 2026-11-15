<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('transaksi', 'anggaran_acara_id')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('anggaran_acara_id')
                ->nullable()
                ->after('acara_id')
                ->constrained('anggaran_acara')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('transaksi', 'anggaran_acara_id')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anggaran_acara_id');
        });
    }
};
