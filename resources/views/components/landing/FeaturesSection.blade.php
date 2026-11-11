<section id="features" class="bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-3xl">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[var(--secondary)] text-[var(--primary)] text-[11px] font-semibold uppercase tracking-wider">
                Built for the parts that matter</div>
            <h2 class="font-display text-[40px] sm:text-[50px] leading-[1.02] tracking-tight font-semibold mt-4">
                Everything your resume <em class="font-serif font-normal">needs to</em> get sharper.</h2>
            <p class="text-base text-[var(--muted-foreground)] mt-4 leading-relaxed">From first upload to final rewrite,
                the workflow keeps the signal high and the busywork low.</p>
        </div>
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach([['scan-text', 'ATS scoring', 'See an actionable score and breakdown across the resume.'], ['lightbulb', 'Issue spotting', 'Find weak bullets, vague language, and missing signal.'], ['wand-2', 'AI rewrites', 'Turn generic bullets into specific, impact-led writing.'], ['key-round', 'Keyword matching', 'See what an ATS sees-and what it misses.'], ['layers', 'Version history', 'Track uploads and rewrites without losing your originals.'], ['git-compare', 'Diff view', 'Compare versions word-by-word before you ship.']] as $f)
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-card p-6">
                    <div
                        class="h-11 w-11 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center">
                        <i data-lucide="{{ $f[0] }}" class="w-5 h-5"></i></div>
                    <h3 class="font-display text-lg font-semibold mt-5">{{ $f[1] }}</h3>
                    <p class="text-sm text-[var(--muted-foreground)] mt-1.5 leading-relaxed">{{ $f[2] }}</p>
            </div>@endforeach
        </div>
    </div>
</section>