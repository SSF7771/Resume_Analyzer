<section class="bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-24 pb-20">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-6">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[var(--secondary)] text-[var(--primary)] text-[11px] font-semibold uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-[var(--primary)]"></span> Resume intelligence
                </div>
                <h1 class="font-display text-[54px] sm:text-[72px] leading-[.94] tracking-tight font-semibold mt-5">
                    Sharpen your resume.<br><em class="font-serif font-normal">Ship with confidence.</em></h1>
                <p class="text-base sm:text-lg text-[var(--muted-foreground)] mt-6 max-w-2xl leading-relaxed">Roaster
                    scores your resume for ATS, identifies weak signal, proposes focused rewrites, and keeps every
                    version so you stay in control.</p>
                <div class="flex flex-wrap gap-3 mt-8"><a href="/register"
                        class="roaster-primary-button inline-flex items-center gap-2 h-12 px-6 rounded-full text-sm font-semibold">
                        Upload your resume
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <a href="#how-it-works"
                        class="roaster-secondary-button inline-flex items-center h-12 px-5 rounded-full text-sm font-semibold">See
                        how it works</a>
                </div>
                <div class="flex items-center gap-6 mt-8 text-xs text-[var(--muted-foreground)]"><span
                        class="flex items-center gap-2"><i data-lucide="check"
                            class="w-3.5 h-3.5 text-[var(--primary)]"></i> PDF upload</span><span
                        class="flex items-center gap-2"><i data-lucide="check"
                            class="w-3.5 h-3.5 text-[var(--primary)]"></i> Gemini analysis</span><span
                        class="flex items-center gap-2"><i data-lucide="check"
                            class="w-3.5 h-3.5 text-[var(--primary)]"></i> Version history</span></div>
            </div>
            <div class="lg:col-span-6">@include('components.landing.HeroDashboardPreview')</div>
        </div>
    </div>
</section>