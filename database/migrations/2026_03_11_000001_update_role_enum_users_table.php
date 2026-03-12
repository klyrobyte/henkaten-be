<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum role yang lama (superadmin/admin/operator)
        // menjadi (admin/tl/gl/pengawas)
        // Gunakan raw SQL karena Laravel Blueprint tidak support ALTER ENUM langsung di MariaDB

        // 1. Konversi semua superadmin & admin lama → admin baru
        DB::statement("UPDATE users SET role = 'admin' WHERE role IN ('superadmin', 'admin')");

        // 2. Konversi operator lama → pengawas
        DB::statement("UPDATE users SET role = 'pengawas' WHERE role = 'operator'");

        // 3. Ubah definisi kolom enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tl','gl','pengawas') NOT NULL DEFAULT 'pengawas'");
    }

    public function down(): void
    {
        // Kembalikan ke enum lama jika rollback
        DB::statement("UPDATE users SET role = 'admin' WHERE role IN ('tl', 'gl')");
        DB::statement("UPDATE users SET role = 'operator' WHERE role = 'pengawas'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','operator') NOT NULL DEFAULT 'operator'");
    }
};
