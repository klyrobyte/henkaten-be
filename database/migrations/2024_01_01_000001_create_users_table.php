<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan migration users default Laravel.
     *
     * SETUP: Hapus atau jangan jalankan migration bawaan Laravel:
     *   database/migrations/0001_01_01_000000_create_users_table.php
     *   database/migrations/0001_01_01_000001_create_cache_table.php
     *   database/migrations/0001_01_01_000002_create_jobs_table.php
     *
     * Lalu jalankan:
     *   php artisan migrate
     */
    public function up(): void
    {
        // Drop tabel default jika ada (dari migration bawaan Laravel)
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['superadmin', 'admin', 'operator'])->default('operator');
            $table->string('name');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
