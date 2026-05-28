<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PayoutMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'PayPal',        'currency' => 'USD',  'account_label' => 'PayPal Email',     'min_withdrawal' => 5000,  'fee_percentage' => 0,    'fixed_fee' => 0, 'sort_order' => 1],
            ['name' => 'USDT (TRC20)',  'currency' => 'USDT', 'account_label' => 'TRC20 Wallet Address', 'min_withdrawal' => 10000, 'fee_percentage' => 0,    'fixed_fee' => 1, 'sort_order' => 2],
            ['name' => 'DANA',          'currency' => 'IDR',  'account_label' => 'DANA Phone Number', 'min_withdrawal' => 5000,  'fee_percentage' => 0,    'fixed_fee' => 0, 'sort_order' => 3],
            ['name' => 'OVO',           'currency' => 'IDR',  'account_label' => 'OVO Phone Number',  'min_withdrawal' => 5000,  'fee_percentage' => 0,    'fixed_fee' => 0, 'sort_order' => 4],
            ['name' => 'GoPay',         'currency' => 'IDR',  'account_label' => 'GoPay Phone Number','min_withdrawal' => 5000,  'fee_percentage' => 0,    'fixed_fee' => 0, 'sort_order' => 5],
            ['name' => 'Bank Transfer', 'currency' => 'IDR',  'account_label' => 'Bank Name + Account Number', 'min_withdrawal' => 20000, 'fee_percentage' => 0.5,  'fixed_fee' => 0, 'sort_order' => 6],
            ['name' => 'Gift Card',     'currency' => 'USD',  'account_label' => 'Email for delivery','min_withdrawal' => 10000, 'fee_percentage' => 0,    'fixed_fee' => 0, 'sort_order' => 7],
        ];

        foreach ($methods as $m) {
            Payment::firstOrCreate(
                ['name' => $m['name']],
                array_merge($m, ['is_active' => true])
            );
        }
    }
}
