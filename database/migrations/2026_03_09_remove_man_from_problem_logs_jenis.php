<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus baris Man yang sudah ada (opsional — bisa dikomentari jika ingin keep data lama)
        DB::table('problem_logs')->where('jenis', 'Man')->delete();

        // Ubah enum: hapus 'Man'
        DB::statement("ALTER TABLE problem_logs MODIFY COLUMN jenis ENUM('Material','Machine','Method') NOT NULL");
    }

    public function down(): void
    {
        // Rollback ke enum semula
        DB::statement("ALTER TABLE problem_logs MODIFY COLUMN jenis ENUM('Man','Material','Machine','Method') NOT NULL");
    }
};
