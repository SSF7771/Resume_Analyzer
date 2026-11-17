<section id="features" class="relative scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="max-w-3xl" data-reveal>
            <div class="roaster-hero-badge">
                <i data-lucide="zap" class="w-3.5 h-3.5"></i> Built for the parts that matter
            </div>
            <h2 class="font-display text-[36px] sm:text-[52px] leading-[1.02] tracking-tight font-extrabold mt-5">
                Everything your resume <span class="font-serif italic font-normal text-gradient">needs</span> to get
                sharper.
            </h2>
            <p class="text-base text-[var(--muted-foreground)] mt-4 leading-relaxed max-w-2xl">From first upload to
                final rewrite, the workflow keeps the signal high and the busywork low.</p>
        </div>

        <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach ([
                ['scan-text', 'ATS scoring', 'See an actionable score and breakdown across the resume.'],
                ['lightbulb', 'Issue spotting', 'Find weak bullets, vague language, and missing signal.'],
                ['wand-2', 'AI rewrites', 'Turn generic bullets into specific, impact-led writing.'],
                ['key-round', 'Keyword matching', 'See what an ATS sees — and what it misses.'],
                ['layers', 'Version history', 'Track uploads and rewrites without losing your originals.'],
                ['git-compare', 'Diff view', 'Compare versions word-by-word before you ship.'],
            ] as $i => $f)
                <div data-reveal data-spotlight style="--reveal-delay: {{ $i * 70 }}ms"
                    class="card-interactive group rounded-3xl border border-[var(--border)] bg-[var(--card)] shadow-card p-7">
                    <div class="icon-tile h-12 w-12 rounded-2xl">
                        <i data-lucide="{{ $f[0] }}" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-display text-lg font-bold mt-6">{{ $f[1] }}</h3>
                    <p class="text-sm text-[var(--muted-foreground)] mt-2 leading-relaxed">{{ $f[2] }}</p>
                    <div
                        class="mt-5 flex items-center gap-1.5 text-xs font-semibold opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0"
                        style="color: var(--accent-strong)">
                        Learn more <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
