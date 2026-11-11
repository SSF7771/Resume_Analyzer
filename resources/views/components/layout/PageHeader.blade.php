@props(['title' => '', 'description' => ''])
<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6']) }}>
    <div>
        <h2 class="font-display text-3xl font-semibold tracking-tight">
            {{ $title }}
        </h2>
        @if($description)
            <p class="text-sm text-[var(--muted-foreground)] mt-1">{{ $description }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endif
</div>