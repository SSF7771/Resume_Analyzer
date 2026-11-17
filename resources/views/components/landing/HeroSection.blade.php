<section class="relative">
    <div class="aurora"><span></span></div>
    <div class="absolute inset-0 grid-backdrop pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-20 pb-20">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-6">
                <div class="roaster-hero-badge" data-reveal>
                    <span class="pulse-dot"></span> AI resume intelligence
                </div>

                <h1 class="font-display text-[44px] sm:text-[68px] leading-[1] tracking-tight font-extrabold mt-6"
                    data-reveal style="--reveal-delay:80ms">
                    Roast your resume.<br>
                    <span class="text-gradient">Land the</span>
                    <span class="font-serif italic font-normal text-gradient" data-typewriter
                        data-words='["interview.","offer.","dream job.","callback."]'>interview.</span>
                </h1>

                <p class="text-base sm:text-lg text-[var(--muted-foreground)] mt-6 max-w-xl leading-relaxed"
                    data-reveal style="--reveal-delay:160ms">
                    Roaster scores your resume for ATS, spots weak signal, proposes focused rewrites, and keeps every
                    version so you stay in control.
                </p>

                <div class="flex flex-wrap gap-3 mt-8" data-reveal style="--reveal-delay:240ms">
                    <a href="/register" data-magnetic
                        class="roaster-primary-button inline-flex items-center gap-2 h-12 px-7 rounded-full text-sm font-semibold">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        Upload your resume
                    </a>
                    <a href="#how-it-works"
                        class="roaster-secondary-button inline-flex items-center gap-2 h-12 px-6 rounded-full text-sm font-semibold">
                        <i data-lucide="play-circle" class="w-4 h-4"></i>
                        See how it works
                    </a>
                </div>

                <!-- Animated stats -->
                <div class="grid grid-cols-3 gap-3 mt-10 max-w-md" data-reveal style="--reveal-delay:320ms">
                    <div class="rounded-2xl glass p-3.5">
                        <div class="font-display text-2xl font-extrabold tabular"><span data-count="100">0</span></div>
                        <div class="text-[11px] text-[var(--muted-foreground)] mt-0.5">Point ATS scale</div>
                    </div>
                    <div class="rounded-2xl glass p-3.5">
                        <div class="font-display text-2xl font-extrabold tabular"><span data-count="6">0</span></div>
                        <div class="text-[11px] text-[var(--muted-foreground)] mt-0.5">AI review tools</div>
                    </div>
                    <div class="rounded-2xl glass p-3.5">
                        <div class="font-display text-2xl font-extrabold tabular"><span data-count="1">0</span>-click</div>
                        <div class="text-[11px] text-[var(--muted-foreground)] mt-0.5">Apply rewrites</div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-8 text-xs text-[var(--muted-foreground)]"
                    data-reveal style="--reveal-delay:400ms">
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            class="w-4 h-4 text-[var(--success)]"></i> PDF upload</span>
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            class="w-4 h-4 text-[var(--success)]"></i> Gemini analysis</span>
                    <span class="flex items-center gap-2"><i data-lucide="check-circle-2"
                            class="w-4 h-4 text-[var(--success)]"></i> Version history</span>
                </div>
            </div>

            <div class="lg:col-span-6 relative" data-reveal="zoom" style="--reveal-delay:200ms">
                <div data-tilt>@include('components.landing.HeroDashboardPreview')</div>

                <!-- Floating chips -->
                <div
                    class="float-slow hidden sm:flex absolute -left-8 top-[46%] glass rounded-2xl px-4 py-3 items-center gap-3 shadow-hover">
                    <div class="h-9 w-9 rounded-xl bg-[var(--success)]/15 text-[var(--success)] flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold">Score +24</div>
                        <div class="text-[10px] text-[var(--muted-foreground)]">after 3 rewrites</div>
                    </div>
                </div>
                <div
                    class="float-slower hidden sm:flex absolute -right-4 bottom-16 glass rounded-2xl px-4 py-3 items-center gap-3 shadow-hover">
                    <div class="h-9 w-9 rounded-xl brand-logo flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold">12 keywords found</div>
                        <div class="text-[10px] text-[var(--muted-foreground)]">React, Laravel, CI/CD…</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keyword marquee -->
    <div class="relative border-y border-[var(--border)] bg-[var(--card)]/60 py-4 marquee">
        <div class="gap-10 text-sm font-semibold text-[var(--muted-foreground)]">
            @foreach (range(1, 2) as $_)
                @foreach (['Software Engineer', 'Product Designer', 'Data Analyst', 'Marketing Lead', 'Backend Developer', 'UI/UX Designer', 'Project Manager', 'DevOps Engineer', 'Data Scientist', 'Frontend Developer'] as $role)
                    <span class="flex items-center gap-2 whitespace-nowrap"><i data-lucide="briefcase"
                            class="w-4 h-4 text-[var(--primary)]"></i>{{ $role }}</span>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
