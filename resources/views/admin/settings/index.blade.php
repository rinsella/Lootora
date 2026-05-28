@extends('layouts.admin-modern')
@section('title', 'Site Settings')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Site Settings</h1>
        <p class="text-sm text-loot-muted">Global configuration for branding, currency, rewards and system.</p>
    </div>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
@endif

@php
    $groups = collect($schema)->groupBy('group');
    $groupTitles = ['brand' => 'Brand', 'currency' => 'Currency', 'rewards' => 'Rewards', 'system' => 'System'];
@endphp

<form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
    @csrf

    @foreach($groups as $groupKey => $rows)
        <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
            <h3 class="font-bold text-loot-ink mb-4">{{ $groupTitles[$groupKey] ?? ucfirst($groupKey) }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($rows as $row)
                    @php $val = $values[$row['key']] ?? $row['default']; @endphp
                    <div>
                        <label class="block text-xs font-semibold text-loot-ink mb-1.5">{{ $row['label'] }}</label>
                        @if($row['type'] === 'bool')
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="{{ $row['key'] }}" value="0">
                                <input type="checkbox" name="{{ $row['key'] }}" value="1" class="w-5 h-5 rounded text-loot-primary focus:ring-loot-primary" @checked($val)>
                                <span class="text-sm text-loot-ink">Enabled</span>
                            </label>
                        @elseif($row['type'] === 'int' || $row['type'] === 'float')
                            <input type="number" step="{{ $row['type'] === 'float' ? '0.01' : '1' }}" name="{{ $row['key'] }}" value="{{ $val }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                        @else
                            <input type="text" name="{{ $row['key'] }}" value="{{ $val }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                        @endif
                        <p class="text-[11px] text-loot-muted mt-1 font-mono">{{ $row['key'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-3 rounded-xl gradient-loot text-white font-bold shadow-cardLg hover:opacity-90">Save settings</button>
    </div>
</form>

@endsection
