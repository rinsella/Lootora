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

{{-- ===== STEP-BY-STEP SETUP ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 mt-6">
    <h3 class="font-bold text-loot-ink mb-3">Step-by-step provider setup</h3>
    <ol class="text-sm text-loot-ink space-y-2 list-decimal pl-5">
        <li>Register your Lootora site on the provider's dashboard. Use your real domain (e.g. <code>https://lootora.net</code>).</li>
        <li>Copy the provider's <strong>App ID / Site ID</strong> and any required <strong>secret key</strong>.</li>
        <li>In Lootora go to <a href="{{ route('admin.offerwalls.create') }}" class="text-loot-primary font-bold">Providers → Add provider</a>. Fill name, slug, app id, postback secret, and revenue share %.</li>
        <li>Set the <strong>iframe URL template</strong> — replace any placeholder with <code>{user_id}</code>.</li>
        <li>Copy your Lootora <strong>postback URL</strong> from the provider page and paste it into the provider's S2S postback settings.</li>
        <li>Save and toggle the provider <strong>Active</strong>.</li>
        <li>Test by completing one small offer; check <a href="{{ route('admin.postback-logs') }}" class="text-loot-primary font-bold">Postback Logs</a> for the request and <a href="{{ route('admin.users') }}" class="text-loot-primary font-bold">Users</a> for the credit.</li>
    </ol>
</div>

{{-- ===== URL TEMPLATE EXAMPLES ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 mt-6">
    <h3 class="font-bold text-loot-ink mb-3">Iframe URL template examples</h3>
    <div class="space-y-2 text-xs font-mono">
        <div class="bg-gray-50 rounded-lg p-3"><span class="text-loot-muted">CPX Research:</span><br><code class="text-loot-ink break-all">https://offers.cpx-research.com/index.php?app_id=YOUR_APP_ID&amp;ext_user_id={user_id}</code></div>
        <div class="bg-gray-50 rounded-lg p-3"><span class="text-loot-muted">BitLabs:</span><br><code class="text-loot-ink break-all">https://web.bitlabs.ai/?uid={user_id}&amp;token=YOUR_TOKEN</code></div>
        <div class="bg-gray-50 rounded-lg p-3"><span class="text-loot-muted">AdGate Rewards:</span><br><code class="text-loot-ink break-all">https://wall.adgaterewards.com/YOUR_WALL_ID/{user_id}</code></div>
        <div class="bg-gray-50 rounded-lg p-3"><span class="text-loot-muted">Pollfish:</span><br><code class="text-loot-ink break-all">https://surveys.pollfish.com/?api_key=YOUR_KEY&amp;reward_name=points&amp;user_id={user_id}</code></div>
        <div class="bg-gray-50 rounded-lg p-3"><span class="text-loot-muted">Generic / Custom:</span><br><code class="text-loot-ink break-all">https://provider.example.com/wall?pub=YOUR_ID&amp;sub1={user_id}&amp;currency=POINTS</code></div>
    </div>
</div>

{{-- ===== POSTBACK TEST ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 mt-6">
    <h3 class="font-bold text-loot-ink mb-3">Test postback with curl</h3>
    <p class="text-sm text-loot-muted mb-2">Replace <code>example-provider</code> with your provider slug:</p>
    <pre class="bg-gray-900 text-emerald-200 text-xs rounded-lg p-3 overflow-x-auto"><code>curl -i "{{ config('app.url') }}/postback/example-provider?user_id=1&amp;transaction_id=test-{{ time() }}&amp;amount=100&amp;currency=POINTS"</code></pre>
    <p class="text-xs text-loot-muted mt-2">A successful test returns HTTP 200 and a new row in <a href="{{ route('admin.postback-logs') }}" class="text-loot-primary font-bold">Postback Logs</a> with status <code>credited</code>.</p>
</div>

{{-- ===== TROUBLESHOOTING ===== --}}
<div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 mt-6">
    <h3 class="font-bold text-loot-ink mb-3">Troubleshooting</h3>
    <div class="text-sm space-y-3">
        <div><p class="font-bold text-loot-ink">User not credited</p><p class="text-loot-muted text-xs">Confirm the postback arrived in Postback Logs. Verify <code>user_id</code> matches an existing user. Check the provider is <strong>active</strong> and revenue share &gt; 0.</p></div>
        <div><p class="font-bold text-loot-ink">Duplicate transaction</p><p class="text-loot-muted text-xs">Same <code>transaction_id</code> arrived twice — this is by design. The first credit wins; the second is logged as <code>duplicate</code>.</p></div>
        <div><p class="font-bold text-loot-ink">Invalid signature</p><p class="text-loot-muted text-xs">Provider's <code>verifier/hash/sig</code> didn't match expected MD5/SHA. Recheck the <strong>postback secret</strong> on the provider's dashboard and in Lootora.</p></div>
        <div><p class="font-bold text-loot-ink">Missing user_id</p><p class="text-loot-muted text-xs">Your iframe URL template is missing <code>{user_id}</code>. Edit the provider and save.</p></div>
        <div><p class="font-bold text-loot-ink">Provider IP blocked</p><p class="text-loot-muted text-xs">If using Cloudflare or a WAF, allow the provider's documented IP ranges through.</p></div>
        <div><p class="font-bold text-loot-ink">No postback received at all</p><p class="text-loot-muted text-xs">Confirm your domain is reachable on HTTPS. Try the curl test above from a public terminal. Check provider's S2S URL configuration.</p></div>
        <div><p class="font-bold text-loot-ink">Wrong reward amount</p><p class="text-loot-muted text-xs">Lootora applies <code>revenue_share_percentage</code> to the provider's payout USD, then converts via <code>LOOT_USD_TO_POINTS</code>. Tune both in the provider settings and <a href="{{ route('admin.settings') }}" class="text-loot-primary font-bold">Site Settings</a>.</p></div>
    </div>
</div>

{{-- ===== SECURITY ===== --}}
<div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 mt-6">
    <h3 class="font-bold text-amber-900 mb-2">⚠ Security reminders</h3>
    <ul class="text-sm text-amber-900 list-disc pl-5 space-y-1">
        <li>Never commit <code>.env</code> or postback secrets to git or Docker Hub.</li>
        <li>Always use HTTPS for postback URLs in production.</li>
        <li>Whitelist provider IPs at the WAF level when supported.</li>
        <li>Keep <code>APP_DEBUG=false</code> in production to avoid leaking stack traces.</li>
        <li>Rotate postback secrets immediately if logs show unverified hits.</li>
    </ul>
</div>

@endsection
