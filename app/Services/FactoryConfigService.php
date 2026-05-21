<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\Machine;
use App\Models\Section;

/**
 * FactoryConfigService
 *
 * UPDATED: All factory and section config now reads from DB (factories + sections tables).
 * No hardcoded section config  - fully dynamic.
 * All method signatures preserved for backward compatibility.
 */
class FactoryConfigService
{
    // ─── Public API ───────────────────────────────────────────────────

    /**
     * Ambil konfigurasi section (array of definitions) untuk satu factory.
     * Format output identik dengan versi lama untuk backward compatibility.
     */
    public function getSectionConfig(string $factory): array
    {
        $factoryModel = Factory::where('name', $factory)->first();
        if (!$factoryModel)
            return [];

        return $factoryModel->sections()
            ->orderBy('order_index')
            ->get()
            ->map(fn($s) => [
                'key' => $s->code,
                'title' => $s->name,
                'type' => $s->type,
                'is_key_persons' => $s->is_key_persons,
                'is_key_robot' => $s->is_key_robot,
            ])
            ->toArray();
    }

    /**
     * Bangun $groups dinamis dari DB  - menggantikan getGroups() lama.
     * Dipakai di DashboardController::index() dan tvMode().
     *
     * Setiap elemen berisi:
     *   title          → judul section
     *   section_key    → key internal (code dari tabel sections)
     *   machines       → array nama mesin (string[])
     *   is_key_persons → boolean
     *   is_key_robot   → boolean
     */
    public function buildGroups(string $factory): array
    {
        $dbMachines = Machine::where('factory', $factory)->orderBy('id')->get();
        $sectionDefs = $this->getSectionConfig($factory);

        // Build lookup of configured codes
        $configuredKeys = array_column($sectionDefs, 'key');

        // Collect all unique section_key values from DB machines
        $allSectionKeys = $dbMachines->pluck('section_key')->unique()->sort()->values()->toArray();

        $groups = [];

        // 1. Add groups from section config IN ORDER (preserve order from DB sections table)
        foreach ($sectionDefs as $def) {
            $key = $def['key'];

            $sectionMachines = $dbMachines->filter(
                fn($m) => $m->section_key === $key
            )->values();

            $groups[] = [
                'title' => $def['title'],
                'section_key' => $key,
                'type' => $def['type'] ?? 'mesin',
                'machines' => $sectionMachines->pluck('name')->toArray(),
                'is_key_persons' => $def['is_key_persons'] ?? false,
                'is_key_robot' => $def['is_key_robot'] ?? false,
            ];
        }

        return $groups;
    }

    /**
     * Generate title automatically for unconfigured sections.
     */
    private function generateSectionTitle(string $key): string
    {
        // Try to look up name from sections table first
        $section = Section::where('code', $key)->first();
        if ($section)
            return $section->name;

        $titles = [
            'lainya' => 'Others / Support Equipment',
            'custom' => 'Custom Section',
            'other' => 'Other Section',
        ];

        return $titles[$key] ?? ucfirst($key);
    }

    /**
     * Semua nama mesin dari DB untuk satu factory.
     * @param bool $excludeKeyPersons  jika true, skip mesin dengan status='persons'
     */
    public function getAllMachines(string $factory, bool $excludeKeyPersons = false): array
    {
        $query = Machine::where('factory', $factory);
        if ($excludeKeyPersons) {
            $query->where('status', '!=', 'persons');
        }
        return $query->pluck('name')->toArray();
    }

    /** Cek apakah judul grup adalah Key Persons */
    public function isKeyPersons(string $groupTitle): bool
    {
        return str_contains($groupTitle, 'Key Persons');
    }

    /** Cek key robot */
    public function isKeyRobot(string $groupTitle): bool
    {
        return str_contains($groupTitle, 'Robot Assy');
    }

    /**
     * Semua factory yang tersedia  - now from DB.
     */
    public function getFactories(): array
    {
        return Factory::orderBy('order_index')->pluck('name')->toArray();
    }

    /**
     * Semua factory sebagai full objects  - for views that need gradient, short_label, etc.
     */
    public function getFactoryObjects(): \Illuminate\Support\Collection
    {
        return Factory::orderBy('order_index')->get();
    }
}