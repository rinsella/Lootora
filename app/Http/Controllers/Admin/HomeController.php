<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $profit = [
            'total'      => (float) Lead::sum('payout'),
            'today'      => (float) Lead::whereDate('created_at', today())->sum('payout'),
            'this_month' => (float) Lead::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('payout'),
        ];

        // Active = logged in / seen within last 24h (was incorrectly using last_login_ip as date)
        $since = Carbon::now()->subDay();

        $users = [
            'count'  => User::count(),
            'new'    => User::where('created_at', '>=', $since)->count(),
            'active' => User::where('last_seen_at', '>=', $since)->count(),
            // Banned = is_banned flag OR status='banned' (was incorrectly using is_admin)
            'banned' => User::where('is_banned', 1)->orWhere('status', 'banned')->count(),
        ];

        $withdrawals = [
            'count'    => Withdrawal::count(),
            'pending'  => Withdrawal::where('status', 'pending')->count(),
            'paid'     => Withdrawal::whereIn('status', ['approved', 'paid', 'completed'])->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'refunded' => Withdrawal::whereIn('status', ['refunded', 'cancelled'])->count(),
        ];

        $recentUsers   = User::latest()->take(6)->get();
        $recentPayouts = Withdrawal::with('user')->latest()->take(6)->get();

        return view('admin.home', compact('profit', 'users', 'withdrawals', 'recentUsers', 'recentPayouts'));
    }
}

