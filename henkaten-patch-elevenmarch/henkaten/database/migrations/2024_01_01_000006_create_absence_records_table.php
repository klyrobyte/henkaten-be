<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * absence_records  - detail absen per member per hari
     *
     * Menggantikan: localStorage key henkaten_absen_v2_{date}_{factory}_{shift}
     * yang berisi object { memberId: { status, reason, name, timestamp } }
     */
    public function up(): void
    {
        Schema::create('absence_records', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('factory');
            $table->enum('shift', ['A', 'B']);
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'absen'])->default('hadir');
            $table->enum('reason', ['Cuti', 'Sakit', 'Ijin', 'Alpha', 'Tugas'])->nullable();
            $table->timestamps();

            // Satu record per member per tanggal per shift
            $table->unique(['tanggal', 'factory', 'shift', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absence_records');
    }
};
