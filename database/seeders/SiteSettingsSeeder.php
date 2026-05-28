<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('site_settings')) {
            return;
        }

        $defaults = [
            ['loot_site_name',             'Lootora',         'string', 'brand'],
            ['loot_site_domain',           'lootora.net',     'string', 'brand'],
            ['loot_contact_email',         'support@lootora.net', 'string', 'brand'],
            ['loot_point_name',            'LOOT Points',     'string', 'currency'],
            ['loot_point_symbol',          '$LOOT',           'string', 'currency'],
            ['loot_usd_to_points',         '1000',            'int',    'currency'],
            ['loot_default_revenue_share', '70',              'float',  'rewards'],
            ['loot_min_withdrawal_points', '5000',            'float',  'rewards'],
            ['loot_daily_checkin_points',  '10',              'float',  'rewards'],
            ['loot_referral_percent',      '10',              'float',  'rewards'],
            ['loot_maintenance_mode',      '0',               'bool',   'system'],
        ];

        foreach ($defaults as [$key, $value, $type, $group]) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type, 'group' => $group]
            );
        }
    }
}
