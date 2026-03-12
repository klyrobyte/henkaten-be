<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan 'tv' ke enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tl','gl','pengawas','tv') NOT NULL DEFAULT 'pengawas'");
    }

    public function down(): void
    {
        // Pindahkan user tv → pengawas sebelum hapus enum
        DB::statement("UPDATE users SET role = 'pengawas' WHERE role = 'tv'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','tl','gl','pengawas') NOT NULL DEFAULT 'pengawas'");
    }
};
