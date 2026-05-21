<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hot-fix: add op_Alpha and spv_Alpha to absence_summaries.
 *
 * The 2026_03_09_update_absence_summaries_breakdown migration was recorded in
 * the migrations table but the columns were never actually applied to the live
 * database (schema drift, likely caused by a DB restore from an older dump).
 * This migration adds the two missing columns safely with hasColumn guards so
 * it is idempotent - running it twice is harmless.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('absence_summaries', 'op_Alpha')) {
                $table->unsignedSmallInteger('op_Alpha')->default(0)->after('op_ijin');
            }
            if (!Schema::hasColumn('absence_summaries', 'spv_Alpha')) {
                // Place after spv_ijin if it exists, otherwise just append
                if (Schema::hasColumn('absence_summaries', 'spv_ijin')) {
                    $table->unsignedSmallInteger('spv_Alpha')->default(0)->after('spv_ijin');
                } else {
                    $table->unsignedSmallInteger('spv_Alpha')->default(0);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('absence_summaries', 'op_Alpha'))  $cols[] = 'op_Alpha';
            if (Schema::hasColumn('absence_summaries', 'spv_Alpha')) $cols[] = 'spv_Alpha';
            if ($cols) $table->dropColumn($cols);
        });
    }
};
