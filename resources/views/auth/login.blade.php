@extends('layouts.auth')

@section('title', 'Sign in')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Welcome back to Lootora</h1>
    <p class="mt-2 text-sm text-loot-muted">Continue earning $LOOT Points from missions, surveys, and offers.</p>

    @if(session('error'))
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="auth-form" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="login-username" class="block text-xs font-semibold text-loot-ink mb-1">Username</label>
            <input id="login-username" type="text" name="username" value="{{ old('username') }}" placeholder="john123" autofocus
                   class="w-full px-4 py-2.5 rounded-xl border @error('username') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="login-password" class="block text-xs font-semibold text-loot-ink">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-loot-primary hover:underline">Forgot password?</a>
                @endif
            </div>
            <input id="login-password" type="password" name="password" placeholder="••••••••"
                   class="w-full px-4 py-2.5 rounded-xl border @error('password') border-red-400 @else border-loot-border @enderror focus:outline-none focus:border-loot-primary text-sm">
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-center gap-2 text-xs text-loot-muted">
            <input type="checkbox" name="remember" class="rounded border-loot-border text-loot-primary focus:ring-loot-primary">
            Keep me signed in
        </label>

        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95 transition">
            Sign in →
        </button>
    </form>

    <p class="mt-6 text-sm text-center text-loot-muted">
        New on Lootora?
        <a href="{{ route('register') }}" class="font-semibold text-loot-primary hover:underline">Create an account</a>
    </p>
@endsection
