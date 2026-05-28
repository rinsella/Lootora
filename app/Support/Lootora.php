<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Brand-wide helpers. Keep static + side-effect-free.
 */
class Lootora
{
    /** Site-wide LOOT → USD conversion rate (points per USD). */
    public static function usdRate(): float
    {
        return (float) SiteSetting::get('loot_usd_to_points', env('LOOT_USD_TO_POINTS', 1000)) ?: 1000.0;
    }

    /** Convert LOOT points to USD. */
    public static function toUsd(float|int|string $points): float
    {
        return round(((float) $points) / max(self::usdRate(), 1), 4);
    }

    /** Pretty-format LOOT points (no trailing zeros beyond 2 dp). */
    public static function fmtPoints(float|int|string $points): string
    {
        return number_format((float) $points, 2);
    }

    /** Pretty-format USD with 2 dp. */
    public static function fmtUsd(float|int|string $usd): string
    {
        return number_format((float) $usd, 2);
    }

    public static function pointSymbol(): string
    {
        return (string) SiteSetting::get('loot_point_symbol', env('LOOT_POINT_SYMBOL', '$LOOT'));
    }

    public static function pointName(): string
    {
        return (string) SiteSetting::get('loot_point_name', env('LOOT_POINT_NAME', 'LOOT Points'));
    }

    public static function minWithdrawal(): float
    {
        return (float) SiteSetting::get('loot_min_withdrawal_points', env('LOOT_MIN_WITHDRAWAL_POINTS', 5000));
    }

    public static function dailyCheckinPoints(): float
    {
        return (float) SiteSetting::get('loot_daily_checkin_points', env('LOOT_DAILY_CHECKIN_POINTS', 10));
    }
}
