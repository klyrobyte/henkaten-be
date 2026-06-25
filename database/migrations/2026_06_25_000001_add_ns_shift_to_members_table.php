<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan nilai 'NS' (Non-Shift) pada kolom shift di tabel members.
 *
 * Member NS = tidak terikat shift A/B secara tetap, melainkan
 * mengikuti shift yang sedang berjalan berdasarkan rotasi mingguan.
 *
 * Kolom shift diubah dari enum('A','B') menjadi enum('A','B','NS').
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum langsung
        DB::statement("ALTER TABLE members MODIFY COLUMN shift ENUM('A', 'B', 'NS') NOT NULL DEFAULT 'A'");
    }

    public function down(): void
    {
        // Sebelum rollback: pastikan tidak ada member NS aktif
        DB::table('members')->where('shift', 'NS')->update(['shift' => 'A']);
        DB::statement("ALTER TABLE members MODIFY COLUMN shift ENUM('A', 'B') NOT NULL DEFAULT 'A'");
    }
};
