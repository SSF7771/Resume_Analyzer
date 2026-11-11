@extends('layouts.app')
@section('title', 'Insights - Roaster')
@section('page', 'insights')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
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
            const $ = Roaster.$;
            const esc = Roaster.esc;
            const icon = Roaster.icon;
            const badge = Roaster.badge;
            const card = Roaster.card;
            const empty = Roaster.empty;
            const pageHeader = Roaster.pageHeader;
            const api = Roaster.api.bind(Roaster);
            const drawIcons = Roaster.drawIcons;

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
                                '<a href="/resumes" class="inline-flex h-10 px-4 rounded-2xl text-white font-semibold items-center bg-[var(--primary)]">Go to resumes</a>',
                            );
                        drawIcons();
                        return;
                    }

                    root.innerHTML =
                        pageHeader(
                            "Insights",
                            "Patterns across all your resumes and analyses.",
                        ) +
                        '<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">' +
                        card(
                            '<div class="text-xs text-[var(--ink-muted)]">Average ATS Score</div><div class="font-display text-3xl font-semibold mt-2 tabular">' +
                            esc(d.averageScore) +
                            '</div><div class="text-xs text-[var(--ink-muted)]">/ 100</div>',
                        ) +
                        card(
                            '<div class="text-xs text-[var(--ink-muted)]">Best Score</div><div class="font-display text-3xl font-semibold mt-2 tabular">' +
                            esc(d.bestScore && d.bestScore.value) +
                            '</div><div class="text-xs text-[var(--ink-muted)] font-semibold">' +
                            esc((d.bestScore && d.bestScore.resumeTitle) || "") +
                            "</div>",
                            "bg-[var(--accent-soft)]/30",
                        ) +
                        card(
                            '<div class="text-xs text-[var(--ink-muted)]">Total Analyses</div><div class="font-display text-3xl font-semibold mt-2 tabular">' +
                            esc(d.totalAnalyses) +
                            "</div>",
                        ) +
                        '</div><div class="mt-6">' +
                        card(
                            '<div class="font-display text-base font-semibold">Score Trend</div><div class="text-sm text-[var(--ink-muted)] mt-1">Every analysis you\'ve run, chronologically</div><div class="h-[260px] mt-5"><canvas id="insight-chart"></canvas></div>',
                        ) +
                        '</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">' +
                        card(
                            '<div class="font-display text-base font-semibold">Recurring Issues</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">What comes up most often across your analyses</div><div class="space-y-3">' +
                            (d.topIssues || [])
                                .map(function (x) {
                                    return (
                                        '<div class="flex items-start gap-3"><div class="h-8 w-8 rounded-xl bg-[var(--surface-2)] flex items-center justify-center">' +
                                        icon("alert-circle") +
                                        '</div><div><div class="text-sm font-medium">' +
                                        esc(x.title) +
                                        '</div><div class="mt-1">' +
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
                            '<div class="font-display text-base font-semibold">Most-Missed Keywords</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">Words ATS expected but didn\'t see</div><div class="flex flex-wrap gap-2">' +
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
                                '<div class="font-display text-base font-semibold">Your Keyword Anchors</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">Words ATS consistently sees on your resumes</div><div class="flex flex-wrap gap-2">' +
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
                            '<div class="font-display text-base font-semibold">By Resume</div><div class="text-sm text-[var(--ink-muted)] mt-1 mb-4">How each of your resumes is performing</div><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-left text-[10px] uppercase tracking-wide text-[var(--ink-muted)] border-b border-[var(--border)]"><th class="py-2">Resume</th><th class="py-2 text-right">Latest</th><th class="py-2 text-right">Best</th><th class="py-2 text-right">Improvement</th><th class="py-2 text-right">Analyses</th></tr></thead><tbody>' +
                            (d.resumePerformance || [])
                                .map(function (r) {
                                    return (
                                        "<tr onclick=\"location.href='/resumes/" +
                                        encodeURIComponent(r.resumeId) +
                                        '\'" class="border-b border-[var(--border)] hover:bg-[var(--surface-2)] cursor-pointer"><td class="py-3 font-medium">' +
                                        esc(r.title) +
                                        '</td><td class="py-3 text-right font-display tabular font-semibold">' +
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
                                        borderColor: getComputedColor("--accent"),
                                        backgroundColor: getComputedColor("--chart-accent"),
                                        fill: true,
                                        tension: 0.35,
                                        pointRadius: 3,
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
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
                                    x: { grid: { display: false } },
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