@extends('layouts.member')

@section('title', 'Proxy detected')

@section('content')
<div class="max-w-xl mx-auto text-center py-10">
    <div class="w-20 h-20 mx-auto rounded-2xl bg-rose-50 text-rose-600 grid place-items-center text-4xl mb-5">⛔</div>
    <h1 class="text-2xl font-extrabold text-loot-ink">Proxy / VPN detected</h1>
    <p class="text-loot-muted mt-2">We blocked your request because you appear to be using a proxy or VPN. Please disable it and try again. Fraudulent attempts may permanently suspend your account.</p>

    <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
        <a href="{{ route('user.earn') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl gradient-loot text-white font-bold shadow-cardLg">Try again</a>
        <a href="{{ route('user.home') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 font-semibold text-loot-ink">Back to dashboard</a>
    </div>
</div>
@endsection
