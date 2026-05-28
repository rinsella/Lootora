@extends('layouts.auth')

@section('title', 'Create account')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Create your Lootora account</h1>
    <p class="mt-2 text-sm text-loot-muted">Start completing missions and unlock your loot.</p>

    @if(session('error'))
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @php
        $refFromQuery = request()->query('ref') ?? request()->cookie('lootora_ref');
    @endphp

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        @if($refFromQuery)
            <input type="hidden" name="ref" value="{{ $refFromQuery }}">
            <div class="rounded-xl bg-emerald-50 text-loot-primary text-xs px-3 py-2 font-semibold">🤝 Referred by code: {{ strtoupper($refFromQuery) }}</div>
        @endif

        <div>
            <label class="block text-xs font-semibold text-loot-ink mb-1">Username</label>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="looter_123"
                   class="w-full px-4 py-2.5 rounded-xl border @error('username') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-loot-ink mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                   class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-loot-ink mb-1">Password</label>
            <input type="password" name="password" placeholder="At least 8 characters"
                   class="w-full px-4 py-2.5 rounded-xl border @error('password') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-start gap-2 text-xs text-loot-muted">
            <input required type="checkbox" class="mt-0.5 rounded border-loot-border text-loot-primary focus:ring-loot-primary">
            <span>I agree to the <a href="{{ route('term-condition') }}" class="font-semibold text-loot-primary hover:underline">terms</a> and <a href="{{ route('privacy-policy') }}" class="font-semibold text-loot-primary hover:underline">privacy policy</a>.</span>
        </label>

        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95 transition">
            Create account →
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-loot-muted">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-loot-primary hover:underline">Sign in instead</a>
    </p>
@endsection
