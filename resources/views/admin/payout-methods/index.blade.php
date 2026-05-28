@extends('layouts.admin-modern')
@section('title', 'Payout Methods')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Payout Methods</h1>
        <p class="text-sm text-loot-muted">{{ $methods->count() }} method(s). Active methods appear on the user wallet.</p>
    </div>
    <a href="{{ route('admin.payout-methods.create') }}" class="px-4 py-2 rounded-xl gradient-loot text-white text-sm font-bold shadow-soft hover:opacity-90">+ Add Method</a>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
@endif

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">Method</th>
                    <th class="px-5 py-3 font-semibold">Currency</th>
                    <th class="px-5 py-3 font-semibold text-right">Min withdraw</th>
                    <th class="px-5 py-3 font-semibold text-right">Fees</th>
                    <th class="px-5 py-3 font-semibold text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($methods as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($m->logoUrl())
                                    <img src="{{ $m->logoUrl() }}" alt="{{ $m->name }}" class="w-10 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-10 h-10 rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-xs">{{ $m->initials() }}</div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-loot-ink truncate">{{ $m->name }}</p>
                                    <p class="text-[11px] text-loot-muted truncate">{{ $m->account_label ?: '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-mono text-loot-ink/80">{{ $m->currency }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-ink">{{ number_format((float)($m->min_withdrawal ?? 0), 0) }} pts</td>
                        <td class="px-5 py-3 text-right text-loot-ink/80">{{ number_format((float)($m->fee_percentage ?? 0), 2) }}% + {{ number_format((float)($m->fixed_fee ?? 0), 2) }}</td>
                        <td class="px-5 py-3 text-center">
                            <form method="POST" action="{{ route('admin.payout-methods.toggle', $m->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold uppercase px-2 py-1 rounded-full hover:opacity-80 {{ $m->is_active ? 'bg-emerald-50 text-loot-primary' : 'bg-gray-100 text-loot-muted' }}">{{ $m->is_active ? 'active' : 'disabled' }}</button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.payout-methods.edit', $m->id) }}" class="text-xs font-bold text-loot-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.payout-methods.destroy', $m->id) }}" class="inline ml-2" onsubmit="return confirm('Delete {{ $m->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-16 text-center text-loot-muted">No payout methods yet. <a href="{{ route('admin.payout-methods.create') }}" class="font-bold text-loot-primary">Add the first one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
