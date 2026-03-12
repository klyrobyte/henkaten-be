<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_assignments', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('factory');
            $table->enum('shift', ['A', 'B']);
            $table->string('group_title');
            $table->string('machine_name');
            $table->unsignedInteger('slot_index')->default(0);
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('member_name')->nullable();
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->string('absent_reason')->nullable();
            $table->boolean('is_substitute')->default(false);
            $table->unsignedInteger('substitute_for')->nullable();
            $table->boolean('synced_from_mm')->default(false);
            $table->timestamps();

            // Index untuk query yang sering dipakai
            $table->index(['tanggal', 'factory', 'shift']);
            $table->index(['tanggal', 'factory', 'shift', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_assignments');
    }
};