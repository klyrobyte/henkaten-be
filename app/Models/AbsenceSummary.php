<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceSummary extends Model
{
    protected $fillable = [
        'tanggal','factory','shift',
        'mp_hadir','mp_absen',
        'p_cuti','p_sakit','p_ijin',
        'o_cuti','o_sakit','o_ijin',
        'total_absen','total_member','source',
    ];

    protected $casts = ['tanggal' => 'date'];
}
