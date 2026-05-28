@extends('layouts.member')

@section('title', 'Live leads')

@section('content')
<div class="mb-5">
    <h1 class="text-xl sm:text-2xl font-extrabold text-loot-ink">Live leads</h1>
    <p class="text-sm text-loot-muted">Real-time feed of completions across the platform.</p>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">User</th>
                    <th class="px-5 py-3 font-semibold">Company</th>
                    <th class="px-5 py-3 font-semibold">Offer</th>
                    <th class="px-5 py-3 font-semibold text-right">Points</th>
                    <th class="px-5 py-3 font-semibold text-right">When</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $lead->user?->avatar() }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <p class="font-semibold text-loot-ink">{{ $lead->user?->username ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-loot-ink/80">{{ $lead->company }}</td>
                        <td class="px-5 py-3 text-loot-ink/80">{{ $lead->offer_name }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-primary">+{{ number_format($lead->offer_points) }}</td>
                        <td class="px-5 py-3 text-right text-xs text-loot-muted">{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-loot-muted">No live activity yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
