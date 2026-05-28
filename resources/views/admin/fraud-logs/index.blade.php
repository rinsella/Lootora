@extends('layouts.admin-modern')
@section('title', 'Fraud Logs')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Fraud Logs</h1>
        <p class="text-sm text-loot-muted">Suspicious activity flagged by the system.</p>
    </div>
</div>

@if(!$tableExists)
    <div class="rounded-2xl bg-white border border-loot-border p-10 text-center">
        <p class="font-bold text-loot-ink">Fraud logs table is missing</p>
        <p class="text-sm text-loot-muted mt-1">Run database migrations to enable this feature.</p>
    </div>
@else
    <form method="GET" action="{{ route('admin.fraud-logs') }}" class="rounded-2xl bg-white border border-loot-border shadow-soft p-4 mb-4 grid sm:grid-cols-3 gap-3">
        <select name="type" class="rounded-xl border border-loot-border px-3 py-2 text-sm">
            <option value="">All types</option>
            @foreach($types as $t)
                <option value="{{ $t }}" @selected($filters['type']===$t)>{{ $t }}</option>
            @endforeach
        </select>
        <input type="text" name="user" value="{{ $filters['user'] ?? '' }}" placeholder="Username / email" class="rounded-xl border border-loot-border px-3 py-2 text-sm">
        <button class="px-3 py-2 rounded-xl bg-loot-ink text-white text-xs font-bold">Filter</button>
    </form>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                        <th class="px-4 py-3 font-semibold">Time</th>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold">Type</th>
                        <th class="px-4 py-3 font-semibold text-center">Risk</th>
                        <th class="px-4 py-3 font-semibold">Message</th>
                        <th class="px-4 py-3 font-semibold">IP / Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-loot-border">
                    @forelse($logs as $log)
                        @php $user = $users[$log->user_id] ?? null; @endphp
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-4 py-3 text-xs text-loot-muted whitespace-nowrap">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3">
                                @if($user)
                                    <p class="font-bold text-loot-ink">{{ $user->username }}</p>
                                    <p class="text-[11px] text-loot-muted">{{ $user->email }}</p>
                                @else
                                    <span class="text-loot-muted">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $log->type }}</td>
                            <td class="px-4 py-3 text-center">
                                @php $risk = (int) ($log->risk_score ?? 0); $tone = $risk >= 70 ? 'bg-rose-100 text-rose-700' : ($risk >= 40 ? 'bg-amber-100 text-loot-accentDark' : 'bg-gray-100 text-loot-muted'); @endphp
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $tone }}">{{ $risk }}</span>
                            </td>
                            <td class="px-4 py-3 text-loot-ink/80 max-w-md">{{ $log->message }}</td>
                            <td class="px-4 py-3 text-xs text-loot-muted">
                                <p class="font-mono">{{ $log->ip_address }}</p>
                                <p class="truncate max-w-[20rem]" title="{{ $log->user_agent }}">{{ \Illuminate\Support\Str::limit($log->user_agent, 50) }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-16 text-center text-loot-muted">No fraud events logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs && $logs->hasPages())
            <div class="px-4 py-3 border-t border-loot-border">{{ $logs->links() }}</div>
        @endif
    </div>
@endif

@endsection
