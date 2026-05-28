@extends('layouts.auth')

@section('title', 'Confirm password')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Confirm your password</h1>
    <p class="mt-2 text-sm text-loot-muted">For your security, please confirm your password to continue.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-xs font-semibold text-loot-ink mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                   class="w-full px-4 py-2.5 rounded-xl border @error('password') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95 transition">
            Confirm →
        </button>

        @if (Route::has('password.request'))
            <p class="text-sm text-center text-loot-muted">
                <a href="{{ route('password.request') }}" class="font-semibold text-loot-primary hover:underline">Forgot your password?</a>
            </p>
        @endif
    </form>
@endsection
