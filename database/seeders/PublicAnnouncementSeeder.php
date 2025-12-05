<?php

namespace Database\Seeders;

use App\Models\Lgu;
use App\Models\PublicAnnouncement;
use App\Models\User;
use Illuminate\Database\Seeder;

class PublicAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $davaoCityLgu = Lgu::where('code', 'DVO')->first();
        $lguStaff = User::where('role', 'lgu')->where('lgu_id', $davaoCityLgu->id)->first();

        $announcements = [
            [
                'title' => 'Welcome to EcoWatch!',
                'content' => 'Thank you for joining our environmental reporting system. Together, we can make a difference in protecting our environment.',
                'type' => 'success',
                'lgu_id' => null,
                'created_by' => $admin->id,
                'is_active' => true,
                'is_pinned' => true,
                'published_at' => now(),
                'expires_at' => null,
            ],
            [
                'title' => 'Community Cleanup Drive - This Saturday',
                'content' => 'Join us this Saturday, 7:00 AM at Magsaysay Park for our monthly community cleanup drive. Bring your gloves and let\'s work together for a cleaner Davao!',
                'type' => 'info',
                'lgu_id' => $davaoCityLgu->id,
                'created_by' => $lguStaff->id,
                'is_active' => true,
                'is_pinned' => false,
                'published_at' => now(),
                'expires_at' => now()->addDays(7),
            ],
            [
                'title' => 'Reminder: Proper Waste Segregation',
                'content' => 'Please remember to properly segregate your waste: Biodegradable (green), Recyclable (blue), and Residual (red). Proper waste management starts at home!',
                'type' => 'warning',
                'lgu_id' => $davaoCityLgu->id,
                'created_by' => $lguStaff->id,
                'is_active' => true,
                'is_pinned' => false,
                'published_at' => now()->subDays(3),
                'expires_at' => null,
            ],
            [
                'title' => 'Report Verification Process',
                'content' => 'All submitted reports undergo verification within 24-48 hours. Valid reports are immediately assigned to the responsible LGU for action. Thank you for your patience.',
                'type' => 'info',
                'lgu_id' => null,
                'created_by' => $admin->id,
                'is_active' => true,
                'is_pinned' => false,
                'published_at' => now()->subWeek(),
                'expires_at' => null,
            ],
            [
                'title' => 'Fire Ban in Effect',
                'content' => 'Due to dry season, open burning is strictly prohibited. Violators will face penalties. Please report any violations immediately.',
                'type' => 'urgent',
                'lgu_id' => $davaoCityLgu->id,
                'created_by' => $lguStaff->id,
                'is_active' => true,
                'is_pinned' => true,
                'published_at' => now()->subDays(2),
                'expires_at' => now()->addMonths(2),
            ],
        ];

        foreach ($announcements as $announcement) {
            PublicAnnouncement::create($announcement);
        }

        $this->command->info('Created 5 sample announcements');
    }
}
