@extends('layouts.member')

@section('title', 'Leaderboard')

@section('content')
@php $top = $winners->first(); @endphp

@if($top)
<div class="rounded-2xl gradient-loot p-6 sm:p-8 text-white shadow-cardLg mb-6 relative overflow-hidden">
    <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/10"></div>
    <div class="absolute -left-10 -bottom-10 w-48 h-48 rounded-full bg-white/5"></div>
    <div class="relative z-10 flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
        <img src="{{ $top->avatar() }}" alt="{{ $top->username }}" class="w-20 h-20 rounded-2xl ring-4 ring-white/30 object-cover">
        <div class="flex-1">
            <p class="text-xs uppercase tracking-wider text-emerald-100 font-bold">🏆 Champion this period</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Congrats, {{ $top->username }}!</h1>
            <p class="text-emerald-100 text-sm mt-1">{{ number_format($top->total_points) }} $LOOT earned.</p>
        </div>
    </div>
</div>
@endif

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
        <h3 class="font-bold text-loot-ink">Top earners</h3>
        <span class="text-xs text-loot-muted">{{ $winners->count() }} entries</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold w-16">Rank</th>
                    <th class="px-5 py-3 font-semibold">User</th>
                    <th class="px-5 py-3 font-semibold text-right">Points</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($winners as $winner)
                    @php $medal = ['🥇','🥈','🥉'][$loop->index] ?? '#'.($loop->iteration); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-bold text-loot-ink">{{ $medal }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $winner->avatar() }}" alt="" class="w-9 h-9 rounded-full object-cover">
                                <div>
                                    <p class="font-semibold text-loot-ink">{{ $winner->username }}</p>
                                    <p class="text-xs text-loot-muted">{{ trim(($winner->firstname ?? '').' '.($winner->lastname ?? '')) ?: '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-loot-primary">{{ number_format($winner->total_points) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-12 text-center text-loot-muted">No leaders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
