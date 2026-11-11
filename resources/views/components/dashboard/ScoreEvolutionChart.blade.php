@props(['data' => []])<x-ui.card>
    <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Score </div>
    <div class="font-display text-base font-semibold mt-1">Every analysis, chronologically</div>
    <div class="h-[260px] mt-4">
        <canvas id="dashboard-score-chart" data-series='@json($scoreSeries)'></canvas>
    </div>
</x-ui.card>