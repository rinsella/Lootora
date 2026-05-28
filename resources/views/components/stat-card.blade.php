@props([
    'label'   => '',
    'value'   => '0',
    'sub'     => null,
    'icon'    => '★',
    'accent'  => 'emerald', // emerald | amber | blue | violet
])
@php
    $pal = [
        'emerald' => 'bg-emerald-50 text-loot-primary',
        'amber'   => 'bg-amber-50 text-loot-accentDark',
        'blue'    => 'bg-blue-50 text-blue-700',
        'violet'  => 'bg-violet-50 text-violet-700',
    ][$accent] ?? 'bg-gray-100 text-gray-700';
@endphp
<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white border border-loot-border p-5 shadow-soft']) }}>
    <div class="flex items-center justify-between">
        <p class="text-xs uppercase tracking-wider text-loot-muted font-semibold">{{ $label }}</p>
        <div class="w-9 h-9 rounded-xl {{ $pal }} grid place-items-center font-bold">{!! $icon !!}</div>
    </div>
    <p class="mt-3 text-2xl font-extrabold text-loot-ink">{{ $value }}</p>
    @if($sub)
        <p class="text-xs text-loot-muted mt-1">{{ $sub }}</p>
    @endif
</div>
