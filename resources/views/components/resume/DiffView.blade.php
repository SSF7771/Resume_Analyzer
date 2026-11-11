@props(['resumeId'=>null,'versions'=>[]])
<div
    id="blade-diff-view"
    data-resume-id="{{ $resumeId }}"
    data-versions='@json(array_values($versions))'
    class="rounded-[20px] border border-[var(--border)] shadow-card bg-[var(--card)] p-5"
>
    @if(count($versions) < 2)
        <div class="flex flex-col items-center text-center py-12">
            <div class="h-14 w-14 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center mb-3">
                <i data-lucide="git-compare" class="w-6 h-6"></i>
            </div>
            <div class="font-display text-lg font-semibold tracking-tight">Need two versions to compare</div>
            <p class="text-sm text-[var(--muted-foreground)] mt-1 max-w-sm">Apply rewrites or upload a new version to see diffs here.</p>
        </div>
    @else
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="font-display text-base font-semibold">Version Diff</div>
                <div class="text-sm text-[var(--muted-foreground)] mt-1">Compare what changed between any two versions</div>
            </div>
            <div id="blade-diff-mode" class="inline-flex items-center gap-1 bg-[var(--secondary)] border border-[var(--border)] p-1 rounded-full"></div>
        </div>
        <div class="rounded-2xl border border-[var(--border)] bg-[var(--secondary)]/60 p-3 mt-5 flex items-center justify-center gap-3 flex-wrap">
            <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--muted-foreground)]">From</span>
            <select id="blade-diff-from" class="h-8 px-3 rounded-full border border-[var(--border)] bg-[var(--card)] text-xs"></select>
            <div class="h-8 w-8 rounded-full flex items-center justify-center text-primary-foreground shadow-card" ><i data-lucide="arrow-right" class="w-4 h-4"></i></div>
            <span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--muted-foreground)]">To</span>
            <select id="blade-diff-to" class="h-8 px-3 rounded-full border border-[var(--border)] bg-[var(--card)] text-xs"></select>
            <button id="blade-diff-compare" class="h-8 px-3 rounded-full text-primary-foreground text-xs font-semibold" >Compare</button>
        </div>
        <div id="blade-diff-result" class="mt-5"></div>
    @endif
</div>