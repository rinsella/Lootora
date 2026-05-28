@props([
    'variant' => 'success', // success | warning | danger | info | neutral
    'icon'    => '●',
])
@php
    $palette = [
        'success' => 'bg-emerald-50 text-loot-primary',
        'warning' => 'bg-amber-50 text-loot-accentDark',
        'danger'  => 'bg-red-50 text-red-700',
        'info'    => 'bg-blue-50 text-blue-700',
        'neutral' => 'bg-gray-100 text-gray-700',
    ][$variant] ?? 'bg-gray-100 text-gray-700';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {$palette}"]) }}>
    <span class="text-[8px] leading-none">{!! $icon !!}</span>
    {{ $slot }}
</span>
