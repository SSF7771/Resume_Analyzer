<div class="relative rounded-[32px] overflow-hidden shadow-hover roaster-preview-panel p-5 sm:p-7">
    <div class="absolute -top-24 -right-24 h-56 w-56 rounded-full bg-brand opacity-20 blur-3xl pointer-events-none"></div>

    <!-- Header -->
    <div class="relative flex items-center justify-between text-xs roaster-preview-muted">
        <div class="flex items-center gap-1.5">
            <span class="h-2.5 w-2.5 rounded-full bg-[#ff5f57]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#febc2e]"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-[#28c840]"></span>
            <span class="ml-2 font-medium">Roaster dashboard</span>
        </div>
        <span class="inline-flex items-center gap-1.5 font-medium">
            <span class="pulse-dot !bg-[var(--success)]"></span>
            live
        </span>
    </div>

    <!-- Stat Grid -->
    <div class="relative mt-6 grid grid-cols-2 gap-3">
        <!-- ATS Score ring -->
        <div class="rounded-2xl roaster-preview-card p-4 flex items-center gap-4">
            <svg class="score-ring h-16 w-16 -rotate-90 shrink-0" viewBox="0 0 64 64" data-score-ring="86">
                <defs>
                    <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" style="stop-color: var(--primary)" />
                        <stop offset="100%" style="stop-color: var(--accent)" />
                    </linearGradient>
                </defs>
                <circle cx="32" cy="32" r="27" fill="none" stroke="var(--border)" stroke-width="7" />
                <circle class="progress" cx="32" cy="32" r="27" fill="none" stroke="url(#ringGrad)" stroke-width="7"
                    stroke-linecap="round" stroke-dasharray="169.6" stroke-dashoffset="169.6" />
            </svg>
            <div>
                <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle font-semibold">ATS score</div>
                <div class="font-display text-3xl font-extrabold tabular"><span data-count="86">0</span></div>
                <div class="text-[10px] roaster-preview-success font-semibold">▲ 8 vs previous</div>
            </div>
        </div>

        <!-- Keywords Matched -->
        <div class="rounded-2xl roaster-preview-card p-4">
            <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle font-semibold">Keywords</div>
            <div class="font-display text-3xl font-extrabold mt-1 tabular">
                <span data-count="27">0</span><span class="text-sm roaster-preview-subtle font-medium"> / 34</span>
            </div>
            <div class="bar mt-3"><span style="width:79%"></span></div>
            <div class="text-[10px] roaster-preview-success mt-1.5 font-semibold">79% matched</div>
        </div>
    </div>

    <!-- Score Evolution Chart Box -->
    <div class="relative mt-3 rounded-2xl roaster-preview-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle font-semibold">Score evolution</div>
                <div class="text-sm font-semibold mt-1">Every analysis, chronologically</div>
            </div>
            <span class="text-[10px] px-2 py-1 rounded-full bg-brand-soft text-[var(--primary)] font-bold">V1 → V5</span>
        </div>
        <div class="mt-4 h-28">
            <canvas id="landing-preview-chart"></canvas>
        </div>
    </div>

    <!-- AI Rewrite Sample Box -->
    <div class="relative mt-3 rounded-2xl roaster-preview-card p-4">
        <div class="flex items-center gap-1.5 text-[10px] uppercase tracking-wider roaster-preview-subtle font-semibold">
            <i data-lucide="wand-2" class="w-3 h-3 text-[var(--primary)]"></i> AI rewrite
        </div>
        <div class="mt-2 text-sm roaster-preview-muted line-through decoration-[var(--danger)]/60">
            Managed frontend delivery across multiple projects.
        </div>
        <div class="mt-1.5 text-sm font-semibold">
            Led frontend delivery across <span class="text-[var(--primary)]">4 products</span>, improving release
            throughput by <span class="text-[var(--success)]">32%</span>.
        </div>
    </div>
</div>
