<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            // Hapus kolom lama yang tidak relevan
            $table->dropColumn([
                'p_cuti', 'p_sakit', 'p_ijin',   // SPV (lama)
                'o_cuti', 'o_sakit', 'o_ijin',   // Operator (lama)
            ]);

            // Tambah kolom baru yang lebih jelas
            // Operator (jabatan berisi 'Operator' / 'OP')
            $table->unsignedSmallInteger('op_hadir')->default(0)->after('mp_absen');
            $table->unsignedSmallInteger('op_cuti')->default(0)->after('op_hadir');
            $table->unsignedSmallInteger('op_sakit')->default(0)->after('op_cuti');
            $table->unsignedSmallInteger('op_ijin')->default(0)->after('op_sakit');
            $table->unsignedSmallInteger('op_mangkir')->default(0)->after('op_ijin');

            // Supervisor / Pengawas (jabatan berisi 'SPV', 'Supervisor', 'Pengawas', 'GL', 'TL', 'KY')
            $table->unsignedSmallInteger('spv_hadir')->default(0)->after('op_mangkir');
            $table->unsignedSmallInteger('spv_cuti')->default(0)->after('spv_hadir');
            $table->unsignedSmallInteger('spv_sakit')->default(0)->after('spv_cuti');
            $table->unsignedSmallInteger('spv_ijin')->default(0)->after('spv_sakit');
            $table->unsignedSmallInteger('spv_mangkir')->default(0)->after('spv_ijin');
        });
    }

    public function down(): void
    {
        Schema::table('absence_summaries', function (Blueprint $table) {
            $table->dropColumn([
                'op_hadir', 'op_cuti', 'op_sakit', 'op_ijin', 'op_mangkir',
                'spv_hadir', 'spv_cuti', 'spv_sakit', 'spv_ijin', 'spv_mangkir',
            ]);

            // Restore kolom lama
            $table->unsignedSmallInteger('p_cuti')->default(0);
            $table->unsignedSmallInteger('p_sakit')->default(0);
            $table->unsignedSmallInteger('p_ijin')->default(0);
            $table->unsignedSmallInteger('o_cuti')->default(0);
            $table->unsignedSmallInteger('o_sakit')->default(0);
            $table->unsignedSmallInteger('o_ijin')->default(0);
        });
    }
};
