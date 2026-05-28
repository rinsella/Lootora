@extends('layouts.member')

@section('title', 'Wallet')

@php use App\Support\Lootora; @endphp

@section('content')

<div class="rounded-2xl gradient-loot p-6 sm:p-8 text-white shadow-cardLg mb-6 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10"></div>
    <div class="relative z-10">
        <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Wallet balance</p>
        <div class="mt-2 flex items-end gap-3 flex-wrap">
            <p class="text-4xl sm:text-5xl font-extrabold leading-none">{{ Lootora::fmtPoints($user->current_points ?? 0) }}</p>
            <p class="text-sm font-bold opacity-90 pb-1">$LOOT</p>
            <p class="text-sm text-emerald-100 pb-1">≈ ${{ Lootora::fmtUsd($usdEquivalent) }} USD</p>
        </div>
        <p class="mt-3 text-xs text-emerald-100">Minimum withdrawal: <span class="font-bold text-white">{{ Lootora::fmtPoints($minWithdrawal) }} $LOOT</span></p>

        @php
            $pct = $minWithdrawal > 0 ? min(100, ((float)($user->current_points ?? 0) / $minWithdrawal) * 100) : 100;
        @endphp
        <div class="mt-4 h-2 w-full rounded-full bg-white/20 overflow-hidden">
            <div class="h-full bg-white" style="width: {{ $pct }}%"></div>
        </div>
        <p class="mt-2 text-xs text-emerald-100">{{ number_format($pct, 0) }}% to next payout</p>
    </div>
</div>

{{-- Payment methods --}}
<h2 class="text-lg font-bold text-loot-ink mb-3">Payout methods</h2>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @foreach($methods as $m)
        <div class="rounded-2xl bg-white border border-loot-border p-4 text-center hover:border-loot-primary transition">
            <div class="w-12 h-12 mx-auto rounded-xl {{ $m['color'] }} grid place-items-center font-extrabold">
                {{ strtoupper(substr($m['name'], 0, 2)) }}
            </div>
            <p class="mt-2 font-semibold text-sm text-loot-ink">{{ $m['name'] }}</p>
            <p class="text-[10px] text-loot-muted">{{ $m['tag'] }}</p>
        </div>
    @endforeach
</div>

{{-- Withdraw CTA --}}
<div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-loot-primary grid place-items-center text-2xl">💸</div>
    <div class="flex-1">
        <h3 class="font-bold text-loot-ink">Request a withdrawal</h3>
        <p class="text-sm text-loot-muted">You need at least {{ Lootora::fmtPoints($minWithdrawal) }} $LOOT to request a payout.</p>
    </div>
    @if((float)($user->current_points ?? 0) >= $minWithdrawal)
        <a href="{{ route('user.shop') }}" class="px-5 py-2.5 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95">Request payout →</a>
    @else
        <a href="{{ route('user.earn') }}" class="px-5 py-2.5 rounded-xl bg-amber-50 text-loot-accentDark font-semibold text-sm hover:bg-amber-100">Earn more first →</a>
    @endif
</div>

{{-- Withdrawal history --}}
<h2 class="text-lg font-bold text-loot-ink mb-3">Withdrawal history</h2>
@if($withdrawals->isEmpty())
    <x-empty-state icon="🧾" title="No withdrawals yet" desc="Your payout history will appear here once you've requested your first withdrawal.">
        <a href="{{ route('user.earn') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-loot-primary text-white font-semibold text-sm hover:bg-loot-primaryDark">Start earning →</a>
    </x-empty-state>
@else
    <div class="rounded-2xl bg-white border border-loot-border overflow-hidden shadow-soft">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-loot-muted text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold">Date</th>
                    <th class="text-left px-4 py-3 font-semibold">Method</th>
                    <th class="text-right px-4 py-3 font-semibold">Amount</th>
                    <th class="text-right px-4 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @foreach($withdrawals as $w)
                    <tr>
                        <td class="px-4 py-3 text-loot-ink">{{ optional($w->created_at)->format('M d, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-loot-ink">{{ $w->method ?? $w->gateway ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-loot-ink">{{ Lootora::fmtPoints($w->amount ?? 0) }} $LOOT</td>
                        <td class="px-4 py-3 text-right">
                            @php
                                $status = strtolower($w->status ?? 'pending');
                                $variant = ['paid'=>'success','completed'=>'success','approved'=>'success','pending'=>'warning','rejected'=>'danger','cancelled'=>'neutral'][$status] ?? 'neutral';
                            @endphp
                            <x-status-badge :variant="$variant">{{ ucfirst($status) }}</x-status-badge>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
