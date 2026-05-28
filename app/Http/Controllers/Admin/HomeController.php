<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudLog;
use App\Models\Lead;
use App\Models\Offerwall;
use App\Models\PostbackLog;
use App\Models\ProviderTransaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $since24h = Carbon::now()->subDay();
        $monthStart = Carbon::now()->startOfMonth();

        // ===== USER STATS =====
        $users = [
            'count'      => User::count(),
            'new_today'  => User::whereDate('created_at', today())->count(),
            'active'     => Schema::hasColumn('users', 'last_seen_at')
                ? User::where('last_seen_at', '>=', $since24h)->count()
                : User::where('updated_at', '>=', $since24h)->count(),
            'banned'     => Schema::hasColumn('users', 'is_banned') ? User::where('is_banned', 1)->count() : 0,
            'suspicious' => Schema::hasColumn('users', 'status') ? User::where('status', 'suspicious')->count() : 0,
        ];

        // ===== REVENUE =====
        $hasPT = Schema::hasTable('provider_transactions');
        $rev = [
            'today_user'    => $hasPT ? (float) ProviderTransaction::whereDate('created_at', today())->sum('reward_points') : 0,
            'today_payout'  => $hasPT ? (float) ProviderTransaction::whereDate('created_at', today())->sum('payout_usd') : 0,
            'today_profit'  => $hasPT ? (float) ProviderTransaction::whereDate('created_at', today())->sum('platform_profit') : 0,
            'month_user'    => $hasPT ? (float) ProviderTransaction::where('created_at', '>=', $monthStart)->sum('reward_points') : 0,
            'month_payout'  => $hasPT ? (float) ProviderTransaction::where('created_at', '>=', $monthStart)->sum('payout_usd') : 0,
            'month_profit'  => $hasPT ? (float) ProviderTransaction::where('created_at', '>=', $monthStart)->sum('platform_profit') : 0,
            'total_user'    => $hasPT ? (float) ProviderTransaction::sum('reward_points') : 0,
            'total_payout'  => $hasPT ? (float) ProviderTransaction::sum('payout_usd') : 0,
            'total_profit'  => $hasPT ? (float) ProviderTransaction::sum('platform_profit') : (float) Lead::sum('payout'),
        ];

        // ===== WITHDRAWALS =====
        $withdrawals = [
            'pending'  => Withdrawal::where('status', 'pending')->count(),
            'paid'     => Withdrawal::whereIn('status', ['approved','paid','completed'])->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'total'    => Withdrawal::count(),
            'queue'    => Withdrawal::with('user')->where('status', 'pending')->orderByDesc('created_at')->take(5)->get(),
        ];

        // ===== PROVIDERS =====
        $providers = [
            'active'   => Offerwall::where('is_active', 1)->count(),
            'total'    => Offerwall::count(),
            'list'     => Offerwall::orderByDesc('is_active')->orderBy('sort_order')->take(8)->get(),
        ];

        // ===== POSTBACKS =====
        $hasPL = Schema::hasTable('postback_logs');
        $postbacks = [
            'recent' => $hasPL ? PostbackLog::orderByDesc('created_at')->take(10)->get() : collect(),
            'failed' => $hasPL ? PostbackLog::whereIn('status', ['rejected','error','duplicate'])->where('created_at','>=', $since24h)->count() : 0,
        ];

        // ===== FRAUD =====
        $hasFL = Schema::hasTable('fraud_logs');
        $fraud = [
            'recent' => $hasFL ? FraudLog::orderByDesc('created_at')->take(5)->get() : collect(),
            'total'  => $hasFL ? FraudLog::count() : 0,
        ];

        return view('admin.home', compact('users','rev','withdrawals','providers','postbacks','fraud'));
    }
}
