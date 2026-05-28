@extends('layouts.member')

@section('title', 'History')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-loot-ink">Your history</h1>
        <p class="text-sm text-loot-muted">Completed offers and earned points.</p>
    </div>
    <a href="{{ route('user.earn') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-xl gradient-loot text-white text-sm font-semibold shadow-cardLg">+ Earn more</a>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">Company</th>
                    <th class="px-5 py-3 font-semibold">Offer</th>
                    <th class="px-5 py-3 font-semibold text-right">Points</th>
                    <th class="px-5 py-3 font-semibold text-right">Completed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-loot-ink">{{ $lead->company }}</td>
                        <td class="px-5 py-3 text-loot-ink/80">{{ $lead->offer_name }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-primary">+{{ number_format($lead->offer_points) }}</td>
                        <td class="px-5 py-3 text-right text-loot-muted text-xs">{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-loot-muted">No completed offers yet. Start earning from <a href="{{ route('user.earn') }}" class="text-loot-primary font-semibold underline">/earn</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
