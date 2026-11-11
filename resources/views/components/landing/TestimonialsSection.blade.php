<section class="bg-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <div class="text-[11px] uppercase tracking-wider font-semibold text-[var(--primary)]">Used in the moments
                that matter</div>
            <h2 class="font-display text-[40px] sm:text-[50px] font-semibold tracking-tight mt-3">Built to feel <em
                    class="font-serif font-normal">quietly useful.</em></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5 mt-12">
            @foreach([['“The diff view made it obvious which rewrite changes were actually worth keeping.”', 'Product engineer'], ['“The dashboard finally gave me one place to see score, versions, and what kept slipping.”', 'Job seeker'], ['“I liked that applying rewrites creates a new version instead of mutating the original.”', 'Hiring manager']] as $t)
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--card)] shadow-card p-7">
                    <p class="text-[15px] leading-relaxed">{{ $t[0] }}</p>
                    <div class="text-xs text-[var(--muted-foreground)] mt-5">{{ $t[1] }}</div>
            </div>@endforeach
        </div>
    </div>
</section>