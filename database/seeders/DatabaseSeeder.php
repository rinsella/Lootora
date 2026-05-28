<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PayoutMethodSeeder::class,
            SiteSettingsSeeder::class,
            BonusSeeder::class,
        ]);

        // Optional dev fixtures — only seed if we have very few users
        if (User::count() <= 1 && app()->environment(['local', 'development'])) {
            User::factory(20)->create();
        }
    }
}
