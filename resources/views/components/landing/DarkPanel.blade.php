@props(['class' => '', 'glow' => true, 'radius' => 'rounded-[32px]'])
<div {{ $attributes->merge(['class' => 'relative overflow-hidden ' . $radius . ' ' . $class]) }}
    style="background: var(--brand-gradient)">
    @if($glow)
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full blur-3xl opacity-30"
            style="background:var(--primary)"></div>
    @endif
    <div class="relative">{{ $slot }}</div>
</div>