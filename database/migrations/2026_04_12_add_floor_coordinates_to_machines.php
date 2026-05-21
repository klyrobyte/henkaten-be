<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            // Floor plan coordinates (viewBox units, not pixels)
            $table->decimal('floor_cx', 8, 3)->nullable()->after('photo')->comment('SVG viewBox X coordinate');
            $table->decimal('floor_cy', 8, 3)->nullable()->after('floor_cx')->comment('SVG viewBox Y coordinate');
            $table->string('floor_plan')->nullable()->after('floor_cy')->comment('Which floor plan (e.g., "f2", "f3", "f4")');
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['floor_cx', 'floor_cy', 'floor_plan']);
        });
    }
};
