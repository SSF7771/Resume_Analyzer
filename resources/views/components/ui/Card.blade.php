@props(['variant' => 'default', 'radius' => 'md', 'padding' => 'md'])

@php
    $baseClasses = 'bg-card border border-border shadow-card transition-all duration-300';
    
    $variantClasses = match($variant) {
        'accent' => 'bg-primary text-primary-foreground border-transparent',
        default => 'hover:shadow-hover',
    };

    $radiusClasses = match($radius) {
        'lg' => 'rounded-3xl',
        'none' => 'rounded-none',
        default => 'rounded-2xl',
    };

    $paddingClasses = match($padding) {
        'sm' => 'p-4',
        'lg' => 'p-6',
        default => 'p-5',
    };

    $classes = "{$baseClasses} {$variantClasses} {$radiusClasses} {$paddingClasses}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>