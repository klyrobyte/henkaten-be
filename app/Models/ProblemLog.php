<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemLog extends Model
{
    protected $fillable = [
        'sc_id',
        'tanggal',
        'factory',
        'shift',
        'jenis',
        'lokasi',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'durasi',
        'deskripsi',
        'cause',
        'countermeasure',
        'pic',
        'created_by',
        'departemen_perbaikan',
    ];

    // Jenis hanya 3M  - Man dihandle oleh AbsenceRecord
    const JENIS_LIST = ['Machine', 'Material', 'Method'];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' || !$this->waktu_selesai;
    }

    public function getJenisColorAttribute(): string
    {
        return match ($this->jenis) {
            'Material' => '#f39c12',
            'Machine' => '#1F3C88',
            'Method' => '#729E3F',
            default => '#888',
        };
    }

    /**
     * Field tambahan yang relevan per jenis:
     * Fixed by Rizky
     * Machine  → no_part, downtime, technician (PIC mesin)
     * Material → no_lot, supplier, qty_defect
     * Method   → standar, deviasi, tindakan
     */
    public static function fieldsByJenis(string $jenis): array
    {
        return match ($jenis) {
            'Machine' => ['lokasi', 'waktu_mulai', 'waktu_selesai', 'deskripsi', 'cause', 'countermeasure', 'pic'],
            'Material' => ['lokasi', 'waktu_mulai', 'waktu_selesai', 'deskripsi', 'cause', 'countermeasure', 'pic'],
            'Method' => ['lokasi', 'waktu_mulai', 'waktu_selesai', 'deskripsi', 'cause', 'countermeasure', 'pic'],
            default => [],
        };
    }
}