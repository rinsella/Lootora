<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@lootora.net'],
            [
                'username'      => 'admin',
                'password'      => Hash::make('password'),
                'is_admin'      => true,
                'registered_ip' => '127.0.0.1',
                'last_login_ip' => '127.0.0.1',
                'last_seen_at'  => now(),
                'user_agent'    => 'Lootora Seeder',
            ]
        );
    }
}
