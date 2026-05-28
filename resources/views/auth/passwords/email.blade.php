@extends('layouts.auth')

@section('title', 'Reset password')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Forgot your password?</h1>
    <p class="mt-2 text-sm text-loot-muted">Enter your email and we'll send a reset link.</p>

    @if (session('status'))
        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-loot-ink mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="you@example.com"
                   class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95 transition">
            Send reset link →
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-loot-muted">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-loot-primary hover:underline">Back to sign in</a>
    </p>
@endsection
