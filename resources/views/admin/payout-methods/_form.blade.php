@php
    $isEdit = $method->exists;
    $action = $isEdit ? route('admin.payout-methods.update', $method->id) : route('admin.payout-methods.store');
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
        <h3 class="font-bold text-loot-ink mb-4">Method details</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Name *</label>
                <input type="text" name="name" required value="{{ old('name', $method->name) }}" placeholder="PayPal, DANA, USDT…" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Currency *</label>
                <input type="text" name="currency" required value="{{ old('currency', $method->currency ?: 'USD') }}" maxlength="8" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm font-mono uppercase">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Description</label>
                <input type="text" name="description" value="{{ old('description', $method->description) }}" placeholder="Short blurb shown to user" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Account label *</label>
                <input type="text" name="account_label" value="{{ old('account_label', $method->account_label) }}" placeholder="PayPal Email / Wallet Address / Phone…" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Sort order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $method->sort_order ?? 0) }}" min="0" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">User-facing instructions</label>
                <textarea name="instructions" rows="2" placeholder="Make sure your PayPal account can receive payments…" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">{{ old('instructions', $method->instructions) }}</textarea>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
        <h3 class="font-bold text-loot-ink mb-4">Limits & fees</h3>
        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Min withdrawal (pts)</label>
                <input type="number" step="0.01" min="0" name="min_withdrawal" value="{{ old('min_withdrawal', $method->min_withdrawal ?? 0) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Fee %</label>
                <input type="number" step="0.01" min="0" max="100" name="fee_percentage" value="{{ old('fee_percentage', $method->fee_percentage ?? 0) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Fixed fee</label>
                <input type="number" step="0.01" min="0" name="fixed_fee" value="{{ old('fixed_fee', $method->fixed_fee ?? 0) }}" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6">
        <h3 class="font-bold text-loot-ink mb-4">Appearance & status</h3>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Logo (PNG / JPG, max 2MB)</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm">
                @if($method->logoUrl())
                    <div class="mt-2 flex items-center gap-3">
                        <img src="{{ $method->logoUrl() }}" alt="" class="w-12 h-12 object-cover rounded-lg border border-loot-border">
                        <span class="text-xs text-loot-muted">Current logo</span>
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Status</label>
                <select name="is_active" class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                    <option value="1" @selected(old('is_active', $method->is_active))>Active</option>
                    <option value="0" @selected(!old('is_active', $method->is_active))>Disabled</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.payout-methods') }}" class="text-sm font-bold text-loot-muted hover:text-loot-ink">Cancel</a>
        <button type="submit" class="px-6 py-3 rounded-xl gradient-loot text-white font-bold shadow-cardLg hover:opacity-90">{{ $isEdit ? 'Save changes' : 'Create method' }}</button>
    </div>
</form>
