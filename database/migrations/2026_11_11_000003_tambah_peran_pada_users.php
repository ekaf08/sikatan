<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'bendahara'])->default('bendahara')->after('password');
            $table->string('username')->unique()->after('name')->nullable();
            $table->boolean('aktif')->default(true)->after('peran');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peran', 'aktif']);
        });
    }
};
