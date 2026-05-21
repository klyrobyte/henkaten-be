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
        Schema::table('absence_records', function (Blueprint $table) {
            $table->string('reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_records', function (Blueprint $table) {
            $table->enum('reason', ['Cuti', 'Sakit', 'Ijin', 'Alpha', 'Tugas'])->nullable()->change();
        });
    }
};
