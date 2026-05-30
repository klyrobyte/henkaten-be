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
        // 1. Create SCS table
        Schema::create('scs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_label')->nullable();
            $table->string('gradient')->default('linear-gradient(135deg,#1f3c88,#2e57d4)');
            $table->integer('order_index')->default(0);
            $table->string('detail_departemen')->nullable();
            $table->timestamps();
        });

        // 2. Insert Default SC1
        DB::table('scs')->insert([
            'name' => 'SC 1',
            'slug' => 'sc-1',
            'short_label' => 'SC1',
            'gradient' => 'linear-gradient(135deg,#1f3c88,#2e57d4)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Add sc_id to related tables
        $tables = [
            'users',
            'factories',
            'statuses',
            'repair_departments',
            'site_configs',
            'absence_reasons'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->unsignedBigInteger('sc_id')->nullable()->after('id')->default(1);
                $table->foreign('sc_id')->references('id')->on('scs')->onDelete('cascade');
            });
        }

        // 4. Update constraints for Master Data (Multi-tenant unique)
        
        // Factories: name and slug unique per SC
        Schema::table('factories', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
            $table->unique(['sc_id', 'name']);
            $table->unique(['sc_id', 'slug']);
        });

        // Statuses: key unique per SC
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->unique(['sc_id', 'key']);
        });

        // Repair Departments: name unique per SC
        Schema::table('repair_departments', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['sc_id', 'name']);
        });

        // Site Configs: key unique per SC
        Schema::table('site_configs', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->unique(['sc_id', 'key']);
        });

        // Absence Reasons: name unique per SC
        Schema::table('absence_reasons', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['sc_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To be safe, we don't drop columns in down() for existing tables if it's destructive,
        // but for a clean rollback:
        $tables = [
            'users',
            'factories',
            'statuses',
            'repair_departments',
            'site_configs',
            'absence_reasons'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['sc_id']);
                $table->dropColumn('sc_id');
            });
        }

        Schema::dropIfExists('scs');
    }
};
