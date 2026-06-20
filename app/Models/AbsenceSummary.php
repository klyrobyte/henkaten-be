<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceSummary extends Model
{
    protected $fillable = [
        'sc_id',
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
        'op_Alpha',

        // Pengawas (SPV) buckets
        'spv_cuti',
        'spv_sakit',
        'spv_ijin',
        'spv_Alpha',

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
        'op_Alpha'     => 'integer',
        'spv_hadir'    => 'integer',
        'spv_cuti'     => 'integer',
        'spv_sakit'    => 'integer',
        'spv_ijin'     => 'integer',
        'spv_Alpha'    => 'integer',
    ];
}