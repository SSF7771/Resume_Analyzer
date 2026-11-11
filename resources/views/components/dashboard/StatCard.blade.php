@props(['label' => '', 'value' => '-', 'suffix' => null, 'delta' => null, 'accent' => false])<x-ui.card
    variant="{{ $accent ? 'accent' : 'default' }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="text-xs {{ $accent ? 'text-white/60' : 'text-[var(--muted-foreground)]' }}">{{ $label }}</div>
            <div class="font-display tabular text-3xl font-semibold tracking-tight mt-2">{{ $value }} @if($suffix)<span
            class="text-sm opacity-50">{{ $suffix }}</span>@endif</div>@if($delta !== null)
                        <div class="text-[10px] mt-1 {{ $accent ? 'text-white/60' : 'text-[var(--primary)]' }}">
                    {{ $delta >= 0 ? '+' : '' }}{{ $delta }} vs previous</div>@endif
        </div>
    </div>
</x-ui.card>