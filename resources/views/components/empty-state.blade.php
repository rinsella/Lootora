@props([
    'icon'   => null,
    'title'  => 'Nothing here yet',
    'desc'   => null,
])
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-loot-border bg-white p-10 text-center']) }}>
    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-loot-primary grid place-items-center text-2xl">
        {!! $icon ?? '✨' !!}
    </div>
    <h3 class="mt-4 font-bold text-loot-ink">{{ $title }}</h3>
    @if($desc)
        <p class="mt-1 text-sm text-loot-muted max-w-md mx-auto">{{ $desc }}</p>
    @endif
    @if(trim($slot) !== '')
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
