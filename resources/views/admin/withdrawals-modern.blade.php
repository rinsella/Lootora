@extends('layouts.admin-modern')
@section('title', 'Withdrawal Requests')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Withdrawal Requests</h1>
        <p class="text-sm text-loot-muted">Review payouts, approve, mark as paid, or reject (auto-refunds points).</p>
    </div>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 mb-4 text-sm">
        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
    </div>
@endif

{{-- FILTER PILLS --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    @php
        $tabs = [
            'all'      => ['All',      'bg-gray-100 text-loot-ink'],
            'pending'  => ['Pending',  'bg-amber-100 text-loot-accentDark'],
            'approved' => ['Approved', 'bg-blue-100 text-blue-700'],
            'paid'     => ['Paid',     'bg-emerald-100 text-loot-primaryDark'],
            'rejected' => ['Rejected', 'bg-rose-100 text-rose-700'],
        ];
    @endphp
    @foreach($tabs as $key => [$label,$tone])
        <a href="{{ route('admin.withdrawals', ['status' => $key, 'q' => $search]) }}"
           class="px-3 py-1.5 rounded-full text-xs font-bold {{ $status === $key ? 'bg-loot-ink text-white' : $tone.' hover:opacity-80' }}">
           {{ $label }} <span class="opacity-70">({{ $counts[$key] ?? 0 }})</span>
        </a>
    @endforeach

    <form method="GET" action="{{ route('admin.withdrawals') }}" class="ml-auto flex items-center gap-2">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search user, method, account…" class="rounded-xl border border-loot-border px-3 py-1.5 text-sm w-64">
        <button class="px-3 py-1.5 rounded-xl bg-loot-ink text-white text-xs font-bold">Search</button>
    </form>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">User</th>
                    <th class="px-5 py-3 font-semibold">Method</th>
                    <th class="px-5 py-3 font-semibold">Account</th>
                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                    <th class="px-5 py-3 font-semibold text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Requested</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($withdrawals as $w)
                    @php
                        $tone = match($w->status){
                            'pending' => 'bg-amber-50 text-loot-accentDark',
                            'approved' => 'bg-blue-50 text-blue-700',
                            'paid','completed' => 'bg-emerald-50 text-loot-primary',
                            'rejected','cancelled' => 'bg-rose-50 text-rose-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-3">
                            <p class="font-bold text-loot-ink">{{ optional($w->user)->username ?? '—' }}</p>
                            <p class="text-[11px] text-loot-muted">{{ optional($w->user)->email }}</p>
                        </td>
                        <td class="px-5 py-3 font-semibold text-loot-ink">{{ $w->method }}</td>
                        <td class="px-5 py-3 text-loot-ink/80 break-all max-w-xs">{{ $w->account }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-loot-ink">{{ number_format($w->amount) }} pts</td>
                        <td class="px-5 py-3 text-center"><span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $tone }}">{{ $w->status }}</span></td>
                        <td class="px-5 py-3 text-right text-xs text-loot-muted">{{ $w->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($w->status === 'pending')
                                <form method="POST" action="{{ route('admin.withdrawals.approve', $w->id) }}" class="inline">
                                    @csrf
                                    <button class="text-xs font-bold text-blue-600 hover:underline">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.withdrawals.mark-paid', $w->id) }}" class="inline ml-2">
                                    @csrf
                                    <button class="text-xs font-bold text-loot-primary hover:underline">Mark Paid</button>
                                </form>
                                <form method="POST" action="{{ route('admin.withdrawals.reject', $w->id) }}" class="inline ml-2" onsubmit="this.querySelector('input[name=note]').value = prompt('Reason for rejection (optional):') || ''; return confirm('Reject and refund {{ $w->amount }} pts to user?')">
                                    @csrf
                                    <input type="hidden" name="note" value="">
                                    <button class="text-xs font-bold text-rose-600 hover:underline">Reject</button>
                                </form>
                            @elseif($w->status === 'approved')
                                <form method="POST" action="{{ route('admin.withdrawals.mark-paid', $w->id) }}" class="inline">
                                    @csrf
                                    <button class="text-xs font-bold text-loot-primary hover:underline">Mark Paid</button>
                                </form>
                                <form method="POST" action="{{ route('admin.withdrawals.reject', $w->id) }}" class="inline ml-2" onsubmit="this.querySelector('input[name=note]').value = prompt('Reason (optional):') || ''; return confirm('Reject and refund?')">
                                    @csrf
                                    <input type="hidden" name="note" value="">
                                    <button class="text-xs font-bold text-rose-600 hover:underline">Reject</button>
                                </form>
                            @elseif($w->status === 'paid')
                                <span class="text-[10px] text-loot-primary font-bold">✓ paid</span>
                                @if(\Schema::hasColumn('withdrawals','paid_at') && $w->paid_at)
                                    <p class="text-[10px] text-loot-muted">{{ $w->paid_at }}</p>
                                @endif
                            @elseif($w->status === 'rejected')
                                <span class="text-[10px] text-rose-600 font-bold">✗ rejected</span>
                                @if(\Schema::hasColumn('withdrawals','refunded_at') && $w->refunded_at)
                                    <p class="text-[10px] text-loot-muted">refunded</p>
                                @endif
                            @else
                                <span class="text-xs text-loot-muted">—</span>
                            @endif
                            @if(\Schema::hasColumn('withdrawals','admin_note') && $w->admin_note)
                                <p class="text-[11px] text-loot-muted mt-1 italic">"{{ $w->admin_note }}"</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center text-loot-muted">No withdrawals match this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
        <div class="px-5 py-3 border-t border-loot-border">{{ $withdrawals->links() }}</div>
    @endif
</div>

@endsection
