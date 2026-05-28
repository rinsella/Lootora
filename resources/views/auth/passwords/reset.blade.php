@extends('layouts.auth')

@section('title', 'Reset password')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Reset your password</h1>
    <p class="mt-2 text-sm text-loot-muted">Choose a new password to regain access to your Lootora account.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-semibold text-loot-ink mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-loot-ink mb-1">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full px-4 py-2.5 rounded-xl border @error('password') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password-confirm" class="block text-xs font-semibold text-loot-ink mb-1">Confirm password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full px-4 py-2.5 rounded-xl border border-loot-border focus:outline-none focus:border-loot-primary text-sm">
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95 transition">
            Reset password →
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-loot-muted">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-loot-primary hover:underline">Back to sign in</a>
    </p>
@endsection
