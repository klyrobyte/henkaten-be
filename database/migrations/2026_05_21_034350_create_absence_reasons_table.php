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
        Schema::create('absence_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#888888');
            $table->timestamps();
        });

        // Seed default reasons
        DB::table('absence_reasons')->insert([
            ['name' => 'Cuti', 'color' => '#2196f3', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sakit', 'color' => '#ff9800', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ijin', 'color' => '#9c27b0', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alpha', 'color' => '#ef4444', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_reasons');
    }
};
