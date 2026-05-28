@extends('layouts.admin-modern')
@section('title', 'Admin · Overview')

@php use App\Support\Lootora; @endphp

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Overview</h1>
        <p class="text-sm text-loot-muted">Real-time KPIs for {{ \App\Models\SiteSetting::get('loot_site_name', config('app.name', 'Lootora')) }}.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.withdrawals') }}" class="px-4 py-2 rounded-xl gradient-loot text-white text-sm font-bold shadow-soft hover:opacity-90">Review withdrawals ({{ $withdrawals['pending'] }})</a>
        <a href="{{ route('admin.offerwalls.create') }}" class="px-4 py-2 rounded-xl bg-loot-ink text-white text-sm font-bold hover:opacity-90">+ Add provider</a>
    </div>
</div>

{{-- ===== KPI CARDS ===== --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @php
        $kpis = [
            ['Total Users', $users['count'], $users['new_today'].' new today', 'bg-blue-50 text-blue-600', '👥'],
            ['Active 24h', $users['active'], 'last seen', 'bg-emerald-50 text-loot-primary', '⚡'],
            ['Suspicious', $users['suspicious'], 'flagged', 'bg-amber-50 text-loot-accentDark', '⚠️'],
            ['Banned', $users['banned'], 'users', 'bg-rose-50 text-rose-600', '🚫'],
            ['Active Providers', $providers['active'].'/'.$providers['total'], 'live offerwalls', 'bg-violet-50 text-violet-600', '🎯'],
            ['Failed Postbacks 24h', $postbacks['failed'], 'investigate', 'bg-orange-50 text-orange-600', '❌'],
        ];
    @endphp
    @foreach($kpis as [$label, $value, $sub, $tone, $emoji])
        <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-4">
            <div class="flex items-start justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider text-loot-muted">{{ $label }}</p>
                <div class="w-9 h-9 rounded-xl grid place-items-center {{ $tone }}">{{ $emoji }}</div>
            </div>
            <p class="mt-2 text-2xl font-extrabold text-loot-ink">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            <p class="text-xs text-loot-muted mt-0.5">{{ $sub }}</p>
        </div>
    @endforeach
</div>

{{-- ===== REVENUE ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-loot-ink">Revenue summary</h2>
        <span class="text-xs text-loot-muted">All amounts in USD / $LOOT</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $cols = [
                ['Today', $rev['today_user'], $rev['today_payout'], $rev['today_profit']],
                ['This month', $rev['month_user'], $rev['month_payout'], $rev['month_profit']],
                ['All time', $rev['total_user'], $rev['total_payout'], $rev['total_profit']],
            ];
        @endphp
        @foreach($cols as [$title,$rew,$pay,$prof])
            <div class="rounded-xl border border-loot-border p-4">
                <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">{{ $title }}</p>
                <div class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-loot-muted">User rewards</span><span class="font-bold text-loot-ink">{{ number_format($rew, 2) }} pts</span></div>
                    <div class="flex justify-between"><span class="text-loot-muted">Provider payout</span><span class="font-bold text-loot-ink">${{ number_format($pay, 2) }}</span></div>
                    <div class="flex justify-between pt-2 border-t border-loot-border"><span class="font-semibold text-loot-primaryDark">Platform profit</span><span class="font-extrabold text-loot-primary">${{ number_format($prof, 2) }}</span></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- ===== WITHDRAWAL QUEUE ===== --}}
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Pending withdrawals <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-amber-50 text-loot-accentDark font-bold">{{ $withdrawals['pending'] }}</span></h3>
            <a href="{{ route('admin.withdrawals') }}" class="text-xs font-bold text-loot-primary hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-loot-border">
            @forelse($withdrawals['queue'] as $w)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-loot-ink truncate">{{ optional($w->user)->username ?? '—' }}</p>
                        <p class="text-xs text-loot-muted truncate">{{ $w->method }} · {{ $w->account }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-extrabold text-loot-ink">{{ number_format($w->amount) }} pts</p>
                        <p class="text-[10px] text-loot-muted">{{ $w->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-loot-muted">All caught up — no pending withdrawals.</div>
            @endforelse
        </div>
    </div>

    {{-- ===== PROVIDER HEALTH ===== --}}
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Offerwall providers</h3>
            <a href="{{ route('admin.offerwalls') }}" class="text-xs font-bold text-loot-primary hover:underline">Manage →</a>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
            @forelse($providers['list'] as $p)
                <div class="rounded-xl border border-loot-border p-3 text-center">
                    @if($p->logoUrl())
                        <img src="{{ $p->logoUrl() }}" alt="{{ $p->name }}" class="w-10 h-10 mx-auto object-cover rounded-lg">
                    @else
                        <div class="w-10 h-10 mx-auto rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-sm">{{ $p->initials() }}</div>
                    @endif
                    <p class="mt-2 text-xs font-bold text-loot-ink truncate">{{ $p->name }}</p>
                    <span class="inline-block mt-1 text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full {{ $p->is_active ? 'bg-emerald-50 text-loot-primary' : 'bg-gray-100 text-loot-muted' }}">{{ $p->is_active ? 'live' : 'off' }}</span>
                </div>
            @empty
                <p class="col-span-full text-center text-sm text-loot-muted py-6">No providers yet. <a href="{{ route('admin.offerwalls.create') }}" class="font-bold text-loot-primary">Add the first one →</a></p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- ===== POSTBACK LOG ===== --}}
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Recent postbacks</h3>
            <a href="{{ route('admin.postback-logs') }}" class="text-xs font-bold text-loot-primary hover:underline">Logs →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-loot-border">
                @forelse($postbacks['recent'] as $pl)
                    @php
                        $tone = match($pl->status){
                            'accepted' => 'bg-emerald-50 text-loot-primary',
                            'duplicate' => 'bg-amber-50 text-loot-accentDark',
                            'rejected','error' => 'bg-rose-50 text-rose-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr>
                        <td class="px-5 py-2.5 font-mono text-xs">{{ $pl->provider }}</td>
                        <td class="px-5 py-2.5 text-xs text-loot-muted">{{ \Illuminate\Support\Str::limit($pl->transaction_id, 14) }}</td>
                        <td class="px-5 py-2.5 text-right font-bold">${{ number_format((float)$pl->payout, 4) }}</td>
                        <td class="px-5 py-2.5 text-right"><span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $tone }}">{{ $pl->status }}</span></td>
                    </tr>
                @empty
                    <tr><td class="px-5 py-8 text-center text-sm text-loot-muted">No postbacks yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== FRAUD LOG ===== --}}
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Fraud alerts <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold">{{ $fraud['total'] }}</span></h3>
            <a href="{{ route('admin.fraud-logs') }}" class="text-xs font-bold text-loot-primary hover:underline">All logs →</a>
        </div>
        <div class="divide-y divide-loot-border">
            @forelse($fraud['recent'] as $f)
                <div class="px-5 py-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-loot-ink">{{ $f->type ?? 'event' }}</p>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-rose-50 text-rose-700">risk {{ $f->risk_score ?? 0 }}</span>
                    </div>
                    <p class="text-xs text-loot-muted mt-0.5">{{ \Illuminate\Support\Str::limit($f->message ?? '', 120) }}</p>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-loot-muted">No fraud events logged.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ===== QUICK ACTIONS ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
    <h3 class="font-bold text-loot-ink mb-3">Quick actions</h3>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.offerwalls.create') }}" class="px-4 py-2 rounded-xl bg-loot-primary text-white text-sm font-bold hover:bg-loot-primaryDark">+ Add provider</a>
        <a href="{{ route('admin.payout-methods.create') }}" class="px-4 py-2 rounded-xl bg-loot-accent text-white text-sm font-bold hover:bg-loot-accentDark">+ Add payout method</a>
        <a href="{{ route('admin.bonus') }}" class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold hover:opacity-90">+ Bonus code</a>
        <a href="{{ route('admin.settings') }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white text-sm font-bold hover:opacity-90">Settings</a>
        <a href="{{ route('admin.integration-guide') }}" class="px-4 py-2 rounded-xl bg-white border border-loot-border text-loot-ink text-sm font-bold hover:bg-gray-50">Integration guide</a>
    </div>
</div>

{{-- ===== SYSTEM HEALTH + DEPLOYMENT ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5">
        <h3 class="font-bold text-loot-ink mb-3">System Health</h3>
        @php
            $row = function($label, $ok, $value=null) {
                $cls = $ok ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700';
                $icon = $ok ? '✓' : '✗';
                return [$label, $cls, $icon, $value];
            };
            $rows = [
                $row('Database', $systemHealth['db_connected']),
                $row('Storage symlink', $systemHealth['storage_linked']),
                $row('Storage writable', $systemHealth['storage_writable']),
                $row('Bootstrap cache writable', $systemHealth['bootstrap_writable']),
                $row('Debug mode OFF (prod safe)', !$systemHealth['app_debug']),
            ];
        @endphp
        <ul class="text-sm divide-y divide-loot-border">
            @foreach($rows as [$label, $cls, $icon, $value])
                <li class="flex items-center justify-between py-2">
                    <span class="text-loot-ink">{{ $label }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $cls }}">{{ $icon }}</span>
                </li>
            @endforeach
            <li class="flex items-center justify-between py-2">
                <span class="text-loot-muted text-xs">Queue / Cache / Mail</span>
                <span class="text-xs font-mono text-loot-ink">{{ $systemHealth['queue_connection'] }} / {{ $systemHealth['cache_driver'] }} / {{ $systemHealth['mail_driver'] }}</span>
            </li>
        </ul>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5">
        <h3 class="font-bold text-loot-ink mb-3">Deployment</h3>
        <dl class="text-sm divide-y divide-loot-border">
            <div class="flex justify-between py-2"><dt class="text-loot-muted">PHP</dt><dd class="font-mono text-loot-ink">{{ $deployment['php_version'] }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-loot-muted">Laravel</dt><dd class="font-mono text-loot-ink">{{ $deployment['laravel_version'] }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-loot-muted">App env</dt><dd class="font-mono text-loot-ink">{{ $systemHealth['app_env'] }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-loot-muted">App URL</dt><dd class="font-mono text-loot-ink text-xs truncate max-w-[60%]">{{ $deployment['app_url'] }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-loot-muted">Filesystem</dt><dd class="font-mono text-loot-ink">{{ $deployment['filesystem'] }}</dd></div>
            <div class="flex justify-between py-2"><dt class="text-loot-muted">Timezone</dt><dd class="font-mono text-loot-ink">{{ $deployment['timezone'] }}</dd></div>
        </dl>
    </div>
</div>

{{-- ===== PROVIDER INTEGRATION + PAYOUT SUMMARY ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5">
        <h3 class="font-bold text-loot-ink mb-3">Provider Integration</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl bg-emerald-50 p-3"><p class="text-loot-muted text-xs">Active</p><p class="font-extrabold text-emerald-700 text-lg">{{ $providerIntegration['active'] }}</p></div>
            <div class="rounded-xl bg-gray-50 p-3"><p class="text-loot-muted text-xs">Inactive</p><p class="font-extrabold text-loot-ink text-lg">{{ $providerIntegration['inactive'] }}</p></div>
            <div class="rounded-xl {{ $providerIntegration['missing_template'] > 0 ? 'bg-amber-50' : 'bg-gray-50' }} p-3"><p class="text-loot-muted text-xs">Missing URL template</p><p class="font-extrabold text-lg {{ $providerIntegration['missing_template'] > 0 ? 'text-amber-700' : 'text-loot-ink' }}">{{ $providerIntegration['missing_template'] }}</p></div>
            <div class="rounded-xl {{ $providerIntegration['missing_secret'] > 0 ? 'bg-rose-50' : 'bg-gray-50' }} p-3"><p class="text-loot-muted text-xs">Missing postback secret</p><p class="font-extrabold text-lg {{ $providerIntegration['missing_secret'] > 0 ? 'text-rose-700' : 'text-loot-ink' }}">{{ $providerIntegration['missing_secret'] }}</p></div>
            <div class="rounded-xl {{ $providerIntegration['missing_logo'] > 0 ? 'bg-amber-50' : 'bg-gray-50' }} p-3 col-span-2"><p class="text-loot-muted text-xs">Active providers missing logo</p><p class="font-extrabold text-lg {{ $providerIntegration['missing_logo'] > 0 ? 'text-amber-700' : 'text-loot-ink' }}">{{ $providerIntegration['missing_logo'] }}</p></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5">
        <h3 class="font-bold text-loot-ink mb-3">Payout Summary</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl bg-emerald-50 p-3"><p class="text-loot-muted text-xs">Active methods</p><p class="font-extrabold text-emerald-700 text-lg">{{ $payoutSummary['methods_active'] }} / {{ $payoutSummary['methods_total'] }}</p></div>
            <div class="rounded-xl bg-amber-50 p-3"><p class="text-loot-muted text-xs">Pending</p><p class="font-extrabold text-amber-700 text-lg">{{ $payoutSummary['pending_count'] }}</p><p class="text-[10px] text-loot-muted">{{ number_format($payoutSummary['pending_points'], 0) }} pts</p></div>
            <div class="rounded-xl bg-blue-50 p-3"><p class="text-loot-muted text-xs">Paid</p><p class="font-extrabold text-blue-700 text-lg">{{ $payoutSummary['paid_count'] }}</p><p class="text-[10px] text-loot-muted">{{ number_format($payoutSummary['paid_points'], 0) }} pts</p></div>
            <div class="rounded-xl bg-rose-50 p-3"><p class="text-loot-muted text-xs">Rejected</p><p class="font-extrabold text-rose-700 text-lg">{{ $payoutSummary['rejected_count'] }}</p></div>
        </div>
    </div>
</div>

{{-- ===== RECENT ADMIN ACTIONS + SETUP CHECKLIST ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 mb-6">
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft">
        <div class="px-5 py-4 border-b border-loot-border"><h3 class="font-bold text-loot-ink">Recent Admin Actions</h3></div>
        <div class="divide-y divide-loot-border">
            @forelse($recentAdminActions as $a)
                <div class="px-5 py-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-loot-ink">{{ $a->action }}</span>
                        <span class="text-[10px] text-loot-muted">{{ $a->created_at?->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-loot-muted">admin#{{ $a->admin_id }} @if($a->target_user_id) → user#{{ $a->target_user_id }} @endif @if($a->ip_address) · {{ $a->ip_address }} @endif</p>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-loot-muted">No admin actions recorded yet.</div>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5">
        <h3 class="font-bold text-loot-ink mb-3">Setup Checklist</h3>
        <ul class="text-sm divide-y divide-loot-border">
            @foreach([
                'env_file' => '.env file present',
                'app_key' => 'APP_KEY generated',
                'db_connected' => 'Database connected',
                'storage_linked' => 'public/storage symlinked',
                'admin_user' => 'Admin user exists',
                'payout_methods' => 'At least 1 active payout method',
                'site_name_set' => 'Site name configured',
                'at_least_1_provider' => 'At least 1 active provider',
                'queue_not_sync' => 'Queue not running on sync',
                'debug_off' => 'APP_DEBUG=false (production)',
            ] as $k => $label)
                <li class="flex items-center justify-between py-2">
                    <span class="text-loot-ink">{{ $label }}</span>
                    @if($setupChecklist[$k])
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">✓ done</span>
                    @else
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">⚠ todo</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

@endsection
