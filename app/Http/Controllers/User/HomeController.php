<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\BonusHistory;
use App\Models\Offerwall;
use App\Support\Lootora;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Offerwall preview (up to 4 active providers).
        $offers = Offerwall::where('is_active', true)
            ->orderByRaw('COALESCE(sort_order, 0) asc')
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        // Recent activity: prefer provider transactions, fallback to leads.
        $recent = collect();
        try {
            $recent = \App\Models\ProviderTransaction::where('user_id', $user->id)
                ->latest()->take(5)->get()
                ->map(fn ($t) => [
                    'title'   => $t->offer_name ?: ucfirst($t->provider).' mission',
                    'meta'    => ucfirst($t->provider).' · '.$t->created_at?->diffForHumans(),
                    'amount'  => '+'.Lootora::fmtPoints($t->reward_points).' $LOOT',
                    'positive'=> true,
                ]);
        } catch (\Throwable $e) {
            $recent = collect();
        }

        if ($recent->isEmpty()) {
            try {
                $recent = $user->leads()->latest()->take(5)->get()->map(fn ($l) => [
                    'title'   => $l->offer_name ?? 'Mission completed',
                    'meta'    => 'Lead · '.$l->created_at?->diffForHumans(),
                    'amount'  => '+'.Lootora::fmtPoints($l->points ?? 0).' $LOOT',
                    'positive'=> true,
                ]);
            } catch (\Throwable $e) {
                $recent = collect();
            }
        }

        $current = (float) ($user->current_points ?? 0);
        $today   = (float) ($user->today_points   ?? 0);
        $total   = (float) ($user->total_points   ?? 0);

        // Earnings breakdown (best-effort): missions vs referrals vs bonuses.
        $missions = 0.0; $referrals = 0.0; $bonuses = 0.0;
        try {
            $missions  = (float) \App\Models\ProviderTransaction::where('user_id', $user->id)->where('status', 'completed')->sum('reward_points');
        } catch (\Throwable $e) {}
        try {
            $referrals = (float) \App\Models\ReferralEarning::where('user_id', $user->id)->where('status', 'completed')->sum('points');
        } catch (\Throwable $e) {}
        try {
            $bonuses   = (float) BonusHistory::where('user_id', $user->id)
                ->join('bonuses', 'bonuses.id', '=', 'bonus_histories.bonus_id')
                ->sum('bonuses.points');
        } catch (\Throwable $e) {}

        $sum = $missions + $referrals + $bonuses;
        $pct = $sum > 0
            ? [
                'missions'  => round($missions  / $sum * 100),
                'referrals' => round($referrals / $sum * 100),
                'bonuses'   => round($bonuses   / $sum * 100),
            ]
            : ['missions' => 0, 'referrals' => 0, 'bonuses' => 0];

        $alreadyCheckedToday = $user->last_checkin_at
            && \Illuminate\Support\Carbon::parse($user->last_checkin_at)->isToday();

        return view('user.home', [
            'user'                => $user,
            'offers'              => $offers,
            'recent'              => $recent,
            'currentPoints'       => $current,
            'todayPoints'         => $today,
            'totalPoints'         => $total,
            'usdEquivalent'       => Lootora::toUsd($current),
            'breakdown'           => $pct,
            'breakdownValues'     => compact('missions', 'referrals', 'bonuses'),
            'alreadyCheckedToday' => $alreadyCheckedToday,
            'checkinReward'       => Lootora::dailyCheckinPoints(),
        ]);
    }

    public function redeem(Request $request)
    {
        $code = $request->code;
        $bonus = Bonus::where('code', $code)->first();

        if (!$bonus)
            return redirect()->back()->with('error', 'Invalid bonus code');

        if (!$bonus->is_active)
            return redirect()->back()->with('error', 'Bonus code is not active');

        if ($this->IsBonusRedeemed($bonus->id))
            return redirect()->back()->with('error', 'You have already redeemed this bonus');

        $user = auth()->user();
        $user->addPoints($bonus->points);
        $user->bonusHistory()->create([
            'bonus_id' => $bonus->id,
        ]);

        return redirect()->back()->with('success', 'Bonus redeemed successfully');
    }

    public function IsBonusRedeemed($id): bool
    {
        $history = BonusHistory::where('user_id', auth()->user()->id)->where('bonus_id', $id)->first();
        if ($history)
            return true;

        return false;
    }
}

