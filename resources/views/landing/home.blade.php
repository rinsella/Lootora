@extends('layouts.lootora')

@section('title', 'Complete missions. Earn rewards. Unlock your loot.')
@section('description', 'Join Lootora to complete surveys, offers, games and daily missions, then withdraw your LOOT rewards via PayPal, USDT, DANA, OVO, GoPay, bank transfer or gift cards.')

@section('body')
    {{-- ============================== NAV ============================== --}}
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-loot-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl gradient-loot text-white grid place-items-center font-extrabold shadow-cardLg">L</span>
                <span class="font-extrabold text-lg tracking-tight">Lootora<span class="text-loot-accent">.</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-loot-muted">
                <a href="#how" class="hover:text-loot-ink">How it works</a>
                <a href="#rewards" class="hover:text-loot-ink">Rewards</a>
                <a href="#safety" class="hover:text-loot-ink">Safety</a>
                <a href="#faq" class="hover:text-loot-ink">FAQ</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ auth()->user()->is_admin ? route('admin.home') : route('user.home') }}"
                       class="px-4 py-2 rounded-xl bg-loot-primary text-white text-sm font-semibold hover:bg-loot-primaryDark transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline px-4 py-2 rounded-xl text-loot-ink text-sm font-semibold hover:bg-gray-100">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-loot-primary text-white text-sm font-semibold hover:bg-loot-primaryDark transition shadow-cardLg">
                        Start earning
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ============================== HERO ============================== --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-emerald-50 via-white to-white"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    $LOOT rewards · No deposit required
                </span>
                <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.05]">
                    Complete missions.<br>
                    Earn rewards.<br>
                    <span class="text-loot-primary">Unlock your loot.</span>
                </h1>
                <p class="mt-5 text-lg text-loot-muted max-w-xl">
                    Join Lootora, finish surveys, games, offers and daily missions, then withdraw your rewards through your preferred payout method.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-loot-primary text-white font-semibold shadow-cardLg hover:bg-loot-primaryDark transition">
                        Start Earning
                    </a>
                    <a href="#how" class="px-6 py-3 rounded-xl bg-white text-loot-ink font-semibold border border-loot-border hover:bg-gray-50">
                        How It Works
                    </a>
                </div>
                <div class="mt-8 flex items-center gap-6 text-sm text-loot-muted">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-loot-primary" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.71-9.71a1 1 0 00-1.42-1.42L9 10.17 7.71 8.88a1 1 0 10-1.42 1.41l2 2a1 1 0 001.42 0l4-4z" clip-rule="evenodd"/></svg> Free to join</div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-loot-primary" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.71-9.71a1 1 0 00-1.42-1.42L9 10.17 7.71 8.88a1 1 0 10-1.42 1.41l2 2a1 1 0 001.42 0l4-4z" clip-rule="evenodd"/></svg> Mobile-first</div>
                    <div class="flex items-center gap-2"><svg class="w-4 h-4 text-loot-primary" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.71-9.71a1 1 0 00-1.42-1.42L9 10.17 7.71 8.88a1 1 0 10-1.42 1.41l2 2a1 1 0 001.42 0l4-4z" clip-rule="evenodd"/></svg> Transparent</div>
                </div>
            </div>

            {{-- Mock dashboard preview card --}}
            <div class="relative">
                <div class="absolute -inset-6 rounded-3xl bg-gradient-to-tr from-emerald-200/60 to-amber-200/60 blur-2xl"></div>
                <div class="relative bg-white rounded-2xl shadow-soft border border-loot-border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-loot-muted">My Balance</p>
                            <p class="mt-1 text-3xl font-extrabold">16.00 <span class="text-loot-accent">$LOOT</span></p>
                            <p class="text-sm text-loot-muted">≈ 0.016 USD</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl gradient-gold text-white grid place-items-center text-xl font-bold">🔥</div>
                    </div>
                    <div class="mt-5">
                        <div class="flex justify-between text-xs text-loot-muted mb-1">
                            <span>Missions</span><span>Referrals</span><span>Bonuses</span>
                        </div>
                        <div class="flex h-2 rounded-full overflow-hidden bg-gray-100">
                            <div class="bg-loot-primary" style="width:60%"></div>
                            <div class="bg-loot-accent" style="width:25%"></div>
                            <div class="bg-emerald-300" style="width:15%"></div>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-4 gap-2 text-center text-xs font-semibold">
                        <button class="py-2 rounded-xl bg-emerald-50 text-loot-primary">Earn</button>
                        <button class="py-2 rounded-xl bg-amber-50 text-loot-accentDark">Withdraw</button>
                        <button class="py-2 rounded-xl bg-blue-50 text-blue-700">Referral</button>
                        <button class="py-2 rounded-xl bg-gray-50 text-loot-ink">History</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== HOW IT WORKS ============================== --}}
    <section id="how" class="py-20 bg-white border-y border-loot-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">How it works</h2>
                <p class="mt-3 text-loot-muted">Four simple steps from sign-up to payout.</p>
            </div>
            <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach([
                    ['1','Create your account','Sign up free with email — no deposit required.'],
                    ['2','Complete missions','Surveys, offers, games and daily tasks.'],
                    ['3','Earn LOOT Points','Track every reward in real time.'],
                    ['4','Withdraw rewards','PayPal, USDT, DANA, OVO, GoPay, bank or gift cards.'],
                ] as [$n,$t,$d])
                    <div class="p-6 rounded-2xl border border-loot-border bg-loot-bg/40 hover:shadow-soft transition">
                        <div class="w-10 h-10 rounded-xl gradient-loot text-white grid place-items-center font-bold">{{ $n }}</div>
                        <h3 class="mt-4 font-bold text-lg">{{ $t }}</h3>
                        <p class="mt-1 text-sm text-loot-muted">{{ $d }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== REWARDS ============================== --}}
    <section id="rewards" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Supported rewards</h2>
            <p class="mt-3 text-loot-muted max-w-2xl">Choose how you want to cash out. New methods are added by the operator from the admin panel.</p>
            <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
                @foreach(['PayPal','USDT','DANA','OVO','GoPay','Bank Transfer','Gift Cards'] as $m)
                    <div class="aspect-square rounded-2xl bg-white border border-loot-border grid place-items-center text-center p-4 font-semibold text-sm hover:shadow-soft hover:-translate-y-0.5 transition">
                        {{ $m }}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== SAFETY ============================== --}}
    <section id="safety" class="py-20 bg-white border-y border-loot-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Safety & transparency</h2>
                <p class="mt-3 text-loot-muted">We build for trust. No fake earnings, no fake payment proof, no shady popups.</p>
                <ul class="mt-6 space-y-3">
                    @foreach([
                        'No deposit required',
                        'Transparent reward tracking',
                        'Manual review for suspicious activity',
                        'Rewards depend on provider verification',
                    ] as $li)
                        <li class="flex gap-3">
                            <span class="mt-1 w-5 h-5 rounded-full bg-emerald-100 text-loot-primary grid place-items-center text-xs font-bold">✓</span>
                            <span>{{ $li }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-6 text-sm text-amber-900">
                <p class="font-semibold mb-2">Honest disclaimer</p>
                <p>Rewards depend on provider availability, eligibility, and successful completion verification. Lootora does not guarantee earnings.</p>
            </div>
        </div>
    </section>

    {{-- ============================== FAQ ============================== --}}
    <section id="faq" class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-center">Frequently asked questions</h2>
            <div class="mt-10 space-y-3">
                @foreach([
                    ['Is Lootora really free?', 'Yes. Registration is free. You never need to deposit money to earn LOOT Points.'],
                    ['How do I earn LOOT Points?', 'Complete offers, surveys, games and daily missions from our offerwall providers. Your reward is credited after the provider verifies the completion.'],
                    ['When can I withdraw?', 'Once you reach the minimum withdrawal threshold set by the platform, request a payout through your preferred method.'],
                    ['Do referrals earn me LOOT?', 'Yes. You earn a configurable percentage of every reward your referred friends complete.'],
                    ['What happens if I cheat?', 'Suspicious activity is reviewed manually. Confirmed fraud results in suspension and forfeiture of LOOT Points.'],
                ] as [$q,$a])
                    <details class="group rounded-2xl bg-white border border-loot-border p-5 open:shadow-soft">
                        <summary class="cursor-pointer flex justify-between items-center font-semibold">
                            {{ $q }}
                            <span class="ml-4 transition group-open:rotate-45 text-loot-primary text-2xl leading-none">+</span>
                        </summary>
                        <p class="mt-3 text-sm text-loot-muted">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== CTA ============================== --}}
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl gradient-loot text-white p-10 sm:p-14 text-center shadow-cardLg">
                <h3 class="text-3xl sm:text-4xl font-extrabold">Turn missions into rewards.</h3>
                <p class="mt-3 text-emerald-50 max-w-xl mx-auto">Sign up today and start unlocking your loot.</p>
                <a href="{{ route('register') }}" class="inline-block mt-6 px-7 py-3 rounded-xl bg-white text-loot-primary font-bold hover:bg-emerald-50 transition">
                    Create free account
                </a>
            </div>
        </div>
    </section>

    {{-- ============================== FOOTER ============================== --}}
    <footer class="border-t border-loot-border bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl gradient-loot text-white grid place-items-center font-extrabold">L</span>
                    <span class="font-extrabold text-lg">Lootora<span class="text-loot-accent">.</span></span>
                </div>
                <p class="mt-3 text-sm text-loot-muted">Complete missions. Earn rewards. Unlock your loot.</p>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-3">Platform</h4>
                <ul class="space-y-2 text-sm text-loot-muted">
                    <li><a href="#how" class="hover:text-loot-ink">How it works</a></li>
                    <li><a href="#rewards" class="hover:text-loot-ink">Rewards</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-loot-ink">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-3">Legal</h4>
                <ul class="space-y-2 text-sm text-loot-muted">
                    <li><a href="{{ route('term-condition') }}" class="hover:text-loot-ink">Terms of Service</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="hover:text-loot-ink">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-loot-ink">Anti-Fraud Policy</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm mb-3">Contact</h4>
                <ul class="space-y-2 text-sm text-loot-muted">
                    <li><a href="mailto:support@lootora.net" class="hover:text-loot-ink">support@lootora.net</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-loot-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-loot-muted flex flex-col sm:flex-row justify-between gap-2">
                <span>&copy; {{ date('Y') }} Lootora.net · All rights reserved.</span>
                <span>Rewards depend on provider availability, eligibility, and successful completion verification.</span>
            </div>
        </div>
    </footer>
@endsection
