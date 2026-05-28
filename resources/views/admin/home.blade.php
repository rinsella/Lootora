@extends('layouts.admin-modern')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl p-5 gradient-loot text-white shadow-cardLg">
        <p class="text-xs uppercase tracking-wider opacity-90 font-semibold">Profit today</p>
        <p class="text-3xl font-extrabold mt-2">${{ number_format($profit['today'], 2) }}</p>
        <p class="text-xs opacity-80 mt-1">Sum of all lead payouts since midnight</p>
    </div>
    <div class="rounded-2xl p-5 bg-white border border-loot-border shadow-soft">
        <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">This month</p>
        <p class="text-3xl font-extrabold mt-2 text-loot-ink">${{ number_format($profit['this_month'], 2) }}</p>
        <p class="text-xs text-loot-muted mt-1">{{ now()->format('F Y') }}</p>
    </div>
    <div class="rounded-2xl p-5 bg-white border border-loot-border shadow-soft">
        <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">All-time profit</p>
        <p class="text-3xl font-extrabold mt-2 text-loot-ink">${{ number_format($profit['total'], 2) }}</p>
        <p class="text-xs text-loot-muted mt-1">Since launch</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $stat = [
            ['label'=>'Total users','value'=>$users['count'],'tone'=>'bg-emerald-50 text-loot-primary'],
            ['label'=>'New (24h)','value'=>$users['new'],'tone'=>'bg-blue-50 text-blue-700'],
            ['label'=>'Active (24h)','value'=>$users['active'],'tone'=>'bg-amber-50 text-loot-accentDark'],
            ['label'=>'Banned','value'=>$users['banned'],'tone'=>'bg-rose-50 text-rose-700'],
        ];
    @endphp
    @foreach($stat as $s)
        <div class="rounded-2xl bg-white border border-loot-border p-4 shadow-soft">
            <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full {{ $s['tone'] }}">{{ $s['label'] }}</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-loot-ink mt-2">{{ number_format($s['value']) }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl bg-white border border-loot-border p-5 shadow-soft">
        <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">Total withdrawals</p>
        <p class="text-3xl font-extrabold text-loot-ink mt-2">{{ number_format($withdrawals['count']) }}</p>
    </div>
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-5">
        <p class="text-xs uppercase tracking-wider text-loot-primary font-semibold">Paid</p>
        <p class="text-3xl font-extrabold text-loot-primaryDark mt-2">{{ number_format($withdrawals['paid']) }}</p>
    </div>
    <div class="rounded-2xl bg-amber-50 border border-amber-100 p-5">
        <p class="text-xs uppercase tracking-wider text-loot-accentDark font-semibold">Pending</p>
        <p class="text-3xl font-extrabold text-loot-accentDark mt-2">{{ number_format($withdrawals['pending']) }}</p>
    </div>
    <div class="rounded-2xl bg-rose-50 border border-rose-100 p-5">
        <p class="text-xs uppercase tracking-wider text-rose-700 font-semibold">Rejected · Refunded</p>
        <p class="text-3xl font-extrabold text-rose-700 mt-2">{{ number_format($withdrawals['rejected'] + $withdrawals['refunded']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Recent users</h3>
            <a href="{{ route('admin.users') }}" class="text-xs font-semibold text-loot-primary hover:underline">All →</a>
        </div>
        <div class="divide-y divide-loot-border">
            @forelse($recentUsers as $user)
                <a href="{{ route('admin.users.view', ['id' => $user->id]) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                    <img src="{{ $user->avatar() }}" class="w-9 h-9 rounded-full object-cover" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-loot-ink truncate">{{ $user->username }}</p>
                        <p class="text-xs text-loot-muted truncate">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs text-loot-muted">{{ $user->created_at?->diffForHumans() }}</span>
                </a>
            @empty
                <p class="px-5 py-6 text-sm text-loot-muted text-center">No users yet.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
        <div class="px-5 py-4 border-b border-loot-border flex items-center justify-between">
            <h3 class="font-bold text-loot-ink">Recent withdrawals</h3>
            <a href="{{ route('admin.withdrawals') }}" class="text-xs font-semibold text-loot-primary hover:underline">All →</a>
        </div>
        <div class="divide-y divide-loot-border">
            @forelse($recentPayouts as $w)
                <div class="flex items-center gap-3 px-5 py-3">
                    <img src="{{ $w->user?->avatar() }}" class="w-9 h-9 rounded-full object-cover" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-loot-ink truncate">{{ $w->user?->username ?? 'Unknown' }}</p>
                        <p class="text-xs text-loot-muted truncate">{{ $w->method }} · {{ $w->account }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-loot-ink">{{ number_format($w->amount) }} pts</p>
                        @php
                            $tone = match($w->status){
                                'pending' => 'bg-amber-50 text-loot-accentDark',
                                'approved','paid','completed' => 'bg-emerald-50 text-loot-primary',
                                'rejected' => 'bg-rose-50 text-rose-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="inline-block mt-0.5 text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full {{ $tone }}">{{ $w->status }}</span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-loot-muted text-center">No withdrawals yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
