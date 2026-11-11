@props(['strengths' => []])
<x-ui.card>
    <div class="font-display text-base font-semibold">Strengths</div>
    <ul class="space-y-2 mt-4">@forelse((array) $strengths as $s)
        <li class="flex gap-2 text-sm"><span
                class="text-[var(--primary)]">✓</span><span>{{ is_string($s) ? $s : ($s['title'] ?? $s['description'] ?? '') }}</span>
    </li>@empty<li class="text-sm text-[var(--muted-foreground)]">No strengths recorded.</li>@endforelse
    </ul>
</x-ui.card>