@props(['items' => []])<x-ui.card>
    <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold mb-3">Activity</div>
    <div class="space-y-2">@forelse($items as $e)<a
        href="{{ !empty($e['resumeId']) ? '/resumes/' . urlencode($e['resumeId']) : '#' }}"
        class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-[var(--secondary)]">
        <div
            class="h-8 w-8 rounded-xl bg-[var(--secondary)] flex items-center justify-center text-[var(--muted-foreground)]">
            <i data-lucide="{{ ($e['type'] ?? '') === 'upload' ? 'upload' : (($e['type'] ?? '') === 'rewrite' ? 'wand-2' : 'sparkles') }}"
                class="w-4 h-4"></i></div>
        <div>
            <div class="text-sm font-medium">{{ $e['title'] ?? 'Activity' }}</div>
            <div class="text-xs text-[var(--muted-foreground)] mt-0.5">{{ $e['subtitle'] ?? '' }}</div>
        </div>
    </a>@empty<div class="text-sm text-[var(--muted-foreground)] py-8 text-center">Quiet here.</div>@endforelse
    </div>
</x-ui.card>