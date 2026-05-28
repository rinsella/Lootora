@extends('layouts.admin-modern')
@section('title', 'Offerwall Providers')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Offerwall Providers</h1>
        <p class="text-sm text-loot-muted">{{ $providers->count() }} provider(s) configured.</p>
    </div>
    <a href="{{ route('admin.offerwalls.create') }}" class="px-4 py-2 rounded-xl gradient-loot text-white text-sm font-bold shadow-soft hover:opacity-90">+ Add Provider</a>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
@endif

<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">Provider</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold text-right">Rev share</th>
                    <th class="px-5 py-3 font-semibold text-center">Postback</th>
                    <th class="px-5 py-3 font-semibold text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($providers as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($p->logoUrl())
                                    <img src="{{ $p->logoUrl() }}" alt="{{ $p->name }}" class="w-10 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-10 h-10 rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-xs">{{ $p->initials() }}</div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-loot-ink truncate">{{ $p->name }}</p>
                                    <p class="text-[11px] text-loot-muted font-mono">{{ $p->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-loot-ink/80">{{ $p->category ?: '—' }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-ink">{{ number_format((float)($p->revenue_share_percentage ?? 70), 1) }}%</td>
                        <td class="px-5 py-3 text-center">
                            @if($p->postback_secret)
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-emerald-50 text-loot-primary">configured</span>
                            @else
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-amber-50 text-loot-accentDark">no secret</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <form method="POST" action="{{ route('admin.offerwalls.toggle', $p->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold uppercase px-2 py-1 rounded-full hover:opacity-80 {{ $p->is_active ? 'bg-emerald-50 text-loot-primary' : 'bg-gray-100 text-loot-muted' }}">{{ $p->is_active ? 'active' : 'disabled' }}</button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.offerwalls.edit', $p->id) }}" class="text-xs font-bold text-loot-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.offerwalls.destroy', $p->id) }}" class="inline ml-2" onsubmit="return confirm('Delete {{ $p->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-16 text-center text-loot-muted">No providers yet. <a href="{{ route('admin.offerwalls.create') }}" class="font-bold text-loot-primary">Add the first one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
