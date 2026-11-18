<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('induk_id')->nullable()->constrained('menu')->nullOnDelete();
            $table->string('judul');
            $table->string('slug')->nullable();
            $table->string('tipe', 20)->default('tautan'); // tautan, route, grup
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->json('route_params')->nullable();
            $table->string('ikon', 100)->nullable();
            $table->string('target', 20)->default('_self');
            $table->string('area', 30)->default('publik'); // publik, admin
            $table->string('bagian', 50)->nullable(); // mis. utama, sidebar-transaksi, footer
            $table->unsignedInteger('urutan')->default(0);
            $table->string('peran', 100)->nullable(); // contoh: semua, tamu, auth, admin, bendahara, admin,bendahara
            $table->boolean('aktif')->default(true);
            $table->boolean('tampil_di_mobile')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['area', 'aktif', 'urutan']);
            $table->index(['induk_id', 'urutan']);
            $table->unique(['area', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
