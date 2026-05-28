<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    /** Definition of all editable settings. */
    private array $schema = [
        ['key' => 'loot_site_name',             'label' => 'Site Name',                  'type' => 'string', 'group' => 'brand',     'default' => 'Lootora'],
        ['key' => 'loot_site_domain',           'label' => 'Site Domain',                'type' => 'string', 'group' => 'brand',     'default' => 'lootora.net'],
        ['key' => 'loot_contact_email',         'label' => 'Contact / Support Email',    'type' => 'string', 'group' => 'brand',     'default' => 'support@lootora.net'],
        ['key' => 'loot_point_name',            'label' => 'Point Name',                 'type' => 'string', 'group' => 'currency',  'default' => 'LOOT Points'],
        ['key' => 'loot_point_symbol',          'label' => 'Point Symbol',               'type' => 'string', 'group' => 'currency',  'default' => '$LOOT'],
        ['key' => 'loot_usd_to_points',         'label' => 'USD → Points Rate',          'type' => 'int',    'group' => 'currency',  'default' => 1000],
        ['key' => 'loot_default_revenue_share', 'label' => 'Default Revenue Share (%)',  'type' => 'float',  'group' => 'rewards',   'default' => 70.0],
        ['key' => 'loot_min_withdrawal_points', 'label' => 'Global Min Withdrawal (pts)','type' => 'float',  'group' => 'rewards',   'default' => 5000],
        ['key' => 'loot_daily_checkin_points',  'label' => 'Daily Check-in Reward',      'type' => 'float',  'group' => 'rewards',   'default' => 10],
        ['key' => 'loot_referral_percent',      'label' => 'Referral Reward (%)',        'type' => 'float',  'group' => 'rewards',   'default' => 10],
        ['key' => 'loot_maintenance_mode',      'label' => 'Maintenance Mode',           'type' => 'bool',   'group' => 'system',    'default' => false],
    ];

    public function index()
    {
        $values = [];
        foreach ($this->schema as $row) {
            $values[$row['key']] = SiteSetting::get($row['key'], $row['default']);
        }
        return view('admin.settings.index', ['schema' => $this->schema, 'values' => $values]);
    }

    public function update(Request $request)
    {
        foreach ($this->schema as $row) {
            $key = $row['key'];
            $val = $request->input($key);
            if ($row['type'] === 'bool') {
                $val = $request->boolean($key);
            }
            SiteSetting::put($key, $val, $row['type'], $row['group']);
        }
        return back()->with('success', 'Settings saved.');
    }
}
