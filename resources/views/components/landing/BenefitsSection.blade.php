<section id="benefits" class="bg-[var(--background)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-3xl">
            <div class="text-[11px] uppercase tracking-wider font-semibold text-[var(--primary)]">Why teams keep it
            </div>
            <h2 class="font-display text-[40px] sm:text-[50px] leading-[1.02] tracking-tight font-semibold mt-3">Less
                ceremony. More <em class="font-serif font-normal">signal.</em></h2>
        </div>
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach([['focus', 'Focus on signal', 'Get a short list of concrete issues instead of a wall of generic advice.'], ['target', 'Aim at the role', 'Target the language and outcomes hiring systems search for.'], ['repeat', 'Iterate safely', 'Keep V1, compare rewrites, and only ship changes you choose.'], ['clock-3', 'Move faster', 'Turn a long resume edit into a focused review you can finish quickly.'], ['shield-check', 'Keep control', 'Your API-backed data stays behind your own Laravel authentication.'], ['sparkles', 'Sharper writing', 'Use AI suggestions as editable drafts, not black-box replacements.']] as $b)
                <div class="rounded-2xl border border-[var(--border)] p-6 bg-[var(--card)] shadow-card">
                    <div
                        class="h-10 w-10 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center">
                        <i data-lucide="{{ $b[0] }}" class="w-5 h-5"></i></div>
                    <h3 class="font-display text-lg font-semibold mt-4">{{ $b[1] }}</h3>
                    <p class="text-sm text-[var(--muted-foreground)] mt-1.5 leading-relaxed">{{ $b[2] }}</p>
            </div>@endforeach
        </div>
    </div>
</section>