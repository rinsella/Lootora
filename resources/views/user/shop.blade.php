@extends('layouts.member')

@section('title', 'Shop')

@section('content')
<div class="mb-5">
    <h1 class="text-xl sm:text-2xl font-extrabold text-loot-ink">Shop & payouts</h1>
    <p class="text-sm text-loot-muted">Pick a payment method to cash out your $LOOT.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($payments as $payment)
        <a href="{{ route('user.shop.view', ['id' => $payment->id]) }}"
           class="group rounded-2xl bg-white border border-loot-border p-5 shadow-soft hover:border-loot-primary hover:shadow-cardLg transition flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl gradient-loot grid place-items-center text-white font-extrabold text-lg">
                {{ strtoupper(substr($payment->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-loot-ink truncate">{{ $payment->name }}</p>
                <p class="text-xs text-loot-muted">Tap to checkout →</p>
            </div>
            <span class="text-loot-primary opacity-0 group-hover:opacity-100 transition">→</span>
        </a>
    @empty
        <div class="col-span-full rounded-2xl bg-white border border-loot-border p-10 text-center text-loot-muted">No payout methods available yet.</div>
    @endforelse
</div>
@endsection
