@extends('layouts.app')
@section('title', 'Insights - Roaster')
@section('page', 'insights')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 min-w-0 px-4 sm:px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>

            <div id="insights-page" class="page-enter">
                <!-- Loading placeholder -->
                <div class="space-y-4 animate-pulse">
                    <div class="h-12 w-1/3 bg-[var(--surface-2)] rounded-2xl"></div>
                    <div class="h-64 w-full bg-[var(--surface-2)] rounded-3xl"></div>
                </div>
            </div>
        </main>
    </div>
    @include('components.layout.CommandPalette')
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Use Roaster helpers cleanly
            const $ = Roaster.$;
            const $$ = Roaster.$$;
            const esc = Roaster.esc;
            const icon = Roaster.icon;
            const card = Roaster.card;
            const relativeTime = Roaster.relativeTime;
            const drawIcons = Roaster.drawIcons;

            // Methods that rely on 'this.[something]':
            const empty = Roaster.empty.bind(Roaster);
            const badge = Roaster.badge.bind(Roaster);
            const pageHeader = Roaster.pageHeader.bind(Roaster);
            const toast = Roaster.toast.bind(Roaster);
            const api = Roaster.api.bind(Roaster);

            function statCard(ic, title, value, sub) {
                const n = parseFloat(value);
                return card(`
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-xs font-semibold text-[var(--muted-foreground)]">${title}</div>
                            <div class="font-display text-4xl font-extrabold mt-2 tabular">${isNaN(n) ? esc(value ?? "-") : `<span data-count="${n}">0</span>`}</div>
                            <div class="text-xs text-[var(--muted-foreground)] mt-0.5 truncate">${esc(sub)}</div>
                        </div>
                        <span class="icon-tile h-11 w-11 rounded-2xl">${icon(ic, "w-5 h-5")}</span>
                    </div>`, "card-interactive");
            }

            async function renderInsights() {
                var root = $("#insights-page");
                if (!root) return;
                try {
                    var d = await api("/insights");
                    if (d.empty) {
                        root.innerHTML =
                            pageHeader(
                                "Insights",
                                "Patterns across all your resumes and analyses.",
                            ) +
                            empty(
                                "sparkles",
                                "No analyses yet",
                                "Once you analyze a few resumes, this page lights up with trends across your data.",
                                '<a href="/resumes" class="roaster-primary-button inline-flex items-center gap-2 h-10 px-4 rounded-2xl text-sm font-semibold text-white">Go to resumes</a>',
                            );
                        drawIcons();
                        return;
                    }

                    root.innerHTML =
                        pageHeader(
                            "Insights",
                            "Patterns across all your resumes and analyses.",
                        ) +
                        `<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 stagger">
                            ${statCard("gauge", "Average ATS score", d.averageScore, "/ 100")}
                            ${statCard("trophy", "Best score", d.bestScore && d.bestScore.value, (d.bestScore && d.bestScore.resumeTitle) || "")}
                            ${statCard("activity", "Total analyses", d.totalAnalyses, "all time")}
                        </div>` +
                        '</div><div class="mt-6">' +
                        card(
                            '<div class="font-display text-base font-bold">Score Trend</div><div class="text-sm text-[var(--ink-muted)] mt-1">Every analysis you\'ve run, chronologically</div><div class="h-[280px] mt-5"><canvas id="insight-chart"></canvas></div>',
                        ) +
                        '</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">' +
                        card(
                            '<div class="font-display text-base font-bold">Recurring issues</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">What comes up most often across your analyses</div><div class="space-y-1">' +
                            (d.topIssues || [])
                                .map(function (x) {
                                    var max = Math.max.apply(null, (d.topIssues || []).map(function (i) { return Number(i.count) || 1; }));
                                    var pct = Math.round(((Number(x.count) || 0) / (max || 1)) * 100);
                                    return (
                                        '<div class="flex items-start gap-3 p-3 rounded-2xl hover:bg-[var(--surface-2)] transition-colors"><div class="icon-tile h-9 w-9 rounded-xl shrink-0">' +
                                        icon("alert-circle") +
                                        '</div><div class="flex-1 min-w-0"><div class="text-sm font-semibold">' +
                                        esc(x.title) +
                                        '</div><div class="bar mt-2"><span style="width:' + pct + '%"></span></div><div class="mt-2">' +
                                        badge(
                                            x.severity || "medium",
                                            x.severity === "high"
                                                ? "danger"
                                                : x.severity === "medium"
                                                    ? "warning"
                                                    : "neutral",
                                        ) +
                                        ' <span class="text-xs text-[var(--ink-muted)] ml-1">' +
                                        esc(x.count) +
                                        "x</span></div></div></div>"
                                    );
                                })
                                .join("") +
                            "</div>",
                        ) +
                        card(
                            '<div class="font-display text-base font-bold">Most-Missed Keywords</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">Words ATS expected but didn\'t see</div><div class="flex flex-wrap gap-2">' +
                            (d.topMissingKeywords || [])
                                .map(function (k) {
                                    return badge(k.keyword + " x" + k.count, "danger");
                                })
                                .join("") +
                            "</div>",
                        ) +
                        "</div>" +
                        (d.topPresentKeywords && d.topPresentKeywords.length
                            ? '<div class="mt-6">' +
                            card(
                                '<div class="font-display text-base font-bold">Your Keyword Anchors</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">Words ATS consistently sees on your resumes</div><div class="flex flex-wrap gap-2">' +
                                d.topPresentKeywords
                                    .map(function (k) {
                                        return badge(
                                            k.keyword + " x" + k.count,
                                            "accent",
                                        );
                                    })
                                    .join("") +
                                "</div>",
                            ) +
                            "</div>"
                            : "") +
                        '<div class="mt-6">' +
                        card(
                            '<div class="font-display text-base font-bold">By Resume</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">How each of your resumes is performing</div><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left text-[10px] uppercase tracking-wide text-[var(--ink-muted)] border-b border-[var(--border)]"><th class="py-2">Resume</th><th class="py-2 text-right">Latest</th><th class="py-2 text-right">Best</th><th class="py-2 text-right">Improvement</th><th class="py-2 text-right">Analyses</th></tr></thead><tbody>' +
                            (d.resumePerformance || [])
                                .map(function (r) {
                                    return (
                                        "<tr onclick=\"location.href='/resumes/" +
                                        encodeURIComponent(r.resumeId) +
                                        '\'" class="border-b border-[var(--border)] last:border-0 hover:bg-[var(--surface-2)] cursor-pointer transition-colors"><td class="py-3.5 font-semibold"><span class="inline-flex items-center gap-2">' + icon("file-text", "w-4 h-4 text-[var(--muted-foreground)]") + ' ' +
                                        esc(r.title) +
                                        '</span></td><td class="py-3 text-right font-display tabular font-extrabold">' +
                                        esc(r.latestScore) +
                                        '</td><td class="py-3 text-right text-[var(--ink-muted)] tabular">' +
                                        esc(r.bestScore) +
                                        '</td><td class="py-3 text-right">' +
                                        badge(
                                            (r.improvement >= 0 ? "+" : "") +
                                            r.improvement,
                                            r.improvement >= 0 ? "success" : "danger",
                                        ) +
                                        '</td><td class="py-3 text-right text-[var(--ink-muted)] tabular">' +
                                        esc(r.analysesCount) +
                                        "</td></tr>"
                                    );
                                })
                                .join("") +
                            "</tbody></table></div>",
                        ) +
                        "</div>";

                    drawIcons();

                    // Helper for chart's responsive coloring
                    function getComputedColor(variableName) {
                        return getComputedStyle(document.documentElement)
                            .getPropertyValue(variableName)
                            .trim();
                    }

                    if ($("#insight-chart") && window.Chart) {
                        new Chart($("#insight-chart"), {
                            type: "line",
                            data: {
                                labels: (d.scoreTrend || []).map(function (_, i) {
                                    return "ATS Score #" + (i + 1);
                                }),
                                datasets: [
                                    {
                                        data: (d.scoreTrend || []).map(function (p) {
                                            return p.score;
                                        }),
                                        borderColor: getComputedColor("--chart-accent"),
                                        borderWidth: 3,
                                        backgroundColor: window.makeGradient($("#insight-chart"), 280),
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 7,
                                        pointBackgroundColor: getComputedColor("--card"),
                                        pointBorderColor: getComputedColor("--chart-accent"),
                                        pointBorderWidth: 2,
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: { duration: 1400, easing: "easeOutQuart" },
                                interaction: { intersect: false, mode: "index" },
                                plugins: { legend: { display: false }, tooltip: window.chartTooltip() },
                                scales: {
                                    y: {
                                        min: 0,
                                        max: 100,
                                        grid: {
                                            color: getComputedStyle(
                                                document.documentElement,
                                            )
                                                .getPropertyValue("--grid-color")
                                                .trim(),
                                            drawBorder: false,
                                        },
                                    },
                                    x: { grid: { display: false }, ticks: { maxRotation: 0, callback: function (_, i) { return "#" + (i + 1); } } },
                                },
                            },
                        });
                    }
                } catch (e) {
                    root.innerHTML = empty(
                        "bar-chart-3",
                        "Couldn't load insights",
                        e.message,
                    );
                    drawIcons();
                }
            }

            renderInsights();
        });
    </script>
@endpush