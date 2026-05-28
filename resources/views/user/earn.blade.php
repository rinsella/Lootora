@extends('layouts.member')

@section('title', 'Earn')

@section('content')

<div class="rounded-2xl gradient-hero p-6 sm:p-8 text-white shadow-cardLg mb-6 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10"></div>
    <div class="relative z-10 max-w-2xl">
        <p class="text-xs uppercase tracking-wider text-emerald-200 font-semibold">Earn $LOOT</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 leading-tight">Complete missions. Earn rewards. Unlock your loot.</h1>
        <p class="mt-2 text-sm text-emerald-100">Browse offers from top providers — surveys, games, apps and more. Rewards are credited automatically.</p>
    </div>
</div>

{{-- Search + Sort --}}
<form method="GET" action="{{ route('user.earn') }}" class="rounded-2xl bg-white border border-loot-border p-4 sm:p-5 shadow-soft mb-5">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search offers…"
                   class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-loot-border focus:outline-none focus:border-loot-primary text-sm">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-loot-muted">🔍</span>
        </div>
        <select name="sort" class="px-3 py-2.5 rounded-xl border border-loot-border bg-white text-sm focus:outline-none focus:border-loot-primary">
            <option value="highest" @selected($sort==='highest')>Highest reward</option>
            <option value="newest"  @selected($sort==='newest')>Newest</option>
            <option value="fastest" @selected($sort==='fastest')>Fastest</option>
        </select>
        <button class="px-5 py-2.5 rounded-xl bg-loot-primary text-white font-semibold text-sm hover:bg-loot-primaryDark">Search</button>
    </div>

    <div class="mt-4 flex gap-2 overflow-x-auto scroll-hide pb-1">
        @foreach($categories as $cat)
            @php $active = ($category ?: 'all') === $cat; @endphp
            <a href="{{ route('user.earn', array_merge(request()->except('category'), ['category' => $cat])) }}"
               class="shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition
                      {{ $active
                            ? 'bg-loot-primary text-white border-loot-primary'
                            : 'bg-white text-loot-ink border-loot-border hover:border-loot-primary' }}">
                {{ ucfirst($cat) }}
            </a>
        @endforeach
    </div>
</form>

@if($offerwalls->isEmpty())
    <x-empty-state icon="🎯" title="No offerwalls are active yet" desc="Please check again later — new providers are added regularly.">
        <a href="{{ route('user.home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-loot-primary text-white font-semibold text-sm hover:bg-loot-primaryDark">Back to dashboard</a>
    </x-empty-state>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($offerwalls as $offerwall)
            @php
                $url = \App\Http\Controllers\User\EarnController::resolveUrl($offerwall);
                $active = (bool) $offerwall->is_active;
            @endphp
            <div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft hover:shadow-cardLg transition flex flex-col">
                <div class="flex items-start gap-3">
                    @if(!empty($offerwall->photo_path))
                        <img src="{{ Storage::url($offerwall->photo_path) }}" alt="{{ $offerwall->name }}" class="w-12 h-12 rounded-xl object-cover border border-loot-border">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-loot-primary grid place-items-center text-xl font-extrabold">
                            {{ strtoupper(substr($offerwall->name ?? '?', 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-loot-ink truncate">{{ $offerwall->name }}</h3>
                        <p class="text-xs text-loot-muted mt-0.5">{{ ucfirst($offerwall->category ?? 'Mixed') }} · {{ ucfirst($offerwall->payout_type ?? 'CPA') }}</p>
                    </div>
                    @if($active)
                        <x-status-badge variant="success" icon="●">Live</x-status-badge>
                    @else
                        <x-status-badge variant="neutral" icon="●">Off</x-status-badge>
                    @endif
                </div>

                <p class="mt-3 text-sm text-loot-muted line-clamp-3 flex-1">
                    {{ $offerwall->description ?: 'Complete short tasks on '.$offerwall->name.' to earn $LOOT rewards.' }}
                </p>

                <div class="mt-4 flex items-center justify-between text-xs">
                    <span class="text-loot-muted">Est. reward</span>
                    <span class="font-bold text-loot-primary">Varies · $LOOT</span>
                </div>

                <div class="mt-4">
                    @if($active && $url && $url !== '#')
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl gradient-loot text-white font-semibold text-sm shadow-cardLg hover:opacity-95">
                            Open offerwall ↗
                        </a>
                    @else
                        <button disabled class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 text-loot-muted font-semibold text-sm cursor-not-allowed">
                            Coming soon
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
