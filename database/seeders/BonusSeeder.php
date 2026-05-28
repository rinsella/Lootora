<?php

namespace Database\Seeders;

use App\Models\Bonus;
use Illuminate\Database\Seeder;

class BonusSeeder extends Seeder
{
    public function run(): void
    {
        Bonus::updateOrCreate(
            ['code' => 'WELCOME200'],
            ['points' => 200, 'max_uses' => 5]
        );
    }
}
