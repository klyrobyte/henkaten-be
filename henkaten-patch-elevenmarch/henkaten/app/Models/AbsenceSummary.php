<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceSummary extends Model
{
    protected $fillable = [
        'tanggal',
        'factory',
        'shift',

        // Totals
        'mp_hadir',
        'total_absen',
        'total_member',

        // Operator buckets
        'op_cuti',
        'op_sakit',
        'op_ijin',

        // Pengawas (SPV) buckets
        'spv_cuti',
        'spv_sakit',
        'spv_ijin',

        'source',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'mp_hadir'     => 'integer',
        'total_absen'  => 'integer',
        'total_member' => 'integer',
        'op_cuti'      => 'integer',
        'op_sakit'     => 'integer',
        'op_ijin'      => 'integer',
        'spv_cuti'     => 'integer',
        'spv_sakit'    => 'integer',
        'spv_ijin'     => 'integer',
    ];
}