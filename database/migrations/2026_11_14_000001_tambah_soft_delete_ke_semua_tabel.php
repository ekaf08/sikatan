<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tabel = [
        'users',
        'rw',
        'rt',
        'pos_dana',
        'kategori',
        'acara',
        'anggaran_acara',
        'transaksi',
        'lampiran',
        'mutasi_dana',
        'log_audit',
        'pengaturan',
    ];

    public function up(): void
    {
        foreach ($this->tabel as $nama) {
            if (! Schema::hasTable($nama) || Schema::hasColumn($nama, 'deleted_at')) {
                continue;
            }

            Schema::table($nama, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tabel as $nama) {
            if (! Schema::hasTable($nama) || ! Schema::hasColumn($nama, 'deleted_at')) {
                continue;
            }

            Schema::table($nama, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
