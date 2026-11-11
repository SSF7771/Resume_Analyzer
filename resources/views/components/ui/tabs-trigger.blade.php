@props(['value'])
<button type="button" data-tab="{{ $value }}" {{ $attributes->merge(['class' => 'h-8 px-3.5 rounded-full text-xs font-medium transition-colors text-[var(--muted-foreground)] hover:text-[var(--foreground)]']) }}>
    {{ $slot }}
</button>