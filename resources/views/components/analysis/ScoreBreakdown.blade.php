@props(['breakdown' => []])
<x-ui.card>
    <div class="font-display text-base font-semibold">Score breakdown</div>
    <div class="space-y-4 mt-5">
        @forelse((array) $breakdown as $key => $row)@php($label = is_string($key) ? ucwords(str_replace('_', ' ', $key)) : ($row['label'] ?? 'Category'))
        @php($value = is_array($row) ? ($row['score'] ?? $row['value'] ?? 0) : $row)
        <div>
            <div class="flex items-center justify-between text-xs mb-1.5"><span
                    class="font-medium">{{ $label }}</span><span
                    class="tabular text-[var(--muted-foreground)]">{{ $value }}</span></div>
            <div class="bar"><span style="width:{{ max(0, min(100, (float) $value)) }}%"></span></div>
        </div>@empty<div class="text-sm text-[var(--muted-foreground)]">No score breakdown returned.</div>@endforelse
    </div>
</x-ui.card>