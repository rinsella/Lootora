@extends('layouts.auth')

@section('title', 'Verify your email')

@section('form')
    <h1 class="text-2xl sm:text-3xl font-extrabold text-loot-ink">Verify your email 📧</h1>
    <p class="mt-2 text-sm text-loot-muted">We've sent a verification link to your inbox. Open it to activate your Lootora account.</p>

    @if(session('resent'))
        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-loot-primaryDark font-semibold">
            ✓ A fresh verification link has been sent to your email address.
        </div>
    @endif

    <div class="mt-6 rounded-2xl border border-loot-border bg-white p-5">
        <p class="text-sm text-loot-ink">Didn't get the email?</p>
        <form method="POST" action="{{ route('verification.resend') }}" class="mt-3">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95">
                Resend verification email →
            </button>
        </form>
    </div>

    <div class="mt-6 text-sm text-center text-loot-muted">
        Wrong account?
        <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
            <button class="font-semibold text-loot-primary hover:underline">Sign out</button>
        </form>
    </div>
@endsection
