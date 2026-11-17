<section id="how-it-works" class="relative scroll-mt-20 bg-[var(--surface-2)]/60 border-y border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center max-w-2xl mx-auto" data-reveal>
            <div class="roaster-hero-badge"><i data-lucide="route" class="w-3.5 h-3.5"></i> How it works</div>
            <h2 class="font-display text-[36px] sm:text-[52px] leading-[1.02] font-extrabold tracking-tight mt-5">
                From PDF to <span class="font-serif italic font-normal text-gradient">stronger copy</span> in three
                steps.
            </h2>
        </div>

        <div class="relative grid md:grid-cols-3 gap-6 mt-16" data-steps>
            <!-- Connector line (desktop) -->
            <div class="hidden md:block absolute top-9 left-[16%] right-[16%] h-[3px] rounded-full bg-[var(--border)] overflow-hidden">
                <div data-steps-line class="h-full w-full bg-brand origin-left scale-x-0 transition-transform duration-[1600ms] ease-out"></div>
            </div>

            @foreach ([
                ['upload-cloud', 'Upload', 'Drop in a PDF. Roaster extracts the text and creates version V1.'],
                ['sparkles', 'Analyze', 'Pick a target role and get a score, issues, strengths, keywords, and rewrites.'],
                ['rocket', 'Refine', 'Apply only the rewrites you want, compare versions, and export an ATS-friendly PDF.'],
            ] as $i => $s)
                <div class="relative text-center group" data-reveal style="--reveal-delay: {{ $i * 150 }}ms">
                    <div class="relative mx-auto h-[72px] w-[72px]">
                        <div class="absolute inset-0 rounded-3xl bg-brand opacity-30 blur-xl transition-opacity group-hover:opacity-60"></div>
                        <div class="relative h-full w-full rounded-3xl brand-logo flex items-center justify-center transition-transform duration-500 group-hover:-translate-y-1 group-hover:rotate-6">
                            <i data-lucide="{{ $s[0] }}" class="w-7 h-7"></i>
                        </div>
                        <span class="absolute -top-2 -right-2 h-7 w-7 rounded-full bg-[var(--card)] border border-[var(--border)] text-[11px] font-extrabold flex items-center justify-center shadow-card">0{{ $i + 1 }}</span>
                    </div>
                    <h3 class="font-display text-xl font-bold mt-6">{{ $s[1] }}</h3>
                    <p class="text-sm text-[var(--muted-foreground)] mt-2 leading-relaxed max-w-xs mx-auto">{{ $s[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
