@php $u = auth()->user(); @endphp
<header class="sticky top-0 z-20 bg-white/85 backdrop-blur border-b border-loot-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
        <a href="{{ route('admin.home') }}" class="lg:hidden flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-sm">L</div>
            <span class="font-extrabold text-loot-ink">Admin</span>
        </a>

        <div class="flex-1"></div>

        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-loot-accentDark">
            <span class="w-1.5 h-1.5 rounded-full bg-loot-accent"></span>
            Admin mode
        </span>

        <span class="hidden sm:inline-block text-xs text-loot-muted">{{ $u->username ?? 'admin' }}</span>

        <a href="{{ route('user.settings') }}" class="w-9 h-9 rounded-full ring-2 ring-loot-border hover:ring-loot-primary transition grid place-items-center bg-emerald-50 text-loot-primary font-extrabold">
            {{ strtoupper(substr($u->username ?? 'A', 0, 1)) }}
        </a>
    </div>
</header>
