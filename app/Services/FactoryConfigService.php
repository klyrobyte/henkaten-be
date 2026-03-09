<?php

namespace App\Services;

/**
 * FactoryConfigService
 *
 * Menggantikan konstanta JS:
 *   - factoryConfig       (daftar grup & mesin per factory)
 *   - circleCountConfig   (jumlah slot/circle per mesin)
 *
 * Di-bind sebagai singleton di AppServiceProvider.
 */
class FactoryConfigService
{
    // ─── Grup & mesin per factory ──────────────────────────────────────
    private array $groups = [
        'Factory 2' => [
            ['title' => 'Key Persons', 'machines' => [
                'GL','TL Resin','TL Assy (1)','TL Assy (2)',
                'KY Resin','KY Assy (1)','KY Assy (2)',
            ]],
            ['title' => 'Resin Injection', 'machines' => [
                '#01-2500T','#02-3500T','#03-3500T','#04-2500T','#05-3500T',
                '#06-2500T','#07-2500T','#08-2500T','Crane 30T','Trans. Part','Trans. Matl',
            ]],
            ['title' => 'Robot Assembly', 'machines' => [
                'Robot 1','Robot 2','Robot 3','Robot 4','Robot 5','Quality',
            ]],
            ['title' => 'SPS & Setting', 'machines' => [
                'SPS','Setting FG','Repair Part',
            ]],
            ['title' => 'Position & Final Check', 'machines' => [
                'Pos 1','Pos 2','Pos 3','Final Check','Setting Fax','Repair Part','Trans. Child Part',
            ]],
        ],
        'Factory 3 & 4' => [
            ['title' => 'Key Persons', 'machines' => [
                'GL','TL Resin 1','TL Resin 2','KY Resin',
            ]],
            ['title' => 'Factory 3 - Resin Injection', 'machines' => [
                '#01-1300T','#02-1300T','Q/Gate D.G.','#03-1300T','#04-1050T',
                '#05-2500T','#06-1600T','#07-2500T','Assy #07&#08','#08-2500T',
                '#09-1600T','#10-1600T','#10&13-650T','#14-650T','MC Vibration',
                'Crane 18T','Crane 7.5T','Trans. Matl','Trans. Part','Trans.Box',
            ]],
            ['title' => 'Factory 4 - Resin Injection', 'machines' => [
                '#01-350T','#08-350T','Crane&Matl','Assy 1','Assy 2','Assy 3',
            ]],
        ],
    ];

    // ─── Jumlah circle/slot per mesin ─────────────────────────────────
    private array $circleCount = [
        'Factory 2' => [
            'Key Persons' => [
                'GL'=>1,'TL Resin'=>1,'TL Assy (1)'=>1,'TL Assy (2)'=>1,
                'KY Resin'=>1,'KY Assy (1)'=>1,'KY Assy (2)'=>1,
            ],
            'Resin Injection' => [
                '#01-2500T'=>3,'#02-3500T'=>2,'#03-3500T'=>1,'#04-2500T'=>1,
                '#05-3500T'=>1,'#06-2500T'=>2,'#07-2500T'=>1,'#08-2500T'=>1,
                'Crane 30T'=>2,'Trans. Part'=>2,'Trans. Matl'=>2,
            ],
            'Robot Assembly'  => ['Robot 1'=>2,'Robot 2'=>2,'Robot 3'=>1,'Robot 4'=>2,'Robot 5'=>2,'Quality'=>1],
            'SPS & Setting'   => ['SPS'=>2,'Setting FG'=>2,'Repair Part'=>1],
            'Position & Final Check' => [
                'Pos 1'=>1,'Pos 2'=>1,'Pos 3'=>1,'Final Check'=>2,
                'Setting Fax'=>1,'Repair Part'=>1,'Trans. Child Part'=>1,
            ],
        ],
        'Factory 3 & 4' => [
            'Key Persons' => ['GL'=>1,'TL Resin 1'=>1,'TL Resin 2'=>1,'KY Resin'=>1],
            'Factory 3 - Resin Injection' => [
                '#01-1300T'=>3,'#02-1300T'=>2,'Q/Gate D.G.'=>1,'#03-1300T'=>1,'#04-1050T'=>1,
                '#05-2500T'=>3,'#06-1600T'=>2,'#07-2500T'=>1,'Assy #07&#08'=>1,'#08-2500T'=>1,
                '#09-1600T'=>1,'#10-1600T'=>2,'#10&13-650T'=>1,'#14-650T'=>1,'MC Vibration'=>1,
                'Crane 18T'=>1,'Crane 7.5T'=>1,'Trans. Matl'=>1,'Trans. Part'=>1,'Trans.Box'=>1,
            ],
            'Factory 4 - Resin Injection' => [
                '#01-350T'=>1,'#08-350T'=>2,'Crane&Matl'=>1,'Assy 1'=>1,'Assy 2'=>1,'Assy 3'=>1,
            ],
        ],
    ];

    // ─── Public API ───────────────────────────────────────────────────

    /** Ambil semua grup untuk satu factory */
    public function getGroups(string $factory): array
    {
        return $this->groups[$factory] ?? [];
    }

    /** Ambil semua nama mesin flat (opsional exclude Key Persons) */
    public function getAllMachines(string $factory, bool $excludeKeyPersons = false): array
    {
        $machines = [];
        foreach ($this->groups[$factory] ?? [] as $group) {
            if ($excludeKeyPersons && str_contains($group['title'], 'Key Persons')) continue;
            array_push($machines, ...$group['machines']);
        }
        return $machines;
    }

    /** Jumlah circle/slot untuk mesin tertentu */
    public function getCircleCount(string $factory, string $groupTitle, string $machineName): int
    {
        return $this->circleCount[$factory][$groupTitle][$machineName] ?? 1;
    }

    /** Cek apakah grup adalah Key Persons */
    public function isKeyPersons(string $groupTitle): bool
    {
        return str_contains($groupTitle, 'Key Persons');
    }

    /** Semua factory yang tersedia */
    public function getFactories(): array
    {
        return array_keys($this->groups);
    }
}
