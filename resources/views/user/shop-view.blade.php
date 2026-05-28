@extends('layouts.member')

@section('title', $payment->name.' checkout')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('user.shop') }}" class="inline-flex items-center text-sm text-loot-muted hover:text-loot-ink mb-4">← Back to shop</a>

    <div class="rounded-2xl gradient-loot p-6 text-white shadow-cardLg mb-5">
        <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Withdraw via</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">{{ $payment->name }}</h1>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 mb-4 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('user.shop.checkout', ['id' => $payment->id]) }}"
          class="rounded-2xl bg-white border border-loot-border p-5 sm:p-6 shadow-soft space-y-4">
        @csrf

        <div>
            <label for="amount" class="block text-xs font-semibold text-loot-ink mb-1.5">Amount in $LOOT <span class="text-rose-500">*</span></label>
            <input id="amount" type="number" name="amount" placeholder="e.g. 5000"
                   class="w-full rounded-xl border {{ $errors->has('amount') ? 'border-rose-300' : 'border-loot-border' }} focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            @error('amount')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="address" class="block text-xs font-semibold text-loot-ink mb-1.5">Account / Address <span class="text-rose-500">*</span></label>
            <input id="address" type="text" name="address" placeholder="Your account, address or number"
                   class="w-full rounded-xl border {{ $errors->has('address') ? 'border-rose-300' : 'border-loot-border' }} focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            @error('address')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center px-5 py-3 rounded-xl gradient-loot text-white font-bold shadow-cardLg hover:opacity-90">
            Confirm checkout
        </button>
    </form>
</div>
@endsection
