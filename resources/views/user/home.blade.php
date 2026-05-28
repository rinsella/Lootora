@extends('layouts.member')

@section('title', 'Dashboard')

@php use App\Support\Lootora; @endphp

@section('content')

{{-- Promo banner --}}
<div class="rounded-2xl gradient-gold p-5 sm:p-6 text-white shadow-cardLg flex items-start gap-4 mb-6 relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
    <div class="absolute -right-2 -bottom-12 w-32 h-32 rounded-full bg-white/10"></div>
    <div class="w-12 h-12 rounded-2xl bg-white/20 grid place-items-center text-2xl shrink-0">⚡</div>
    <div class="flex-1 relative z-10">
        <p class="text-xs uppercase tracking-wider text-white/90 font-semibold">Welcome back, {{ $user->username ?? 'looter' }}</p>
        <h2 class="text-lg sm:text-xl font-extrabold mt-1 leading-snug">$LOOT is your reward power. Complete missions, earn more, unlock your loot.</h2>
        <a href="{{ route('user.earn') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-bold text-white hover:underline">
            Here's how →
        </a>
    </div>
</div>

{{-- Grid: streak + balance --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft lg:col-span-1">
        <div class="flex items-center justify-between">
            <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">Daily Streak</p>
            <span class="text-2xl">🔥</span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <p class="text-3xl font-extrabold text-loot-ink">{{ (int)($user->day_streak ?? 0) }}</p>
            <p class="text-sm text-loot-muted">day{{ (int)$user->day_streak === 1 ? '' : 's' }}</p>
        </div>
        <p class="text-xs text-loot-muted mt-1">Best streak: <span class="font-semibold text-loot-ink">{{ (int)($user->best_streak ?? 0) }} days</span></p>

        <form action="{{ route('user.checkin') }}" method="POST" class="mt-4">
            @csrf
            <button @if($alreadyCheckedToday) disabled @endif
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition
                       {{ $alreadyCheckedToday ? 'bg-gray-100 text-loot-muted cursor-not-allowed' : 'gradient-loot text-white shadow-cardLg hover:opacity-95' }}">
                @if($alreadyCheckedToday)
                    ✓ Checked in today
                @else
                    Claim daily +{{ Lootora::fmtPoints($checkinReward) }} $LOOT
                @endif
            </button>
        </form>
    </div>

    <div class="rounded-2xl gradient-loot p-5 sm:p-6 text-white shadow-cardLg lg:col-span-2 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/10"></div>
        <div class="relative z-10">
            <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Wallet balance</p>
            <div class="mt-2 flex items-end gap-3 flex-wrap">
                <p class="text-4xl sm:text-5xl font-extrabold leading-none">{{ Lootora::fmtPoints($currentPoints) }}</p>
                <p class="text-sm font-bold opacity-90 pb-1">$LOOT</p>
                <p class="text-sm text-emerald-100 pb-1">≈ ${{ Lootora::fmtUsd($usdEquivalent) }} USD</p>
            </div>

            @php
                $mp = (int)$breakdown['missions'];
                $rp = (int)$breakdown['referrals'];
                $bp = max(0, 100 - $mp - $rp);
            @endphp
            <div class="mt-5">
                <div class="h-2.5 w-full rounded-full bg-white/20 overflow-hidden flex">
                    <div class="h-full bg-white" style="width: {{ $mp }}%"></div>
                    <div class="h-full bg-amber-300" style="width: {{ $rp }}%"></div>
                    <div class="h-full bg-emerald-200" style="width: {{ $bp }}%"></div>
                </div>
                <div class="mt-3 flex flex-wrap gap-4 text-xs text-emerald-100">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-white"></span>Missions {{ $mp }}%</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-300"></span>Referrals {{ $rp }}%</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-200"></span>Bonuses {{ $bp }}%</span>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('user.wallet') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-loot-primary font-semibold text-sm hover:bg-emerald-50">Withdraw</a>
                <a href="{{ route('user.earn') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 text-white font-semibold text-sm hover:bg-white/25">Earn more</a>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card label="Earned today" :value="Lootora::fmtPoints($todayPoints).' $LOOT'" icon="⚡" accent="amber"/>
    <x-stat-card label="All-time" :value="Lootora::fmtPoints($totalPoints).' $LOOT'" icon="🏆" accent="emerald"/>
    <x-stat-card label="Referral code" :value="$user->referral_code ?? '—'" icon="🤝" accent="blue" :sub="'Share & earn '.(int)env('LOOT_REFERRAL_PERCENT', 10).'%'"/>
    <x-stat-card label="Min payout" :value="Lootora::fmtPoints(Lootora::minWithdrawal()).' $LOOT'" icon="🎯" accent="violet"/>
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-bold text-loot-ink">Featured offers</h2>
    <a href="{{ route('user.earn') }}" class="text-sm font-semibold text-loot-primary hover:underline">View all →</a>
</div>

@php
    $cards = $offers->count()
        ? $offers->map(fn($o) => [
            'name'  => $o->name,
            'cat'   => ucfirst($o->category ?? 'Mixed'),
            'desc'  => $o->description ?? 'Complete tasks and earn rewards.',
            'href'  => route('user.earn'),
            'icon'  => '🎯',
            'badge' => $o->is_active ? 'Live' : 'Soon',
        ])
        : collect([
            ['name'=>"Rock N' Cash",    'cat'=>'Games',   'desc'=>'Play casual games & cash out fast.',         'href'=>route('user.earn'),'icon'=>'🎮','badge'=>'Hot'],
            ['name'=>'Survey Sprint',   'cat'=>'Surveys', 'desc'=>'Quick paid surveys, 2–5 minutes each.',     'href'=>route('user.earn'),'icon'=>'📋','badge'=>'Easy'],
            ['name'=>'App Trial Bonus', 'cat'=>'Apps',    'desc'=>'Install featured apps and claim bonuses.',   'href'=>route('user.earn'),'icon'=>'📱','badge'=>'New'],
            ['name'=>'Game Level Quest','cat'=>'Quests',  'desc'=>'Reach a level milestone for big rewards.',   'href'=>route('user.earn'),'icon'=>'⚔️','badge'=>'Big'],
        ]);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($cards as $c)
        <a href="{{ $c['href'] }}" class="group rounded-2xl bg-white border border-loot-border p-5 hover:border-loot-primary hover:shadow-cardLg transition">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-loot-primary grid place-items-center text-xl">{{ $c['icon'] }}</div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-loot-accentDark bg-amber-50 px-2 py-0.5 rounded-full">{{ $c['badge'] }}</span>
            </div>
            <h3 class="mt-4 font-bold text-loot-ink group-hover:text-loot-primary">{{ $c['name'] }}</h3>
            <p class="text-xs text-loot-muted mt-1">{{ Str::limit($c['desc'], 65) }}</p>
            <p class="mt-3 text-[10px] uppercase tracking-wider font-semibold text-loot-muted">{{ $c['cat'] }}</p>
        </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft">
        <h3 class="font-bold text-loot-ink">Quick actions</h3>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <a href="{{ route('user.earn') }}" class="rounded-xl border border-loot-border p-3 hover:border-loot-primary text-center text-sm font-semibold text-loot-ink">
                <div class="text-2xl">🎯</div><div class="mt-1">Earn</div>
            </a>
            <a href="{{ route('user.wallet') }}" class="rounded-xl border border-loot-border p-3 hover:border-loot-primary text-center text-sm font-semibold text-loot-ink">
                <div class="text-2xl">💸</div><div class="mt-1">Withdraw</div>
            </a>
            <a href="{{ route('register', ['ref' => $user->referral_code]) }}" class="rounded-xl border border-loot-border p-3 hover:border-loot-primary text-center text-sm font-semibold text-loot-ink">
                <div class="text-2xl">🤝</div><div class="mt-1">Referral</div>
            </a>
            <a href="{{ route('user.history') }}" class="rounded-xl border border-loot-border p-3 hover:border-loot-primary text-center text-sm font-semibold text-loot-ink">
                <div class="text-2xl">🧾</div><div class="mt-1">History</div>
            </a>
        </div>

        <form action="{{ route('user.home.redeem') }}" method="POST" class="mt-5">
            @csrf
            <label class="text-xs font-semibold text-loot-muted">Redeem bonus code</label>
            <div class="mt-1 flex gap-2">
                <input name="code" placeholder="LOOTBONUS" class="flex-1 px-3 py-2 rounded-xl border border-loot-border focus:outline-none focus:border-loot-primary text-sm uppercase">
                <button class="px-4 py-2 rounded-xl bg-loot-primary text-white text-sm font-semibold hover:bg-loot-primaryDark">Apply</button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft lg:col-span-2">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Recent activity</h3>
            <a href="{{ route('user.transactions') }}" class="text-xs font-semibold text-loot-primary hover:underline">View all →</a>
        </div>

        @if($recent->isEmpty())
            <x-empty-state icon="🎯" title="No activity yet" desc="Complete your first mission to start earning $LOOT and watch your activity appear here." class="mt-4">
                <a href="{{ route('user.earn') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-loot-primary text-white font-semibold text-sm hover:bg-loot-primaryDark">Browse offers →</a>
            </x-empty-state>
        @else
            <ul class="mt-4 divide-y divide-loot-border">
                @foreach($recent as $r)
                    <li class="py-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-loot-primary grid place-items-center">✓</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-loot-ink truncate">{{ $r['title'] }}</p>
                            <p class="text-xs text-loot-muted truncate">{{ $r['meta'] }}</p>
                        </div>
                        <span class="text-sm font-bold {{ ($r['positive'] ?? true) ? 'text-loot-primary' : 'text-red-600' }}">{{ $r['amount'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@endsection
