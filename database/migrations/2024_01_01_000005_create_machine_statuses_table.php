<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_statuses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('factory');
            $table->enum('shift', ['A', 'B']);
            $table->string('machine_name');                    // nama mesin persis dari factoryConfig
            $table->enum('status', ['normal', 'man', 'material', 'machine', 'method'])->default('normal');
            $table->timestamps();

            $table->unique(['tanggal', 'factory', 'shift', 'machine_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_statuses');
    }
};
