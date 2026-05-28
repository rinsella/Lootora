@php
    $nav = [
        ['label' => 'Dashboard',   'route' => 'user.home',        'icon' => '🏠'],
        ['label' => 'Earn',        'route' => 'user.earn',        'icon' => '🎯'],
        ['label' => 'Missions',    'route' => 'user.earn',        'icon' => '⚔️'],
        ['label' => 'Surveys',     'route' => 'user.earn',        'icon' => '📋'],
        ['label' => 'Wallet',      'route' => 'user.wallet',      'icon' => '👛'],
        ['label' => 'Leaderboard', 'route' => 'user.leaderboard', 'icon' => '🏆'],
        ['label' => 'History',     'route' => 'user.history',     'icon' => '🧾'],
        ['label' => 'Shop',        'route' => 'user.shop',        'icon' => '🛍️'],
        ['label' => 'Settings',    'route' => 'user.settings',    'icon' => '⚙️'],
    ];
@endphp
<aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 flex-col bg-white border-r border-loot-border z-30">
    <div class="px-6 pt-6 pb-5 border-b border-loot-border">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl gradient-loot grid place-items-center text-white font-extrabold text-lg shadow-cardLg">L</div>
            <div>
                <p class="font-extrabold tracking-tight text-loot-ink leading-none">Lootora</p>
                <p class="text-[10px] text-loot-muted mt-1">Earn · Unlock · Loot</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scroll-hide">
        @foreach($nav as $item)
            @php
                $active = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ $active ? 'bg-emerald-50 text-loot-primary' : 'text-loot-ink hover:bg-gray-50' }}">
                <span class="text-base w-5 text-center">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if($active)<span class="w-1.5 h-1.5 rounded-full bg-loot-primary"></span>@endif
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-loot-border">
        <a href="{{ route('user.wallet') }}" class="block rounded-2xl gradient-loot p-4 text-white shadow-cardLg hover:opacity-95 transition">
            <p class="text-[10px] uppercase tracking-wider text-emerald-100">Wallet balance</p>
            <p class="mt-1 text-xl font-extrabold">{{ number_format((float)(auth()->user()->current_points ?? 0), 2) }} <span class="text-xs font-bold opacity-90">$LOOT</span></p>
            <p class="text-[10px] text-emerald-100 mt-1">≈ ${{ number_format((float)(auth()->user()->current_points ?? 0) / max((float)(env('LOOT_USD_TO_POINTS', 1000)), 1), 2) }} USD</p>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button class="w-full text-left text-sm text-loot-muted hover:text-loot-ink flex items-center gap-2 px-3 py-2">
                <span>⏻</span> Log out
            </button>
        </form>
    </div>
</aside>
