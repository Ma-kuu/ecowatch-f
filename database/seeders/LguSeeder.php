<?php

namespace Database\Seeders;

use App\Models\Lgu;
use Illuminate\Database\Seeder;

class LguSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lgus = [
            [
                'name' => 'Davao City',
                'code' => 'DVO',
                'province' => 'Davao del Sur',
                'region' => 'Region XI',
                'contact_email' => 'environment@davaocity.gov.ph',
                'contact_phone' => '(082) 227-3000',
                'address' => 'City Hall Drive, Davao City',
                'latitude' => 7.1907,
                'longitude' => 125.4553,
                'coverage_radius_km' => 15.00,
            ],
            [
                'name' => 'Tagum City',
                'code' => 'TAG',
                'province' => 'Davao del Norte',
                'region' => 'Region XI',
                'contact_email' => 'environment@tagumcity.gov.ph',
                'contact_phone' => '(084) 655-8405',
                'address' => 'Pioneer Avenue, Tagum City',
                'latitude' => 7.4479,
                'longitude' => 125.8078,
                'coverage_radius_km' => 12.00,
            ],
            [
                'name' => 'Digos City',
                'code' => 'DGS',
                'province' => 'Davao del Sur',
                'region' => 'Region XI',
                'contact_email' => 'environment@digoscity.gov.ph',
                'contact_phone' => '(082) 553-2680',
                'address' => 'Rizal Avenue, Digos City',
                'latitude' => 6.7498,
                'longitude' => 125.3572,
                'coverage_radius_km' => 10.00,
            ],
            [
                'name' => 'Panabo City',
                'code' => 'PNB',
                'province' => 'Davao del Norte',
                'region' => 'Region XI',
                'contact_email' => 'environment@panabocity.gov.ph',
                'contact_phone' => '(084) 628-6800',
                'address' => 'JP Laurel Avenue, Panabo City',
                'latitude' => 7.3077,
                'longitude' => 125.6836,
                'coverage_radius_km' => 10.00,
            ],
            [
                'name' => 'Mati City',
                'code' => 'MAT',
                'province' => 'Davao Oriental',
                'region' => 'Region XI',
                'contact_email' => 'environment@maticity.gov.ph',
                'contact_phone' => '(087) 388-2224',
                'address' => 'Rizal Street, Mati City',
                'latitude' => 6.9549,
                'longitude' => 126.2185,
                'coverage_radius_km' => 12.00,
            ],
        ];

        foreach ($lgus as $lgu) {
            Lgu::create($lgu);
        }

        $this->command->info('Created 5 LGUs in Davao Region');
    }
}
