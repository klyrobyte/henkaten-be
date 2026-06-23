<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Section;
use App\Models\Status;
use Illuminate\Database\Seeder;

class DynamicSeeder extends Seeder
{
    public function run(): void
    {
        //   Statuses                             ─
        $statuses = [
            ['key' => 'mesin', 'label' => 'Mesin', 'icon' => '⚙️', 'color' => '#1f3c88', 'order_index' => 1],
            ['key' => 'persons', 'label' => 'Persons', 'icon' => '👥', 'color' => '#2e7d32', 'order_index' => 2],
            ['key' => 'robot', 'label' => 'Robot', 'icon' => '🤖', 'color' => '#6a1b9a', 'order_index' => 3],
            ['key' => 'line', 'label' => 'Line', 'icon' => '🔗', 'color' => '#e67e22', 'order_index' => 4],
            ['key' => 'pos', 'label' => 'Pos', 'icon' => '📍', 'color' => '#6a1b9a', 'order_index' => 5],
            ['key' => 'lainya', 'label' => 'Lainya', 'icon' => '📦', 'color' => '#546e7a', 'order_index' => 6],
            ['key' => 'mc_vibration', 'label' => 'MC Vibration', 'icon' => '🔴', 'color' => '#c0392b', 'order_index' => 7],
        ];

        foreach ($statuses as $s) {
            Status::firstOrCreate(['key' => $s['key']], $s);
        }

        //   Factories + Sections                        
        $data = [
            [
                'name' => 'Factory 2',
                'slug' => 'factory-2',
                'short_label' => 'F2',
                'gradient' => 'linear-gradient(135deg,#2e7d32,#43a047)',
                'order_index' => 1,
                'sections' => [
                    ['name' => 'Key Persons', 'code' => 'f2-persons', 'type' => 'persons', 'is_key_persons' => true, 'is_key_robot' => false, 'order_index' => 1],
                    ['name' => 'Resin Injection', 'code' => 'f2-resin', 'type' => 'mesin', 'is_key_persons' => false, 'is_key_robot' => false, 'order_index' => 2],
                    ['name' => 'Robot Assy Junbiki Trim Group', 'code' => 'f2-line', 'type' => 'line', 'is_key_persons' => false, 'is_key_robot' => false, 'order_index' => 3],
                    ['name' => 'Robot Assy D26A Doortrim', 'code' => 'f2-pos', 'type' => 'pos', 'is_key_persons' => false, 'is_key_robot' => false, 'order_index' => 4],
                ],
            ],
            [
                'name' => 'Factory 3 & 4',
                'slug' => 'factory-3-4',
                'short_label' => 'F3&4',
                'gradient' => 'linear-gradient(135deg,#1565c0,#1e88e5)',
                'order_index' => 2,
                'sections' => [
                    ['name' => 'Key Persons', 'code' => 'f3-persons', 'type' => 'persons', 'is_key_persons' => true, 'is_key_robot' => false, 'order_index' => 1],
                    ['name' => 'Factory 3 - Resin Injection', 'code' => 'f3-resin', 'type' => 'mesin', 'is_key_persons' => false, 'is_key_robot' => false, 'order_index' => 2],
                    ['name' => 'Factory 3 - Robot Assy', 'code' => 'f3-robot', 'type' => 'robot', 'is_key_persons' => false, 'is_key_robot' => true, 'order_index' => 3],
                    ['name' => 'Factory 4 - Resin Injection', 'code' => 'f4-resin', 'type' => 'mesin', 'is_key_persons' => false, 'is_key_robot' => false, 'order_index' => 4],
                ],
            ],
            [
                'name' => 'Painting',
                'slug' => 'painting',
                'short_label' => 'PT',
                'gradient' => 'linear-gradient(135deg,#e65100,#f57c00)',
                'order_index' => 3,
                'sections' => [],
            ],
        ];

        foreach ($data as $fData) {
            $sections = $fData['sections'];
            unset($fData['sections']);

            $factory = Factory::firstOrCreate(['name' => $fData['name']], $fData);

            foreach ($sections as $sData) {
                $section = Section::firstOrCreate(
                    ['factory_id' => $factory->id, 'code' => $sData['code']],
                    array_merge($sData, ['factory_id' => $factory->id])
                );

                // Update type on existing rows that were created before the type column existed
                if (empty($section->type)) {
                    $section->update(['type' => $sData['type']]);
                }
            }
        }
    }
}
