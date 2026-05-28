@extends('layouts.member')

@section('title', 'Wallet')

@php use App\Support\Lootora; @endphp

@section('content')

<div class="rounded-2xl gradient-loot p-6 sm:p-8 text-white shadow-cardLg mb-6 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-white/10"></div>
    <div class="relative z-10">
        <p class="text-xs uppercase tracking-wider text-emerald-100 font-semibold">Wallet balance</p>
        <div class="mt-2 flex items-end gap-3 flex-wrap">
            <p class="text-4xl sm:text-5xl font-extrabold leading-none">{{ Lootora::fmtPoints($user->current_points ?? 0) }}</p>
            <p class="text-sm font-bold opacity-90 pb-1">$LOOT</p>
            <p class="text-sm text-emerald-100 pb-1">≈ ${{ Lootora::fmtUsd($usdEquivalent) }} USD</p>
        </div>
        <p class="mt-3 text-xs text-emerald-100">Global minimum withdrawal: <span class="font-bold text-white">{{ Lootora::fmtPoints($minWithdrawal) }} $LOOT</span></p>

        @php
            $pct = $minWithdrawal > 0 ? min(100, ((float)($user->current_points ?? 0) / $minWithdrawal) * 100) : 100;
        @endphp
        <div class="mt-4 h-2 w-full rounded-full bg-white/20 overflow-hidden">
            <div class="h-full bg-white" style="width: {{ $pct }}%"></div>
        </div>
        <p class="mt-2 text-xs text-emerald-100">{{ number_format($pct, 0) }}% to next payout</p>
    </div>
</div>

@if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 text-loot-primaryDark px-4 py-3 mb-4 text-sm font-semibold">✓ {{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 mb-4 text-sm">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
@endif

@if($methods->isEmpty())
    <div class="rounded-2xl bg-white border border-loot-border p-8 text-center mb-6">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-loot-accentDark grid place-items-center text-2xl">⏳</div>
        <p class="mt-3 font-bold text-loot-ink">No payout methods available yet</p>
        <p class="text-sm text-loot-muted">The admin hasn't activated any payout methods. Please check back soon.</p>
    </div>
@else
    <form method="POST" action="{{ route('user.wallet.withdraw') }}" class="rounded-2xl bg-white border border-loot-border shadow-soft p-5 sm:p-6 mb-6">
        @csrf

        <h2 class="text-lg font-bold text-loot-ink mb-1">Request a withdrawal</h2>
        <p class="text-sm text-loot-muted mb-5">Pick a payout method, enter your account details, and submit. Your request will be reviewed by the admin.</p>

        <p class="text-xs font-semibold text-loot-ink mb-2">Payout method</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-5">
            @foreach($methods as $m)
                @php
                    $logo = $m->logoUrl();
                    $minM = max((float) ($m->min_withdrawal ?? 0), (float) $minWithdrawal);
                @endphp
                <label class="cursor-pointer">
                    <input type="radio" name="method_id" value="{{ $m->id }}" class="peer sr-only" {{ old('method_id') == $m->id ? 'checked' : ($loop->first && !old('method_id') ? 'checked' : '') }}
                           data-min="{{ $minM }}" data-label="{{ $m->account_label ?: 'Account / Address' }}" data-name="{{ $m->name }}" data-instructions="{{ $m->instructions ? e($m->instructions) : '' }}">
                    <div class="rounded-2xl border-2 border-loot-border p-3 text-center transition peer-checked:border-loot-primary peer-checked:bg-emerald-50/50 hover:border-loot-primary/50">
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ $m->name }}" class="w-10 h-10 mx-auto object-cover rounded-lg">
                        @else
                            <div class="w-10 h-10 mx-auto rounded-lg gradient-loot grid place-items-center text-white font-extrabold text-sm">{{ $m->initials() }}</div>
                        @endif
                        <p class="mt-2 text-sm font-bold text-loot-ink truncate">{{ $m->name }}</p>
                        <p class="text-[10px] text-loot-muted">min {{ number_format($minM, 0) }} $LOOT</p>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Amount in $LOOT</label>
                <input type="number" name="amount" step="0.01" min="1" value="{{ old('amount') }}" placeholder="e.g. 5000"
                       class="w-full rounded-xl border {{ $errors->has('amount') ? 'border-rose-300' : 'border-loot-border' }} focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-loot-ink mb-1.5">Account name (optional)</label>
                <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="Recipient name"
                       class="w-full rounded-xl border border-loot-border focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-loot-ink mb-1.5"><span id="account-label">Account / Address</span></label>
                <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Your payout destination"
                       class="w-full rounded-xl border {{ $errors->has('account_number') ? 'border-rose-300' : 'border-loot-border' }} focus:border-loot-primary focus:ring-4 focus:ring-emerald-100 px-3.5 py-2.5 text-sm">
                <p id="method-instructions" class="text-xs text-loot-muted mt-2"></p>
            </div>
        </div>

        <button type="submit" class="mt-5 inline-flex items-center px-5 py-3 rounded-xl gradient-loot text-white font-bold shadow-cardLg hover:opacity-90">
            Submit withdrawal request
        </button>
    </form>
@endif

<h3 class="text-lg font-bold text-loot-ink mb-3">Recent withdrawals</h3>
<div class="rounded-2xl bg-white border border-loot-border shadow-soft overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wider text-loot-muted">
                    <th class="px-5 py-3 font-semibold">Method</th>
                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                    <th class="px-5 py-3 font-semibold">Account</th>
                    <th class="px-5 py-3 font-semibold text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Requested</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-loot-border">
                @forelse($withdrawals as $w)
                    @php
                        $tone = match($w->status){
                            'pending' => 'bg-amber-50 text-loot-accentDark',
                            'approved','paid','completed' => 'bg-emerald-50 text-loot-primary',
                            'rejected','cancelled' => 'bg-rose-50 text-rose-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-loot-ink">{{ $w->method }}</td>
                        <td class="px-5 py-3 text-right font-bold text-loot-ink">{{ number_format($w->amount) }}</td>
                        <td class="px-5 py-3 text-loot-ink/80 truncate max-w-xs">{{ $w->account }}</td>
                        <td class="px-5 py-3 text-center"><span class="inline-block text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $tone }}">{{ $w->status }}</span></td>
                        <td class="px-5 py-3 text-right text-xs text-loot-muted">{{ $w->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-loot-muted">No withdrawals yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('input[name="method_id"]').forEach(r => {
        r.addEventListener('change', e => {
            const el = e.target;
            const label = document.getElementById('account-label');
            const ins = document.getElementById('method-instructions');
            const amount = document.querySelector('input[name="amount"]');
            if (label) label.textContent = el.dataset.label || 'Account / Address';
            if (ins)   ins.textContent   = el.dataset.instructions || '';
            if (amount && el.dataset.min) amount.placeholder = 'min ' + parseFloat(el.dataset.min).toLocaleString();
        });
    });
    // Trigger initial
    const checked = document.querySelector('input[name="method_id"]:checked');
    if (checked) checked.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
