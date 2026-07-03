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
        // Force drop and recreate using raw statements just in case Schema builder failed silently
        try {
            DB::statement('ALTER TABLE member_skills DROP INDEX member_skills_unique');
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }
        
        try {
            DB::statement('ALTER TABLE member_skills ADD UNIQUE INDEX member_skills_unique (sc_id, member_id, machine_name, process_name, factory)');
        } catch (\Exception $e) {
            // Ignore if it already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_skills', function (Blueprint $table) {
            $table->dropUnique('member_skills_unique');
            
            // This might fail if there are duplicates when rolling back, but we just restore the previous definition
            $table->unique(['sc_id', 'member_id', 'machine_name', 'factory'], 'member_skills_unique');
        });
    }
};
