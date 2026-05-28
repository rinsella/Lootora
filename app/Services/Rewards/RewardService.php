<?php

namespace App\Services\Rewards;

use App\Models\ReferralEarning;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * RewardService — converts a provider payout (USD) into LOOT Points,
 * splits revenue share, credits the user atomically and triggers
 * referral earnings.
 */
class RewardService
{
    public function defaultUsdToPoints(): float
    {
        return (float) SiteSetting::get('loot_usd_to_points', env('LOOT_USD_TO_POINTS', 1000));
    }

    public function defaultRevenueSharePercent(): float
    {
        return (float) SiteSetting::get('loot_default_revenue_share', env('LOOT_DEFAULT_REVENUE_SHARE', 70));
    }

    public function referralPercent(): float
    {
        return (float) SiteSetting::get('loot_referral_percent', env('LOOT_REFERRAL_PERCENT', 10));
    }

    /**
     * Compute reward breakdown for a given provider payout (USD).
     *
     * @return array{user_points: float, payout_usd: float, platform_profit: float}
     */
    public function compute(float $providerPayoutUsd, ?float $revenueSharePercent = null): array
    {
        $share = $revenueSharePercent ?? $this->defaultRevenueSharePercent();
        $share = max(0.0, min(100.0, (float) $share));

        $userPayoutUsd   = round($providerPayoutUsd * $share / 100, 4);
        $userPoints      = round($userPayoutUsd * $this->defaultUsdToPoints(), 4);
        $platformProfit  = round($providerPayoutUsd - $userPayoutUsd, 4);

        return [
            'user_points'     => $userPoints,
            'payout_usd'      => $userPayoutUsd,
            'platform_profit' => $platformProfit,
        ];
    }

    /**
     * Atomically credit LOOT points to a user. Locks the user row to
     * prevent concurrent double-credits.
     */
    public function creditUser(User $user, float $points): void
    {
        DB::transaction(function () use ($user, $points) {
            /** @var User $locked */
            $locked = User::lockForUpdate()->find($user->id);
            if (!$locked) {
                return;
            }
            $locked->addPoints($points);
        });
    }

    /**
     * Reverse a previous credit (chargeback). Will not push balance below zero.
     */
    public function reverseUser(User $user, float $points): void
    {
        DB::transaction(function () use ($user, $points) {
            /** @var User $locked */
            $locked = User::lockForUpdate()->find($user->id);
            if (!$locked) {
                return;
            }
            $deduct = min((float) $locked->current_points, $points);
            $locked->current_points = (float) $locked->current_points - $deduct;
            $locked->total_points   = max(0, (float) $locked->total_points - $points);
            $locked->save();
        });
    }

    /**
     * Award referral earnings to the upline (if any).
     */
    public function awardReferral(User $referredUser, float $rewardPoints, ?int $sourceTransactionId = null): void
    {
        if (!$referredUser->referred_by) {
            return;
        }
        $percent = $this->referralPercent();
        if ($percent <= 0) {
            return;
        }

        $referralPoints = round($rewardPoints * $percent / 100, 4);
        if ($referralPoints <= 0) {
            return;
        }

        $referrer = User::find($referredUser->referred_by);
        if (!$referrer || $referrer->isBanned()) {
            return;
        }

        DB::transaction(function () use ($referrer, $referredUser, $referralPoints, $sourceTransactionId) {
            $this->creditUser($referrer, $referralPoints);

            ReferralEarning::create([
                'user_id'               => $referrer->id,
                'referred_user_id'      => $referredUser->id,
                'source_transaction_id' => $sourceTransactionId,
                'points'                => $referralPoints,
                'status'                => 'completed',
            ]);
        });
    }
}
