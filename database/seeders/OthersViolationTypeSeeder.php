<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ViolationType;

class OthersViolationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if "Others" already exists
        $exists = ViolationType::where('slug', 'others')->exists();
        
        if (!$exists) {
            ViolationType::create([
                'name' => 'Others',
                'slug' => 'others',
                'description' => 'Other environmental violations not categorized above',
                'icon' => 'bi-three-dots',
                'color' => '#6c757d', // Gray color
                'is_active' => true,
            ]);
            
            $this->command->info('✓ "Others" violation type created successfully!');
        } else {
            $this->command->info('ℹ "Others" violation type already exists.');
        }
    }
}
