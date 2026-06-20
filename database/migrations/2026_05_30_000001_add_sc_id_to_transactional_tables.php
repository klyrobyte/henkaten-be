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
        $tables = [
            'machines',
            'members',
            'problem_logs',
            'absence_summaries',
            'absence_records',
            'daily_assignments',
            'assignment_replacements'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('sc_id')->nullable()->after('id')->default(1);
                $table->foreign('sc_id')->references('id')->on('scs')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'machines',
            'members',
            'problem_logs',
            'absence_summaries',
            'absence_records',
            'daily_assignments',
            'assignment_replacements'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['sc_id']);
                $table->dropColumn('sc_id');
            });
        }
    }
};
