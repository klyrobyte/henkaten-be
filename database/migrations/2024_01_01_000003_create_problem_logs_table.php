<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_logs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('factory');
            $table->enum('shift', ['A', 'B']);
            $table->enum('jenis', ['Man', 'Material', 'Machine', 'Method']);
            $table->string('lokasi');                          // nama mesin
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->string('durasi')->nullable();              // ex: "2h 30m"
            $table->text('deskripsi');
            $table->string('cause')->nullable();               // root cause
            $table->string('countermeasure')->nullable();
            $table->string('pic')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_logs');
    }
};
