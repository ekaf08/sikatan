<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->string('nama');
            $table->decimal('nominal_standar', 15, 2)->nullable();
            $table->boolean('sensitif')->default(false);
            $table->timestamps();

            $table->unique(['jenis', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
