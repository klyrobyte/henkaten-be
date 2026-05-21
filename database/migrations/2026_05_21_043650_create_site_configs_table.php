<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default theme settings
        $now = now();
        DB::table('site_configs')->insert([
            ['key' => 'navbar_color',    'value' => '#2E7D32', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'primary_color',   'value' => '#2E7D32', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'secondary_color', 'value' => '#729E3F', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'theme_effect',    'value' => 'normal',  'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_configs');
    }
};
