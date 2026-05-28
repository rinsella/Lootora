<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\FraudLog;
use App\Models\Lead;
use App\Models\Offerwall;
use App\Models\Payment;
use App\Models\PostbackLog;
use App\Models\ProviderTransaction;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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

        // ===== SYSTEM HEALTH =====
        $dbOk = false;
        try { DB::connection()->getPdo(); $dbOk = true; } catch (\Throwable $e) { $dbOk = false; }
        $storageLinked = is_link(public_path('storage')) || is_dir(public_path('storage'));
        $storageWritable = is_writable(storage_path('app/public')) || is_writable(storage_path('app'));
        $bootstrapWritable = is_writable(base_path('bootstrap/cache'));

        $systemHealth = [
            'app_env'           => config('app.env'),
            'app_debug'         => (bool) config('app.debug'),
            'queue_connection'  => config('queue.default'),
            'cache_driver'      => config('cache.default'),
            'mail_driver'       => config('mail.default') ?? config('mail.driver'),
            'db_connected'      => $dbOk,
            'storage_linked'    => $storageLinked,
            'storage_writable'  => $storageWritable,
            'bootstrap_writable'=> $bootstrapWritable,
        ];

        // ===== DEPLOYMENT STATUS =====
        $deployment = [
            'php_version'      => PHP_VERSION,
            'laravel_version'  => app()->version(),
            'app_url'          => config('app.url'),
            'filesystem'       => config('filesystems.default'),
            'timezone'         => config('app.timezone'),
            'env_file_present' => file_exists(base_path('.env')),
        ];

        // ===== PROVIDER INTEGRATION SUMMARY =====
        $missingTemplate = Schema::hasColumn('offerwalls', 'iframe_url_template')
            ? Offerwall::where('is_active', 1)->where(function ($q) {
                $q->whereNull('iframe_url_template')->orWhere('iframe_url_template', '');
            })->count() : 0;
        $missingSecret = Schema::hasColumn('offerwalls', 'postback_secret')
            ? Offerwall::where('is_active', 1)->where(function ($q) {
                $q->whereNull('postback_secret')->orWhere('postback_secret', '');
            })->count() : 0;
        $missingLogo = 0;
        foreach (Offerwall::where('is_active', 1)->get() as $ow) {
            if (!$ow->hasLogo()) $missingLogo++;
        }
        $providerIntegration = [
            'active'           => $providers['active'],
            'inactive'         => max(0, $providers['total'] - $providers['active']),
            'missing_template' => $missingTemplate,
            'missing_secret'   => $missingSecret,
            'missing_logo'     => $missingLogo,
        ];

        // ===== PAYOUT SUMMARY =====
        $payoutSummary = [
            'methods_active'  => Payment::where('is_active', 1)->count(),
            'methods_total'   => Payment::count(),
            'pending_count'   => Withdrawal::where('status', 'pending')->count(),
            'pending_points'  => (float) Withdrawal::where('status', 'pending')->sum('amount'),
            'paid_count'      => Withdrawal::where('status', 'paid')->count(),
            'paid_points'     => (float) Withdrawal::where('status', 'paid')->sum('amount'),
            'rejected_count'  => Withdrawal::where('status', 'rejected')->count(),
        ];

        // ===== RECENT ADMIN ACTIONS =====
        $recentAdminActions = Schema::hasTable('admin_action_logs')
            ? AdminActionLog::orderByDesc('created_at')->take(10)->get()
            : collect();

        // ===== SETUP CHECKLIST =====
        $hasSiteSettings = Schema::hasTable('site_settings');
        $setupChecklist = [
            'env_file'         => $deployment['env_file_present'],
            'app_key'          => !empty(config('app.key')),
            'db_connected'     => $dbOk,
            'storage_linked'   => $storageLinked,
            'admin_user'       => Schema::hasColumn('users','is_admin') ? User::where('is_admin', 1)->exists() : false,
            'payout_methods'   => Payment::where('is_active', 1)->exists(),
            'site_name_set'    => $hasSiteSettings ? (SiteSetting::where('key','loot_site_name')->exists()) : false,
            'at_least_1_provider' => Offerwall::where('is_active', 1)->exists(),
            'queue_not_sync'   => config('queue.default') !== 'sync',
            'debug_off'        => !((bool) config('app.debug')),
        ];

        return view('admin.home', compact(
            'users','rev','withdrawals','providers','postbacks','fraud',
            'systemHealth','deployment','providerIntegration','payoutSummary',
            'recentAdminActions','setupChecklist'
        ));
    }
}
