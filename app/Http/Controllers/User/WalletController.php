<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Support\Lootora;

class WalletController extends Controller
{
    public function index()
    {
        $user        = auth()->user();
        $withdrawals = $user->withdrawals()->take(20)->get();

        $methods = [
            ['name' => 'PayPal',        'tag' => 'Instant',     'color' => 'bg-blue-50 text-blue-700'],
            ['name' => 'USDT',          'tag' => 'Crypto',      'color' => 'bg-emerald-50 text-emerald-700'],
            ['name' => 'DANA',          'tag' => 'E-wallet ID', 'color' => 'bg-sky-50 text-sky-700'],
            ['name' => 'OVO',           'tag' => 'E-wallet ID', 'color' => 'bg-violet-50 text-violet-700'],
            ['name' => 'GoPay',         'tag' => 'E-wallet ID', 'color' => 'bg-teal-50 text-teal-700'],
            ['name' => 'Bank Transfer', 'tag' => 'Bank',        'color' => 'bg-slate-50 text-slate-700'],
        ];

        return view('user.wallet', [
            'user'           => $user,
            'withdrawals'    => $withdrawals,
            'methods'        => $methods,
            'minWithdrawal'  => Lootora::minWithdrawal(),
            'usdEquivalent'  => Lootora::toUsd($user->current_points ?? 0),
        ]);
    }
}
