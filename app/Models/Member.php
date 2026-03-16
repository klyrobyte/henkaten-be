<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'nama', 'nik', 'jabatan', 'shift', 'factory', 'mesin', 'photo', 'status',
    ];

    // ── Relasi ───────────────────────────────────────────────────────
    public function absenceRecords(): HasMany
    {
        return $this->hasMany(AbsenceRecord::class);
    }

    // ── Helper: CSS badge class untuk jabatan (mengganti getRoleBadgeClass() JS) ──
    public function getRoleBadgeClassAttribute(): string
    {
        return match (strtolower($this->jabatan ?? '')) {
            'spv'   => 'spv',
            'tl'    => 'tl',
            'gl'    => 'gl',
            'ky'    => 'ky',
            default => 'op',
        };
    }

    // ── Helper: URL foto (storage atau placeholder) ──────────────────
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) return null;
        if (str_starts_with($this->photo, 'data:')) return $this->photo; // base64 legacy
        return '/storage/' . $this->photo;
    }
}
