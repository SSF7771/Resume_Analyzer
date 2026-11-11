@props(['versions' => [], 'resumeId' => null, 'resumeTitle' => null])
<x-ui.card>
    <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold mb-3">Recent versions
    </div>
    <div class="space-y-1">
        @forelse($versions as $v)
            <a href="/resumes/{{ $v['resume_id'] ?? $resumeId }}"
                class="flex items-center gap-3 p-3 rounded-2xl hover:bg-[var(--secondary)]">
                <div
                    class="h-9 w-9 rounded-xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ $v['label'] ?? 'Version' }}</div>
                    <div class="text-xs text-[var(--muted-foreground)]">{{ $v['resumeTitle'] ?? $resumeTitle ?? '' }}</div>
                </div>
                <div class="font-display tabular font-semibold">{{ $v['score'] ?? '-' }}</div>
            </a>
        @empty
            <div class="text-sm text-[var(--muted-foreground)] py-8 text-center">No versions yet.</div>
        @endforelse
    </div>
</x-ui.card>