<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AbsenceRecord  - detail absen per member per hari
 *
 * Menggantikan: localStorage key  henkaten_absen_v2_{date}_{factory}_{shift}
 *               yang berisi object { memberId: { status, reason, name, timestamp } }
 */
class AbsenceRecord extends Model
{
    protected $fillable = [
        'tanggal',
        'factory',
        'shift',
        'member_id',
        'status',
        'reason',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
