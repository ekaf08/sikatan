<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('transaksi', 'penerima')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('catatan_internal');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('transaksi', 'penerima')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('penerima');
        });
    }
};
