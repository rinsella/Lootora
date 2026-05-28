@props([
    'href'    => null,
    'type'    => 'button',
    'variant' => 'primary', // primary | secondary | ghost | gold
    'size'    => 'md',      // sm | md | lg
])
@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition focus:outline-none focus:ring-2 focus:ring-offset-2';
    $sizes = ['sm' => 'px-3 py-1.5 text-sm', 'md' => 'px-4 py-2.5 text-sm', 'lg' => 'px-6 py-3 text-base'][$size];
    $variants = [
        'primary'   => 'bg-loot-primary text-white hover:bg-loot-primaryDark focus:ring-loot-primary/40 shadow-cardLg',
        'secondary' => 'bg-white text-loot-ink border border-loot-border hover:bg-gray-50 focus:ring-loot-border',
        'ghost'     => 'bg-transparent text-loot-ink hover:bg-gray-100',
        'gold'      => 'bg-loot-accent text-white hover:bg-loot-accentDark focus:ring-loot-accent/40',
    ][$variant];
    $cls = $base.' '.$sizes.' '.$variants;
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</button>
@endif
