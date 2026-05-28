@extends('layouts.member')

@section('title', 'Transactions')

@section('content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-loot-ink">Transactions</h1>
        <p class="text-sm text-loot-muted">All your withdrawal requests.</p>
    </div>
    <a href="{{ route('user.shop') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-xl gradient-loot text-white text-sm font-semibold shadow-cardLg">+ New withdrawal</a>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">Method</th>
                    <th class="px-5 py-3 font-semibold text-right">Points</th>
                    <th class="px-5 py-3 font-semibold">Destination</th>
                    <th class="px-5 py-3 font-semibold text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Requested</th>
                    <th class="px-5 py-3 font-semibold text-right">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($withdrawals as $w)
                    @php
                        $tone = match($w->status){
                            'pending' => 'bg-amber-50 text-loot-accentDark',
                            'approved','paid','completed' => 'bg-emerald-50 text-loot-primary',
                            'rejected' => 'bg-rose-50 text-rose-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-loot-ink">{{ $w->method }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-ink">{{ number_format($w->amount) }}</td>
                        <td class="px-5 py-3 text-loot-ink/80 truncate max-w-xs">{{ $w->account }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-block text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $tone }}">{{ $w->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-loot-muted">{{ $w->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right text-xs text-loot-muted">{{ $w->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-loot-muted">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
