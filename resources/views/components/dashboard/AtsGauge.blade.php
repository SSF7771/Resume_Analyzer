@props(['score' => 0, 'delta' => 0])<x-ui.card class="h-full">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--muted-foreground)]">ATS score
            </div>
            <div class="font-display text-5xl font-semibold tracking-tight mt-2">{{ $score ?? '-' }}</div>
            <div class="text-xs text-[var(--muted-foreground)] mt-1">/ 100</div>
        </div>
        <div class="h-20 w-20 rounded-full border-8 border-[var(--secondary)] flex items-center justify-center"><span
                class="text-sm font-semibold">{{ is_numeric($score) ? round($score) : '-' }}</span></div>
    </div>@if($delta !== null)
    <div class="text-[10px] text-[var(--primary)] mt-4">{{ $delta >= 0 ? '+' : '' }}{{ $delta }} vs previous</div>@endif
</x-ui.card>