<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absence_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('factory');
            $table->enum('shift', ['A', 'B']);
            // MP
            $table->unsignedSmallInteger('mp_hadir')->default(0);
            $table->unsignedSmallInteger('mp_absen')->default(0);
            // SPV
            $table->unsignedSmallInteger('p_cuti')->default(0);
            $table->unsignedSmallInteger('p_sakit')->default(0);
            $table->unsignedSmallInteger('p_ijin')->default(0);
            // Operator
            $table->unsignedSmallInteger('o_cuti')->default(0);
            $table->unsignedSmallInteger('o_sakit')->default(0);
            $table->unsignedSmallInteger('o_ijin')->default(0);
            // Computed (bisa di-generate, disimpan untuk performa)
            $table->unsignedSmallInteger('total_absen')->default(0);
            $table->unsignedSmallInteger('total_member')->default(0);
            $table->string('source')->default('admin_board'); // 'admin_board' | 'dailyassignment'
            $table->timestamps();

            // Satu record per tanggal+factory+shift
            $table->unique(['tanggal', 'factory', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_summaries');
    }
};
