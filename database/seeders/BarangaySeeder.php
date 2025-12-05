<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\Lgu;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            // Davao City barangays
            'DVO' => [
                'Poblacion District',
                'Buhangin',
                'Talomo',
                'Agdao',
                'Toril',
                'Calinan',
                'Marilog',
                'Baguio District',
            ],
            // Tagum City barangays
            'TAG' => [
                'Poblacion',
                'Apokon',
                'Mankilam',
                'Nueva Fuerza',
                'Pagsabangan',
            ],
            // Digos City barangays
            'DGS' => [
                'Poblacion',
                'Zone 1',
                'Zone 2',
                'Aplaya',
                'Cogon',
            ],
            // Panabo City barangays
            'PNB' => [
                'Poblacion',
                'J.P. Laurel',
                'A.O. Floirendo',
                'Gredu',
                'New Pandan',
            ],
            // Mati City barangays
            'MAT' => [
                'Poblacion',
                'Sainz',
                'Dahican',
                'Central',
                'Badas',
            ],
        ];

        foreach ($barangays as $lguCode => $barangayNames) {
            $lgu = Lgu::where('code', $lguCode)->first();

            if ($lgu) {
                foreach ($barangayNames as $index => $name) {
                    Barangay::create([
                        'lgu_id' => $lgu->id,
                        'name' => $name,
                        'code' => $lguCode . '-BRG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Created barangays for all LGUs');
    }
}
