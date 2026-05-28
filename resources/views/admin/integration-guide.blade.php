@extends('layouts.admin-modern')
@section('title', 'Integration Guide')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-loot-ink">Integration Guide</h1>
    <p class="text-sm text-loot-muted">How to wire up offerwall providers (CPX Research, BitLabs, AdGate, AdGem, OfferToro, etc.).</p>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
    <h3 class="font-bold text-loot-ink mb-2">1. Iframe URL placeholders</h3>
    <p class="text-sm text-loot-muted mb-3">When building your provider's iframe URL template, use these placeholders. They are replaced at runtime with the active user's data.</p>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase text-loot-muted">
                    <th class="px-3 py-2">Placeholder</th>
                    <th class="px-3 py-2">Replaced with</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border font-mono text-xs">
                @foreach([
                    '{user_id}' => 'Authenticated user ID (numeric, primary key)',
                    '{username}' => 'User login name',
                    '{email}' => 'User email address',
                    '{country}' => 'User country code (ISO-2)',
                    '{ip}' => 'User current IP address',
                ] as $k => $v)
                    <tr><td class="px-3 py-2 font-bold">{{ $k }}</td><td class="px-3 py-2">{{ $v }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
    <h3 class="font-bold text-loot-ink mb-2">2. Postback endpoint</h3>
    <p class="text-sm text-loot-muted mb-3">All providers post conversions to a single endpoint. Each provider has its own slug-based path.</p>
    <div class="rounded-xl bg-gray-50 border border-loot-border px-4 py-3 mb-3">
        <p class="text-[10px] uppercase tracking-wider text-loot-muted font-semibold">Generic format</p>
        <p class="text-sm font-mono text-loot-ink break-all">{{ url('/api/postback/{provider_slug}') }}</p>
    </div>
    <p class="text-sm text-loot-muted mb-2">Common parameters our normalizer understands (any name works for most):</p>
    <ul class="text-xs text-loot-muted list-disc pl-5 space-y-1">
        <li><code>user_id</code> / <code>sub_id</code> / <code>uid</code> — your user ID we passed in iframe</li>
        <li><code>transaction_id</code> / <code>trans_id</code> / <code>txid</code> — unique conversion ID (for deduplication)</li>
        <li><code>offer_id</code> / <code>offer_name</code> — what the user completed</li>
        <li><code>amount</code> / <code>payout</code> / <code>revenue</code> — payout to us in USD</li>
        <li><code>ip</code> / <code>country</code> — optional context for fraud checks</li>
        <li><code>hash</code> / <code>signature</code> — HMAC signature when postback_secret is set</li>
    </ul>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
    <h3 class="font-bold text-loot-ink mb-3">3. Your configured providers ({{ $providers->count() }})</h3>
    @if($providers->isEmpty())
        <p class="text-sm text-loot-muted">No providers yet. <a href="{{ route('admin.offerwalls.create') }}" class="font-bold text-loot-primary hover:underline">Add the first one →</a></p>
    @else
        <div class="space-y-3">
            @foreach($providers as $p)
                <div class="rounded-xl border border-loot-border p-4 flex items-start gap-4">
                    @if($p->logoUrl())
                        <img src="{{ $p->logoUrl() }}" alt="" class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-xs flex-shrink-0">{{ $p->initials() }}</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-bold text-loot-ink">{{ $p->name }}</p>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $p->is_active ? 'bg-emerald-50 text-loot-primary' : 'bg-gray-100 text-loot-muted' }}">{{ $p->is_active ? 'active' : 'inactive' }}</span>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $p->postback_secret ? 'bg-emerald-50 text-loot-primary' : 'bg-amber-50 text-loot-accentDark' }}">{{ $p->postback_secret ? 'signed' : 'no secret' }}</span>
                        </div>
                        <p class="text-[11px] text-loot-muted font-mono mt-0.5">{{ $p->slug }}</p>
                        <div class="mt-2 rounded-lg bg-gray-50 border border-loot-border px-3 py-2">
                            <p class="text-[10px] uppercase tracking-wider text-loot-muted font-semibold">Postback URL</p>
                            <p class="text-xs font-mono text-loot-ink break-all">{{ url('/api/postback/'.$p->slug) }}</p>
                        </div>
                        @if($p->iframe_url_template)
                            <div class="mt-2 rounded-lg bg-gray-50 border border-loot-border px-3 py-2">
                                <p class="text-[10px] uppercase tracking-wider text-loot-muted font-semibold">Iframe URL template</p>
                                <p class="text-xs font-mono text-loot-ink break-all">{{ $p->iframe_url_template }}</p>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('admin.offerwalls.edit', $p->id) }}" class="text-xs font-bold text-loot-primary hover:underline flex-shrink-0">Edit →</a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
    <h3 class="font-bold text-loot-ink mb-3">4. Security tips</h3>
    <ul class="text-sm text-loot-muted list-disc pl-5 space-y-1.5">
        <li>Always set a unique <strong>Postback secret</strong> per provider to enable HMAC signature validation.</li>
        <li>Use the <strong>IP whitelist</strong> field with the provider's official postback IPs whenever possible.</li>
        <li>Set a sensible <strong>revenue share %</strong> — anything above 90% leaves little margin.</li>
        <li>Monitor <a href="{{ route('admin.postback-logs') }}" class="font-bold text-loot-primary hover:underline">postback logs</a> for rejected/duplicate/error entries after going live.</li>
        <li>Review the <a href="{{ route('admin.fraud-logs') }}" class="font-bold text-loot-primary hover:underline">fraud logs</a> regularly and ban or suspend abusive accounts.</li>
    </ul>
</div>

<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
    <h3 class="font-bold text-loot-ink mb-3">5. Quick provider notes</h3>
    <div class="grid sm:grid-cols-2 gap-4 text-sm">
        <div>
            <p class="font-bold text-loot-ink">CPX Research</p>
            <p class="text-loot-muted text-xs">Use <code>ext_user_id={user_id}</code> in iframe. Postback params: <code>trans_id</code>, <code>amount_local</code>, <code>amount_usd</code>, <code>hash</code>.</p>
        </div>
        <div>
            <p class="font-bold text-loot-ink">BitLabs</p>
            <p class="text-loot-muted text-xs">Use <code>uid={user_id}</code>. Postback params: <code>tx</code>, <code>val</code>, <code>raw</code>, <code>hash</code> (SHA1).</p>
        </div>
        <div>
            <p class="font-bold text-loot-ink">AdGate Media</p>
            <p class="text-loot-muted text-xs">Use <code>s1={user_id}</code>. Postback params: <code>g={transaction_id}</code>, <code>p={payout}</code>.</p>
        </div>
        <div>
            <p class="font-bold text-loot-ink">AdGem</p>
            <p class="text-loot-muted text-xs">Use <code>player_id={user_id}</code>. Postback params: <code>transaction_id</code>, <code>amount</code>, <code>verifier</code>.</p>
        </div>
        <div>
            <p class="font-bold text-loot-ink">OfferToro</p>
            <p class="text-loot-muted text-xs">Use <code>id={user_id}</code>. Postback params: <code>oid</code>, <code>amount</code>, <code>sig</code> (MD5).</p>
        </div>
        <div>
            <p class="font-bold text-loot-ink">Generic / Custom</p>
            <p class="text-loot-muted text-xs">Our normalizer accepts most common parameter names. Use the <a href="{{ route('admin.postback-logs') }}" class="text-loot-primary font-bold">postback logs</a> to verify each integration.</p>
        </div>
    </div>
</div>

@endsection
