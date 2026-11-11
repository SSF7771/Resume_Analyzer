<section id="how-it-works" class="bg-[var(--background)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center max-w-2xl mx-auto">
            <div class="text-[11px] uppercase tracking-wider font-semibold text-[var(--primary)]">How it works</div>
            <h2 class="font-display text-[40px] sm:text-[52px] leading-[1] font-semibold tracking-tight mt-3">A calmer
                path from PDF to <em class="font-serif font-normal">stronger copy.</em></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5 mt-12">
            @foreach([['01', 'Upload', 'Drop in a PDF. Roaster extracts the text and creates version V1.'], ['02', 'Analyze', 'Pick a target role and let the backend return score, issues, strengths, keywords, and rewrites.'], ['03', 'Refine', 'Apply only the rewrites you want, compare versions, and export an ATS-friendly PDF.']] as $s)
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--card)] shadow-card p-7">
                    <div class="font-display text-4xl font-semibold text-[var(--primary)]">{{ $s[0] }}</div>
                    <h3 class="font-display text-xl font-semibold mt-5">{{ $s[1] }}</h3>
                    <p class="text-sm text-[var(--muted-foreground)] mt-2 leading-relaxed">{{ $s[2] }}</p>
            </div>@endforeach
        </div>
    </div>
</section>