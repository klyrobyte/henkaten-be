<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DailyAssignment
 *
 * Menggantikan struktur localStorage:
 *   henkaten_assign_{date}_{factory}_{shift}
 *   → assignments['GroupTitle::MachineName'][slotIndex] = { memberName, status, ... }
 * Bug fixed by Rizky
 */
class DailyAssignment extends Model
{
    protected $fillable = [
        'sc_id',
        'tanggal',
        'factory',
        'shift',
        'group_title',
        'machine_name',
        'slot_index',
        'member_id',
        'member_name',
        'status',
        'absent_reason',
        'is_substitute',
        'substitute_for',
        'synced_from_mm',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_substitute' => 'boolean',
        'synced_from_mm' => 'boolean',
        'slot_index' => 'integer',
        'substitute_for' => 'integer',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // ── Helper: build key string seperti JS ─────────────────────────────────

    public function getMcKeyAttribute(): string
    {
        return "{$this->group_title}::{$this->machine_name}";
    }

    // ── Static: ubah koleksi DB → format array JS (assignments{}) ───────────

    /**
     * Ambil semua slot untuk konteks tertentu, kembalikan dalam format JS:
     * [
     *   'GroupTitle::MachineName' => [
     *     0 => ['memberName'=>'...', 'status'=>'present', 'isSubstitute'=>false, ...],
     *     1 => [...],
     *   ],
     *   ...
     * ]
     */
    public static function toAssignmentsArray(string $tanggal, string $factory, string $shift): array
    {
        $scId = auth()->check() ? (auth()->user()->sc_id ?? 1) : 1;
        $rows = static::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->with('member')->orderBy('slot_index')->get();

        $assignments = [];
        foreach ($rows as $row) {
            $key = "{$row->group_title}::{$row->machine_name}";
            $assignments[$key][] = [
                'memberName' => $row->member_name ?? ($row->member?->nama ?? ''),
                'foto' => $row->member?->photo_url,
                'status' => $row->status,
                'absentReason' => $row->absent_reason ?? '',
                'isSubstitute' => $row->is_substitute,
                'substituteFor' => $row->substitute_for,
                'syncedFromMM' => $row->synced_from_mm,
                'memberId' => $row->member_id,
                'dbId' => $row->id,
            ];
        }

        return $assignments;
    }
}
