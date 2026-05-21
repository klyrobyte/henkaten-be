<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('problem_logs', function (Blueprint $table) {
            $table->string('departemen_perbaikan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_logs', function (Blueprint $table) {
            $table->dropColumn('departemen_perbaikan');
        });
    }
};
