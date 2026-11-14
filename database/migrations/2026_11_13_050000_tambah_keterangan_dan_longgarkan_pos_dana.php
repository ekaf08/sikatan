<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_dana', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE pos_dana DROP CONSTRAINT IF EXISTS pos_dana_jenis_check');
            DB::statement('ALTER TABLE pos_dana DROP CONSTRAINT IF EXISTS pos_dana_jenis_unique');
        } else {
            Schema::table('pos_dana', function (Blueprint $table) {
                $table->dropUnique(['jenis']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('pos_dana', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE pos_dana ADD CONSTRAINT pos_dana_jenis_check CHECK (jenis IN ('kematian', 'masjid', 'sedekah_bumi', 'umum'))");
            DB::statement('ALTER TABLE pos_dana ADD CONSTRAINT pos_dana_jenis_unique UNIQUE (jenis)');
        } else {
            Schema::table('pos_dana', function (Blueprint $table) {
                $table->unique('jenis');
            });
        }
    }
};
