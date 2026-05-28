@php
    $nav = [
        ['label' => 'Overview',            'route' => 'admin.home',              'icon' => '🏠'],
        ['label' => 'Users',               'route' => 'admin.users',             'icon' => '👥'],
        ['label' => 'Offerwall Providers', 'route' => 'admin.offerwalls',        'icon' => '🎯'],
        ['label' => 'Payout Methods',      'route' => 'admin.payout-methods',    'icon' => '🏦'],
        ['label' => 'Withdrawal Requests', 'route' => 'admin.withdrawals',       'icon' => '💸'],
        ['label' => 'Transactions / Leads','route' => 'admin.leads',             'icon' => '📈'],
        ['label' => 'Postback Logs',       'route' => 'admin.postback-logs',     'icon' => '📡'],
        ['label' => 'Fraud Logs',          'route' => 'admin.fraud-logs',        'icon' => '🛡️'],
        ['label' => 'Bonus Codes',         'route' => 'admin.bonus',             'icon' => '🎁'],
        ['label' => 'Bonus History',       'route' => 'admin.bonus-history',     'icon' => '🧾'],
        ['label' => 'Site Settings',       'route' => 'admin.settings',          'icon' => '⚙️'],
        ['label' => 'Integration Guide',   'route' => 'admin.integration-guide', 'icon' => '📚'],
    ];
@endphp
<aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 flex-col bg-white border-r border-loot-border z-30">
    <div class="px-6 pt-6 pb-5 border-b border-loot-border">
        <a href="{{ route('admin.home') }}" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl gradient-loot grid place-items-center text-white font-extrabold text-lg shadow-cardLg">L</div>
            <div>
                <p class="font-extrabold tracking-tight text-loot-ink leading-none">Lootora</p>
                <p class="text-[10px] uppercase tracking-wider text-loot-accentDark mt-1 font-bold">Admin Console</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scroll-hide">
        @foreach($nav as $item)
            @php
                $active = false;
                try { $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); } catch (\Throwable $e) {}
                $exists = \Illuminate\Support\Facades\Route::has($item['route']);
                $url = $exists ? route($item['route']) : '#';
            @endphp
            <a href="{{ $url }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ $active ? 'bg-emerald-50 text-loot-primary' : 'text-loot-ink hover:bg-gray-50' }}">
                <span class="text-base w-5 text-center">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if($active)<span class="w-1.5 h-1.5 rounded-full bg-loot-primary"></span>@endif
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-loot-border space-y-2">
        <a href="{{ route('user.home') }}" class="block text-center text-xs font-semibold px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-loot-ink">← User panel</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full text-left text-sm text-loot-muted hover:text-loot-ink flex items-center gap-2 px-3 py-2">
                <span>⏻</span> Log out
            </button>
        </form>
    </div>
</aside>
