<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sc_id');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('machine_name');   // nama mesin/pos
            $table->string('factory');        // untuk scope per factory
            $table->tinyInteger('skill_pct')->default(0); // 0-100
            $table->unsignedBigInteger('updated_by')->nullable(); // user yg update
            $table->timestamps();

            // Unique per member per mesin per factory
            $table->unique(['sc_id', 'member_id', 'machine_name', 'factory'], 'member_skills_unique');
            $table->index(['sc_id', 'factory']);
            $table->index(['sc_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_skills');
    }
};
