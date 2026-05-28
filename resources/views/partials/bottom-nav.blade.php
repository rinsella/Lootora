@php
    $items = [
        ['label' => 'Home',   'route' => 'user.home',   'icon' => '🏠'],
        ['label' => 'Earn',   'route' => 'user.earn',   'icon' => '🎯'],
        ['label' => 'Wallet', 'route' => 'user.wallet', 'icon' => '👛'],
        ['label' => 'Alerts', 'route' => 'user.alerts', 'icon' => '🔔'],
        ['label' => 'More',   'route' => 'user.settings','icon' => '☰'],
    ];
@endphp
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-loot-border pb-[env(safe-area-inset-bottom)]">
    <div class="grid grid-cols-5">
        @foreach($items as $i)
            @php $active = request()->routeIs($i['route']); @endphp
            <a href="{{ route($i['route']) }}"
               class="flex flex-col items-center justify-center py-2.5 text-[11px] font-medium transition
                      {{ $active ? 'text-loot-primary' : 'text-loot-muted hover:text-loot-ink' }}">
                <span class="text-lg leading-none">{{ $i['icon'] }}</span>
                <span class="mt-1">{{ $i['label'] }}</span>
                @if($active)<span class="mt-0.5 w-1 h-1 rounded-full bg-loot-primary"></span>@endif
            </a>
        @endforeach
    </div>
</nav>
