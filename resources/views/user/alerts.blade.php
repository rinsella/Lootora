@extends('layouts.member')

@section('title', 'Alerts')

@section('content')

<div class="rounded-2xl bg-white border border-loot-border p-5 sm:p-6 shadow-soft mb-5 flex items-start gap-4">
    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-loot-primary grid place-items-center text-2xl">🔔</div>
    <div>
        <h1 class="text-xl font-extrabold text-loot-ink">Notifications</h1>
        <p class="text-sm text-loot-muted mt-1">Updates about your missions, payouts, bonuses and account.</p>
    </div>
</div>

@if($notifications->isEmpty())
    <x-empty-state icon="📭" title="You're all caught up" desc="No new notifications right now. We'll let you know when something happens.">
        <a href="{{ route('user.earn') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-loot-primary text-white font-semibold text-sm hover:bg-loot-primaryDark">Browse offers →</a>
    </x-empty-state>
@else
    <ul class="space-y-3">
        @foreach($notifications as $n)
            @php
                $isRead = (bool)($n->is_read ?? false);
            @endphp
            <li class="rounded-2xl bg-white border border-loot-border p-4 sm:p-5 shadow-soft flex items-start gap-4 {{ $isRead ? '' : 'ring-1 ring-loot-primary/30' }}">
                <div class="w-10 h-10 rounded-xl {{ $isRead ? 'bg-gray-100 text-loot-muted' : 'bg-emerald-50 text-loot-primary' }} grid place-items-center">●</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-loot-ink">{{ $n->title ?? 'Notification' }}</h3>
                        @if(!$isRead)
                            <x-status-badge variant="success" icon="●">New</x-status-badge>
                        @endif
                    </div>
                    <p class="text-sm text-loot-muted mt-1">{{ $n->message ?? $n->body ?? '' }}</p>
                    <p class="text-[11px] text-loot-muted mt-2">{{ optional($n->created_at)->diffForHumans() }}</p>
                </div>
            </li>
        @endforeach
    </ul>
@endif

@endsection
