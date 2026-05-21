<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Machine extends Model
{
    protected $fillable = ['factory', 'name', 'photo', 'status', 'section', 'floor_cx', 'floor_cy', 'floor_plan'];

    /**
     * URL foto mesin  - null jika belum ada foto
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo)
            return null;
        // Ambil basename saja  - disk root sudah di public/storage/machines/
        $filename = basename($this->photo);
        return '/storage/machines/' . $filename;
    }

    /**
     * Kunci section untuk routing ke dashboard.
     * Fixed by Rizky
     * UPDATED: Sekarang section field dapat digunakan untuk menentukan dashboard section
     * untuk SEMUA factory dan status.
     * 
     * Priority:
     * 1. Gunakan custom section jika ada di kolom 'section'
     * 2. Untuk Factory 3 & 4: map dari f3-xxx, f4-xxx ke section key
     * 3. Fallback ke status
     */
    public function getSectionKeyAttribute(): string
    {
        if (empty($this->section)) {
            return $this->status;
        }

        $code = $this->section;

        // ── Priority 1: check if the value is a known code in the sections table.
        // This is the canonical path for all dynamically-created sections (e.g. f3-lainya).
        $exists = \App\Models\Section::where('code', $code)->exists();
        if ($exists) {
            return $code;
        }

        // ── Priority 2: fall back to the legacy display-name / old-format mapping
        // for machines that were assigned before the dynamic section system existed.
        return $this->mapSectionNameToKey($code);
    }

    /**
     * Legacy mapper  - only used for machines with old-style display names or
     * pre-DB codes stored in the section column.
     * Fixed by Rizky
     * NOTE: Do NOT add new codes here. New sections are handled via Priority 1
     *       (direct DB lookup) in getSectionKeyAttribute().
     */
    private function mapSectionNameToKey(string $sectionName): string
    {
        $mapping = [
            // ── Display names (legacy, pre-DB) ──
            'Key Persons' => 'persons',
            'Factory 3 - Robot Assy' => 'robot',
            'Resin Injection' => 'mesin',
            'Robot Assy Junbiki Trim Group' => 'line',
            'Robot Assy D26A Doortrim' => 'pos',
            'Others / Support Equipment' => 'lainya',

            // ── Internal status keys (direct passthrough) ──
            'persons' => 'persons',
            'mesin' => 'mesin',
            'robot' => 'robot',
            'line' => 'line',
            'pos' => 'pos',
            'lainya' => 'lainya',
            'mc_vibration' => 'mc_vibration',

            // ── Legacy factory codes (pre-DB, no longer actively used) ──
            'f3' => 'f3',
            'f4' => 'f4',
        ];

        return $mapping[$sectionName] ?? $sectionName;
    }

    /**
     * Get status color for floor plan visualization
     * Maps status to color class for SVG circles
     */
    public function getStatusColorAttribute(): string
    {
        // Check current machine status from MachineStatus table
        $machineStatus = $this->machineStatus?->status ?? 'unknown';

        return match ($machineStatus) {
            'ok', 'safe' => 'safe',           // green
            'ada_masalah' => 'problem',        // red
            'warning' => 'low-risk',          // yellow
            'caution' => 'mid-risk',          // orange
            default => 'unknown'              // gray
        };
    }

    /**
     * Get floor plan data for JSON API
     */
    public function getFloorPlanDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cx' => $this->floor_cx,
            'cy' => $this->floor_cy,
            'status' => $this->machineStatus?->status ?? 'unknown',
            'color' => $this->status_color,
        ];
    }

    /**
     * Relationship to MachineStatus
     */
    public function machineStatus()
    {
        return $this->hasOne(MachineStatus::class, 'machine_id')->latest();
    }
}