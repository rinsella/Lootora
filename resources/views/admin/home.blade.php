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

@endsection
