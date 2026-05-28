<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PayoutMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name'           => 'PayPal',
                'currency'       => 'USD',
                'account_label'  => 'PayPal Email',
                'min_withdrawal' => 5000,
                'sort_order'     => 1,
            ],
            [
                'name'           => 'USDT',
                'currency'       => 'USDT',
                'account_label'  => 'USDT Wallet Address',
                'min_withdrawal' => 10000,
                'instructions'   => 'Include network, e.g. TRC20/BEP20.',
                'sort_order'     => 2,
            ],
            [
                'name'           => 'DANA',
                'currency'       => 'IDR',
                'account_label'  => 'DANA Number',
                'min_withdrawal' => 5000,
                'sort_order'     => 3,
            ],
            [
                'name'           => 'OVO',
                'currency'       => 'IDR',
                'account_label'  => 'OVO Number',
                'min_withdrawal' => 5000,
                'sort_order'     => 4,
            ],
            [
                'name'           => 'GoPay',
                'currency'       => 'IDR',
                'account_label'  => 'GoPay Number',
                'min_withdrawal' => 5000,
                'sort_order'     => 5,
            ],
            [
                'name'           => 'Bank Transfer',
                'currency'       => 'IDR',
                'account_label'  => 'Bank Account Number',
                'min_withdrawal' => 10000,
                'instructions'   => 'Include bank name and account holder.',
                'sort_order'     => 6,
            ],
            [
                'name'           => 'Gift Card',
                'currency'       => 'USD',
                'account_label'  => 'Email Address',
                'min_withdrawal' => 5000,
                'sort_order'     => 7,
            ],
        ];

        foreach ($methods as $m) {
            Payment::updateOrCreate(
                ['name' => $m['name']],
                array_merge($m, [
                    'photo_path'    => null,
                    'is_active'     => true,
                    'fee_percentage'=> 0,
                    'fixed_fee'     => 0,
                ])
            );
        }
    }
}
