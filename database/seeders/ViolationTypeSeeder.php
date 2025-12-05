<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $violationTypes = [
            [
                'name' => 'Illegal Dumping',
                'slug' => 'illegal-dumping',
                'description' => 'Unauthorized disposal of waste in natural areas or public spaces',
                'icon' => 'bi-trash',
                'color' => '#dc3545',
                'severity' => 'high',
            ],
            [
                'name' => 'Water Pollution',
                'slug' => 'water-pollution',
                'description' => 'Contamination of water bodies including rivers, lakes, and coastal areas',
                'icon' => 'bi-droplet',
                'color' => '#0dcaf0',
                'severity' => 'critical',
            ],
            [
                'name' => 'Air Pollution',
                'slug' => 'air-pollution',
                'description' => 'Emission of harmful substances into the atmosphere',
                'icon' => 'bi-cloud',
                'color' => '#6c757d',
                'severity' => 'high',
            ],
            [
                'name' => 'Deforestation',
                'slug' => 'deforestation',
                'description' => 'Illegal logging and clearing of forest areas',
                'icon' => 'bi-tree',
                'color' => '#198754',
                'severity' => 'critical',
            ],
            [
                'name' => 'Noise Pollution',
                'slug' => 'noise-pollution',
                'description' => 'Excessive or disturbing noise that disrupts the environment',
                'icon' => 'bi-volume-up',
                'color' => '#ffc107',
                'severity' => 'medium',
            ],
            [
                'name' => 'Soil Contamination',
                'slug' => 'soil-contamination',
                'description' => 'Pollution of soil through chemicals, waste, or hazardous materials',
                'icon' => 'bi-layers',
                'color' => '#795548',
                'severity' => 'high',
            ],
            [
                'name' => 'Wildlife Violations',
                'slug' => 'wildlife-violations',
                'description' => 'Illegal hunting, trading, or harm to protected wildlife species',
                'icon' => 'bi-bug',
                'color' => '#20c997',
                'severity' => 'high',
            ],
            [
                'name' => 'Industrial Violations',
                'slug' => 'industrial-violations',
                'description' => 'Environmental violations by industrial facilities and operations',
                'icon' => 'bi-building',
                'color' => '#fd7e14',
                'severity' => 'critical',
            ],
        ];

        foreach ($violationTypes as $type) {
            ViolationType::create($type);
        }

        $this->command->info('Created 8 violation types');
    }
}
