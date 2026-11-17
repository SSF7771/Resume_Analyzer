{{-- Interactive tabbed showcase (tabs handled by initTabs in app.js) --}}
<section class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center" data-tabs>
            <div data-reveal="left">
                <div class="roaster-hero-badge"><i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i> Inside the
                    workspace</div>
                <h2 class="font-display text-[36px] sm:text-[48px] leading-[1.02] font-extrabold tracking-tight mt-5">
                    Everything stays close to the <span class="font-serif italic font-normal text-gradient">source.</span>
                </h2>
                <p class="text-base text-[var(--muted-foreground)] mt-4 leading-relaxed">Upload, analysis, rewrites,
                    versions, history, insights, and export are all connected to the same resume record.</p>

                <div class="mt-8 space-y-3" role="tablist">
                    @foreach ([
                        ['analyze', 'gauge', 'Instant ATS analysis', 'Score breakdown across format, keywords, impact and clarity.'],
                        ['rewrite', 'wand-2', 'One-click rewrites', 'Accept the suggestions you like — each creates a new version.'],
                        ['compare', 'git-compare', 'Side-by-side diff', 'See exactly what changed between any two versions.'],
                    ] as $i => $t)
                        <button type="button" role="tab" data-tab="{{ $t[0] }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                            class="tab-btn w-full text-left flex items-start gap-4 p-4 rounded-2xl border border-transparent transition-all duration-300 hover:bg-[var(--card)] aria-selected:bg-[var(--card)] aria-selected:border-[var(--border)] aria-selected:shadow-card">
                            <div class="icon-tile h-11 w-11 rounded-xl shrink-0"><i data-lucide="{{ $t[1] }}" class="w-5 h-5"></i></div>
                            <div class="flex-1">
                                <div class="font-display font-bold">{{ $t[2] }}</div>
                                <div class="text-sm text-[var(--muted-foreground)] mt-0.5">{{ $t[3] }}</div>
                                <div class="tab-progress mt-3 h-1 rounded-full bg-[var(--border)] overflow-hidden hidden">
                                    <span class="block h-full bg-brand origin-left"></span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div data-reveal="right">
                <div class="relative rounded-[32px] roaster-preview-panel shadow-hover p-6 sm:p-8 min-h-[380px] overflow-hidden">
                    <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-brand opacity-20 blur-3xl pointer-events-none"></div>

                    <!-- Analyze panel -->
                    <div data-tab-panel="analyze" class="relative">
                        <div class="text-xs font-semibold text-[var(--muted-foreground)] uppercase tracking-wider">Score breakdown</div>
                        <div class="mt-5 space-y-4">
                            @foreach ([['Format', 92], ['Keywords', 79], ['Impact', 71], ['Clarity', 88]] as $b)
                                <div>
                                    <div class="flex justify-between text-sm font-semibold">
                                        <span>{{ $b[0] }}</span><span class="tabular">{{ $b[1] }}</span>
                                    </div>
                                    <div class="bar mt-2"><span style="width: {{ $b[1] }}%"></span></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach (['React', 'TypeScript', 'Laravel', 'REST API', 'CI/CD'] as $k)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[var(--success)]/12 text-[var(--success)] border border-[var(--success)]/25">✓ {{ $k }}</span>
                            @endforeach
                            @foreach (['Docker', 'GraphQL'] as $k)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[var(--danger)]/10 text-[var(--danger)] border border-[var(--danger)]/25">✕ {{ $k }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Rewrite panel -->
                    <div data-tab-panel="rewrite" class="relative hidden">
                        <div class="text-xs font-semibold text-[var(--muted-foreground)] uppercase tracking-wider">Suggested rewrites</div>
                        <div class="mt-5 space-y-3">
                            @foreach ([
                                ['Worked on the company website.', 'Rebuilt the marketing site in Next.js, cutting load time by 48%.'],
                                ['Helped with customer support tickets.', 'Resolved 120+ tickets/month with a 97% satisfaction score.'],
                            ] as $r)
                                <div class="rounded-2xl roaster-preview-card p-4">
                                    <div class="text-sm text-[var(--muted-foreground)] line-through">{{ $r[0] }}</div>
                                    <div class="text-sm font-semibold mt-1.5">{{ $r[1] }}</div>
                                    <div class="flex gap-2 mt-3">
                                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-brand text-white">Apply</span>
                                        <span class="text-[11px] font-bold px-3 py-1 rounded-full border border-[var(--border)]">Skip</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Compare panel -->
                    <div data-tab-panel="compare" class="relative hidden">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold text-[var(--muted-foreground)] uppercase tracking-wider">V2 → V3</div>
                            <span class="text-[10px] px-2 py-1 rounded-full bg-[var(--success)]/12 text-[var(--success)] font-bold">+11 score</span>
                        </div>
                        <div class="mt-5 rounded-2xl roaster-preview-card p-5 text-sm leading-7 font-mono">
                            <div><span class="bg-[var(--danger)]/15 text-[var(--danger)] line-through px-1 rounded">Responsible for</span>
                                <span class="bg-[var(--success)]/15 text-[var(--success)] px-1 rounded">Owned</span> the payments
                                API, <span class="bg-[var(--success)]/15 text-[var(--success)] px-1 rounded">processing $2M/month</span>.</div>
                            <div class="mt-2"><span class="bg-[var(--danger)]/15 text-[var(--danger)] line-through px-1 rounded">Did</span>
                                <span class="bg-[var(--success)]/15 text-[var(--success)] px-1 rounded">Automated</span> testing
                                <span class="bg-[var(--success)]/15 text-[var(--success)] px-1 rounded">with GitHub Actions</span>.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
