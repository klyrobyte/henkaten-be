<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignment_replacements', function (Blueprint $table) {
            $table->dropUnique('unique_replacement');
            $table->unique(
                ['tanggal', 'factory', 'shift', 'member_id', 'target_machine'],
                'unique_replacement'
            );
        });
    }

    public function down(): void
    {
        Schema::table('assignment_replacements', function (Blueprint $table) {
            $table->dropUnique('unique_replacement');
            $table->unique(
                ['tanggal', 'factory', 'shift', 'member_id'],
                'unique_replacement'
            );
        });
    }
};
