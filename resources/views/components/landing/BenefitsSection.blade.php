<section id="benefits" class="relative scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="max-w-3xl" data-reveal>
            <div class="roaster-hero-badge"><i data-lucide="heart" class="w-3.5 h-3.5"></i> Why people keep it</div>
            <h2 class="font-display text-[36px] sm:text-[52px] leading-[1.02] tracking-tight font-extrabold mt-5">
                Less ceremony. More <span class="font-serif italic font-normal text-gradient">signal.</span>
            </h2>
        </div>

        <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach ([
                ['focus', 'Focus on signal', 'Get a short list of concrete issues instead of a wall of generic advice.'],
                ['target', 'Aim at the role', 'Target the language and outcomes hiring systems search for.'],
                ['repeat', 'Iterate safely', 'Keep V1, compare rewrites, and only ship changes you choose.'],
                ['clock-3', 'Move faster', 'Turn a long resume edit into a focused review you can finish quickly.'],
                ['shield-check', 'Keep control', 'Your data stays behind your own authenticated account.'],
                ['sparkles', 'Sharper writing', 'Use AI suggestions as editable drafts, not black-box replacements.'],
            ] as $i => $b)
                <div data-reveal data-spotlight style="--reveal-delay: {{ $i * 70 }}ms"
                    class="card-interactive group flex gap-4 rounded-3xl border border-[var(--border)] p-6 bg-[var(--card)] shadow-card">
                    <div class="icon-tile h-11 w-11 rounded-2xl shrink-0"><i data-lucide="{{ $b[0] }}" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-display text-base font-bold">{{ $b[1] }}</h3>
                        <p class="text-sm text-[var(--muted-foreground)] mt-1.5 leading-relaxed">{{ $b[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ accordion (initAccordion in app.js) -->
        <div class="mt-24 grid lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4" data-reveal>
                <h3 class="font-display text-3xl font-extrabold tracking-tight">Questions? <span class="font-serif italic font-normal text-gradient">Answers.</span></h3>
                <p class="text-sm text-[var(--muted-foreground)] mt-3 leading-relaxed">Everything you need to know before uploading your first resume.</p>
            </div>
            <div class="lg:col-span-8 space-y-3" data-accordion>
                @foreach ([
                    ['What file formats are supported?', 'Roaster currently accepts PDF resumes up to 5MB. The text is extracted automatically and saved as version V1.'],
                    ['How is the ATS score calculated?', 'The AI checks formatting, keyword coverage for your target role, measurable impact and clarity, then combines them into a score out of 100.'],
                    ['Will rewrites overwrite my original resume?', 'Never. Applying rewrites creates a new version, so you can always compare or go back to the original.'],
                    ['Can I export the improved resume?', 'Yes — export an ATS-friendly PDF from any version on the export page.'],
                ] as $i => $q)
                    <div class="accordion-item rounded-2xl border border-[var(--border)] bg-[var(--card)] shadow-card overflow-hidden" data-reveal style="--reveal-delay: {{ $i * 60 }}ms">
                        <button type="button" class="accordion-trigger w-full flex items-center justify-between gap-4 p-5 text-left font-semibold" aria-expanded="false">
                            <span>{{ $q[0] }}</span>
                            <span class="accordion-icon h-8 w-8 rounded-full bg-[var(--secondary)] flex items-center justify-center shrink-0 transition-transform duration-300">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </span>
                        </button>
                        <div class="accordion-panel grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-out">
                            <div class="overflow-hidden">
                                <p class="px-5 pb-5 text-sm text-[var(--muted-foreground)] leading-relaxed">{{ $q[1] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
