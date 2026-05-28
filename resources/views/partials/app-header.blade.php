@php
    $u = auth()->user();
    $pageTitle = trim(View::yieldContent('title')) ?: 'Dashboard';
@endphp
<header class="sticky top-0 z-20 bg-white/85 backdrop-blur border-b border-loot-border">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
        {{-- Mobile brand --}}
        <a href="{{ route('user.home') }}" class="lg:hidden flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-sm">L</div>
            <span class="font-extrabold text-loot-ink">Lootora</span>
        </a>

        {{-- Desktop title --}}
        <div class="hidden lg:block">
            <h1 class="text-base font-bold text-loot-ink">@yield('title', 'Dashboard')</h1>
        </div>

        <div class="flex-1"></div>

        {{-- Status badge --}}
        @php
            $status = $u->status ?? 'active';
            $isOk = !in_array($status, ['banned', 'suspicious']);
        @endphp
        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
            {{ $isOk ? 'bg-emerald-50 text-loot-primary' : 'bg-amber-50 text-loot-accentDark' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $isOk ? 'bg-loot-primary' : 'bg-loot-accent' }}"></span>
            {{ $isOk ? 'Full Access' : ucfirst($status) }}
        </span>

        {{-- Balance pill (mobile shows compact) --}}
        <a href="{{ route('user.wallet') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-loot-primary text-xs font-bold hover:bg-emerald-100">
            <span>$LOOT</span>
            <span>{{ number_format((float)($u->current_points ?? 0), 2) }}</span>
        </a>

        {{-- Notifications --}}
        <a href="{{ route('user.alerts') }}" class="relative w-9 h-9 grid place-items-center rounded-full bg-gray-100 hover:bg-gray-200 text-loot-ink">
            <span>🔔</span>
            @php
                $unread = 0;
                try { $unread = $u->notifications()->where('is_read', 0)->count(); } catch (\Throwable $e) {}
            @endphp
            @if($unread > 0)
                <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-loot-accent text-white text-[10px] font-bold grid place-items-center">{{ $unread > 9 ? '9+' : $unread }}</span>
            @endif
        </a>

        {{-- Avatar (initials, no GD required) --}}
        @php
            $initial = strtoupper(substr($u->username ?? $u->name ?? '?', 0, 1));
            $avatarPath = $u->profile_photo_path ?? null;
        @endphp
        <a href="{{ route('user.settings') }}" class="w-9 h-9 rounded-full overflow-hidden ring-2 ring-loot-border hover:ring-loot-primary transition grid place-items-center bg-emerald-50 text-loot-primary font-extrabold">
            @if($avatarPath && file_exists(storage_path('app/public/'.$avatarPath)))
                <img src="{{ asset('storage/'.$avatarPath) }}" alt="avatar" class="w-full h-full object-cover">
            @else
                {{ $initial }}
            @endif
        </a>
    </div>
</header>
