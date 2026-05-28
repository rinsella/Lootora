@extends('layouts.admin-modern')
@section('title', 'Postback Logs')

@section('content')

<div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-extrabold text-loot-ink">Postback Logs</h1>
        <p class="text-sm text-loot-muted">Every incoming offerwall postback request. Use to debug integrations.</p>
    </div>
</div>

@if(!$tableExists)
    <div class="rounded-2xl bg-white border border-loot-border p-10 text-center">
        <p class="font-bold text-loot-ink">Postback logs table is missing</p>
        <p class="text-sm text-loot-muted mt-1">Run database migrations to enable this feature.</p>
    </div>
@else
    <form method="GET" action="{{ route('admin.postback-logs') }}" class="rounded-2xl bg-white border border-loot-border shadow-soft p-4 mb-4 grid sm:grid-cols-4 gap-3">
        <select name="provider" class="rounded-xl border border-loot-border px-3 py-2 text-sm">
            <option value="">All providers</option>
            @foreach($providers as $p)
                <option value="{{ $p }}" @selected($filters['provider']===$p)>{{ $p }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border border-loot-border px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(['received','accepted','duplicate','rejected','error'] as $s)
                <option value="{{ $s }}" @selected($filters['status']===$s)>{{ $s }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ $filters['date'] }}" class="rounded-xl border border-loot-border px-3 py-2 text-sm">
        <div class="flex gap-2">
            <input type="text" name="transaction_id" value="{{ $filters['transaction_id'] }}" placeholder="Transaction ID" class="flex-1 rounded-xl border border-loot-border px-3 py-2 text-sm">
            <button class="px-3 py-2 rounded-xl bg-loot-ink text-white text-xs font-bold">Filter</button>
        </div>
    </form>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                        <th class="px-4 py-3 font-semibold">Time</th>
                        <th class="px-4 py-3 font-semibold">Provider</th>
                        <th class="px-4 py-3 font-semibold">User</th>
                        <th class="px-4 py-3 font-semibold">Transaction</th>
                        <th class="px-4 py-3 font-semibold">Offer</th>
                        <th class="px-4 py-3 font-semibold text-right">Amount</th>
                        <th class="px-4 py-3 font-semibold text-right">Payout $</th>
                        <th class="px-4 py-3 font-semibold text-center">Sig</th>
                        <th class="px-4 py-3 font-semibold text-center">Status</th>
                        <th class="px-4 py-3 font-semibold">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-loot-border">
                    @forelse($logs as $log)
                        @php
                            $tone = match($log->status){
                                'accepted' => 'bg-emerald-50 text-loot-primary',
                                'duplicate' => 'bg-amber-50 text-loot-accentDark',
                                'rejected','error' => 'bg-rose-50 text-rose-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-4 py-3 text-xs text-loot-muted whitespace-nowrap">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $log->provider }}</td>
                            <td class="px-4 py-3">{{ $log->user_id ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] break-all max-w-[12rem]">{{ $log->transaction_id }}</td>
                            <td class="px-4 py-3 text-xs">{{ $log->offer_name ?? $log->offer_id ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format((float)($log->amount ?? 0), 2) }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format((float)($log->payout ?? 0), 4) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded-full {{ $log->signature_valid ? 'bg-emerald-50 text-loot-primary' : 'bg-rose-50 text-rose-600' }}">{{ $log->signature_valid ? 'ok' : 'bad' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center"><span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $tone }}">{{ $log->status }}</span></td>
                            <td class="px-4 py-3 text-xs text-loot-muted">{{ $log->ip_address }}{{ $log->country ? ' · '.$log->country : '' }}</td>
                        </tr>
                        @if($log->error_message || $log->raw_payload)
                            <tr class="bg-gray-50/40">
                                <td colspan="10" class="px-4 pb-3 text-[11px] text-loot-muted">
                                    @if($log->error_message)<p class="text-rose-700 font-semibold">⚠ {{ $log->error_message }}</p>@endif
                                    @if($log->raw_payload)
                                        <details class="mt-1">
                                            <summary class="cursor-pointer font-bold">raw payload</summary>
                                            <pre class="mt-1 p-2 bg-white border border-loot-border rounded overflow-x-auto whitespace-pre-wrap break-all">{{ is_string($log->raw_payload) ? $log->raw_payload : json_encode($log->raw_payload, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="10" class="px-4 py-16 text-center text-loot-muted">No postbacks logged yet.</td></tr>
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
