<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberSkill extends Model
{
    protected $fillable = [
        'sc_id',
        'member_id',
        'machine_name',
        'process_name',
        'factory',
        'skill_pct',
        'updated_by',
    ];

    protected $casts = [
        'skill_pct' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Cek apakah skill memenuhi syarat penggantian (>= 75%)
     */
    public function isEligibleForReplacement(): bool
    {
        return $this->skill_pct >= 75;
    }
}
