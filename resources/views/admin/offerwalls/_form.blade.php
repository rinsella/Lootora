@php
    $isEdit = $provider->exists;
    $action = $isEdit ? route('admin.offerwalls.update', $provider->id) : route('admin.offerwalls.store');
@endphp

@if($errors->any())
    <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 mb-4 text-sm">
        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
        <h3 class="font-bold text-loot-ink mb-4">Basics</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Name *</label>
                <input type="text" name="name" required value="{{ old('name', $provider->name) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Slug (auto if empty)</label>
                <input type="text" name="slug" value="{{ old('slug', $provider->slug) }}" placeholder="cpx-research" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">{{ old('description', $provider->description) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Category</label>
                <input type="text" name="category" value="{{ old('category', $provider->category) }}" placeholder="surveys, offers, video, app installs" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Payout type</label>
                <input type="text" name="payout_type" value="{{ old('payout_type', $provider->payout_type) }}" placeholder="CPA, CPI, CPL" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $provider->sort_order ?? 0) }}" min="0" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Status</label>
                <select name="is_active" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                    <option value="1" @selected(old('is_active', $provider->is_active))>Active</option>
                    <option value="0" @selected(!old('is_active', $provider->is_active))>Disabled</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Logo (PNG / JPG, max 2MB)</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm">
                @if($provider->logoUrl())
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ $provider->logoUrl() }}" alt="" class="w-12 h-12 object-cover rounded-lg border border-loot-border">
                        <span class="text-xs text-loot-muted">Current logo</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
        <h3 class="font-bold text-loot-ink mb-1">Integration</h3>
        <p class="text-xs text-loot-muted mb-4">Use placeholders <code>{user_id}</code>, <code>{username}</code>, <code>{email}</code>, <code>{country}</code>, <code>{ip}</code> in the iframe URL.</p>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">iframe URL template</label>
                <input type="text" name="iframe_url_template" value="{{ old('iframe_url_template', $provider->iframe_url_template ?? $provider->iframe_url) }}" placeholder="https://offers.example.com/?u={user_id}&ip={ip}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-loot-ink mb-1.5">API key</label>
                    <input type="text" name="api_key" value="{{ old('api_key', $provider->api_key) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-loot-ink mb-1.5">Secret key</label>
                    <input type="text" name="secret_key" value="{{ old('secret_key', $provider->secret_key) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
        <h3 class="font-bold text-loot-ink mb-1">Postback & security</h3>
        <p class="text-xs text-loot-muted mb-4">Set this URL in your provider's dashboard.</p>
        @if($isEdit && $provider->slug)
            <div class="mb-4 rounded-xl bg-gray-50 border border-loot-border px-4 py-3">
                <p class="text-[10px] uppercase tracking-wider text-loot-muted font-semibold">Postback URL</p>
                <p class="text-sm font-mono text-loot-ink break-all">{{ url('/api/postback/'.$provider->slug) }}</p>
            </div>
        @endif
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Postback secret (HMAC)</label>
                <input type="text" name="postback_secret" value="{{ old('postback_secret', $provider->postback_secret) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">IP whitelist (comma-separated)</label>
                <input type="text" name="ip_whitelist" value="{{ old('ip_whitelist', $provider->ip_whitelist) }}" placeholder="1.2.3.4, 5.6.7.0/24" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Revenue share % (paid to user)</label>
                <input type="number" step="0.01" min="0" max="100" name="revenue_share_percentage" value="{{ old('revenue_share_percentage', $provider->revenue_share_percentage ?? 70) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                <p class="mt-1 text-[11px] text-loot-muted">User gets this %; platform keeps the rest.</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.offerwalls') }}" class="text-sm font-bold text-loot-muted hover:text-loot-ink">Cancel</a>
        <button type="submit" class="px-6 py-3 rounded-xl gradient-loot text-white font-bold shadow-cardLg hover:opacity-90">{{ $isEdit ? 'Save changes' : 'Create provider' }}</button>
    </div>
</form>
