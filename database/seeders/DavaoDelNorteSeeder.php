<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lgu;
use App\Models\Barangay;

class DavaoDelNorteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = 'C:\Users\Mark Amper\Downloads\ddn.json';

        if (!file_exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data) {
            $this->command->error("Failed to parse JSON file");
            return;
        }

        $this->command->info("Starting Davao del Norte data seeding...");

        // Delete all existing barangays first
        $this->command->info("Deleting existing barangays...");
        DB::table('barangays')->delete();

        // Update existing LGUs or create new ones
        $this->command->info("Processing LGUs and barangays...");

        foreach ($data['administrative_divisions'] as $division) {
            // Name mapping for existing LGUs
            $nameMap = [
                'City of Tagum' => 'Tagum City',
                'City of Panabo' => 'Panabo City',
                'Island Garden City of Samal (IGACOS)' => 'Island Garden City of Samal',
            ];

            $lguName = $nameMap[$division['name']] ?? $division['name'];

            // Find or create LGU
            $lgu = Lgu::where('name', $lguName)->first();

            if (!$lgu) {
                $this->command->warn("LGU not found, creating: {$lguName}");
                $lgu = Lgu::create([
                    'name' => $lguName,
                    'code' => $division['lgu_psgc'],
                    'province' => $data['province'],
                    'region' => 'Region XI',
                    'is_active' => true,
                    'coverage_radius_km' => 10.00,
                ]);
            } else {
                // Update PSGC code if needed
                $lgu->update(['code' => $division['lgu_psgc']]);
            }

            $this->command->info("Processing {$division['barangay_count']} barangays for {$lgu->name}");

            // Insert barangays for this LGU
            foreach ($division['barangays'] as $barangayData) {
                Barangay::create([
                    'lgu_id' => $lgu->id,
                    'name' => $barangayData['name'],
                    'code' => $barangayData['psgc_code'],
                    'is_active' => true,
                ]);
            }
        }

        // Verify counts
        $lguCount = Lgu::where('province', 'Davao del Norte')->count();
        $barangayCount = Barangay::count();

        $this->command->info("✅ Seeding complete!");
        $this->command->info("   LGUs: {$lguCount}");
        $this->command->info("   Barangays: {$barangayCount}");

        if ($barangayCount !== 223) {
            $this->command->warn("⚠️  Expected 223 barangays, but found {$barangayCount}");
        } else {
            $this->command->info("✅ Barangay count is correct (223)");
        }
    }
}
