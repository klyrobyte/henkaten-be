<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('absence_summaries', 'op_cuti')) {
                $table->unsignedSmallInteger('op_cuti')->default(0)->after('total_member');
            }
            if (!Schema::hasColumn('absence_summaries', 'op_sakit')) {
                $table->unsignedSmallInteger('op_sakit')->default(0)->after('op_cuti');
            }
            if (!Schema::hasColumn('absence_summaries', 'op_ijin')) {
                $table->unsignedSmallInteger('op_ijin')->default(0)->after('op_sakit');
            }
            if (!Schema::hasColumn('absence_summaries', 'spv_cuti')) {
                $table->unsignedSmallInteger('spv_cuti')->default(0)->after('op_ijin');
            }
            if (!Schema::hasColumn('absence_summaries', 'spv_sakit')) {
                $table->unsignedSmallInteger('spv_sakit')->default(0)->after('spv_cuti');
            }
            if (!Schema::hasColumn('absence_summaries', 'spv_ijin')) {
                $table->unsignedSmallInteger('spv_ijin')->default(0)->after('spv_sakit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            $cols = ['op_cuti','op_sakit','op_ijin','spv_cuti','spv_sakit','spv_ijin'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('absence_summaries', $c));
            if ($existing) $table->dropColumn(array_values($existing));
        });
    }
};
