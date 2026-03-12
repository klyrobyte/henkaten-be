<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom factory dan shift ke tabel users.
     *
     * Hanya diisi untuk role: tl, gl, pengawas.
     * Admin dan tv biarkan null (akses tidak dibatasi).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('factory')->nullable()->after('role');
            $table->string('shift')->nullable()->after('factory');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['factory', 'shift']);
        });
    }
};
