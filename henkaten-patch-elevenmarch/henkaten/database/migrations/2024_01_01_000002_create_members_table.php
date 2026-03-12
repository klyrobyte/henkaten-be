<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->nullable();                // NIK / ID karyawan
            $table->string('jabatan')->default('Operator');   // Operator/SPV/TL/GL/KY
            $table->enum('shift', ['A', 'B'])->default('A');
            $table->string('factory');                        // 'Factory 2' | 'Factory 3 & 4'
            $table->string('mesin')->nullable();
            $table->string('photo')->nullable();              // path relatif storage/
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
