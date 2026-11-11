<div class="rounded-[32px] overflow-hidden shadow-hover roaster-preview-panel p-5 sm:p-7">
    <!-- Header -->
    <div class="flex items-center justify-between text-xs roaster-preview-muted">
        <span>Roaster dashboard</span>
        <span class="inline-flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-[var(--preview-success)]"></span>
            live workspace
        </span>
    </div>

    <!-- Stat Grid -->
    <div class="mt-6 grid grid-cols-2 gap-3">
        <!-- ATS Score -->
        <div class="rounded-2xl roaster-preview-card p-4">
            <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle">ATS score</div>
            <div class="font-display text-4xl font-semibold mt-2">
                86<span class="text-sm roaster-preview-subtle"> / 100</span>
            </div>
            <div class="text-[10px] roaster-preview-success mt-1 font-medium">+8 vs previous</div>
        </div>

        <!-- Keywords Matched -->
        <div class="rounded-2xl roaster-preview-card p-4">
            <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle">Keywords</div>
            <div class="font-display text-4xl font-semibold mt-2">
                27<span class="text-sm roaster-preview-subtle"> / 34</span>
            </div>
            <div class="text-[10px] roaster-preview-success mt-1 font-medium">79% matched</div>
        </div>
    </div>

    <!-- Score Evolution Chart Box -->
    <div class="mt-4 rounded-2xl roaster-preview-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle">Score evolution</div>
                <div class="text-sm font-semibold mt-1">Every analysis, chronologically</div>
            </div>
            <span class="text-xs roaster-preview-subtle font-medium">V1 → V5</span>
        </div>
        <div class="mt-5 h-28">
            <canvas id="landing-preview-chart"></canvas>
        </div>
    </div>

    <!-- AI Rewrite Sample Box -->
    <div class="mt-4 rounded-2xl roaster-preview-card p-4">
        <div class="text-[10px] uppercase tracking-wider roaster-preview-subtle">AI rewrite</div>
        <div class="mt-2 text-sm roaster-preview-muted line-through">
            Managed frontend delivery across multiple projects.
        </div>
        <div class="mt-2 text-sm font-medium">
            Led frontend delivery across 4 products, improving release throughput by 32%.
        </div>
    </div>
</div>