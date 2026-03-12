<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentReplacement extends Model
{
    protected $fillable = [
        'tanggal',
        'factory',
        'shift',
        'target_machine',
        'member_id',
        'source_machine',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}