<?php

namespace Database\Seeders;

use App\Models\Lgu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecowatch.ph',
            'password' => Hash::make('password'),
            'phone' => '09171234567',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create LGU staff for each LGU
        $lgus = Lgu::all();
        foreach ($lgus as $lgu) {
            User::create([
                'name' => $lgu->name . ' Staff',
                'email' => strtolower(str_replace(' ', '', $lgu->code)) . '@ecowatch.ph',
                'password' => Hash::make('password'),
                'phone' => '0917' . rand(1000000, 9999999),
                'role' => 'lgu',
                'lgu_id' => $lgu->id,
                'is_active' => true,
            ]);
        }

        // Create regular users
        $userNames = [
            'Juan Dela Cruz',
            'Maria Santos',
            'Pedro Reyes',
            'Ana Garcia',
            'Jose Fernandez',
            'Sofia Villanueva',
            'Miguel Torres',
            'Carmen Lopez',
            'Ricardo Morales',
            'Isabel Pascual',
        ];

        foreach ($userNames as $index => $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'password' => Hash::make('password'),
                'phone' => '0917' . rand(1000000, 9999999),
                'role' => 'user',
                'is_active' => true,
            ]);
        }

        $this->command->info('Created 1 admin, ' . $lgus->count() . ' LGU staff, and 10 regular users');
    }
}
