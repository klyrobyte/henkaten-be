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
        Schema::create('machine_processes', function (Blueprint $table) {
            $table->id();
            $table->integer('sc_id')->default(1);
            $table->string('factory', 50);
            $table->string('machine_name', 100);
            $table->string('process_name', 150);
            $table->timestamps();
            
            $table->index(['sc_id', 'factory', 'machine_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_processes');
    }
};
