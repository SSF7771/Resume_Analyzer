@extends('layouts.app')
@section('title', 'History - Roaster')
@section('page', 'history')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>

            <div id="history-page" class="page-enter">
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

            async function renderHistory() {
                var root = $("#history-page");
                if (!root) return;
                try {
                    var d = await api("/history");
                    var events = d.events || [],
                        tot = d.totals || { all: events.length },
                        filter = "all";

                    function render() {
                        var f =
                            filter === "all"
                                ? events
                                : events.filter(function (e) {
                                    return e.type === filter;
                                });

                        var groups = {};
                        f.forEach(function (e) {
                            var k = new Date(e.at).toLocaleDateString(undefined, {
                                weekday: "long",
                                month: "long",
                                day: "numeric",
                                year:
                                    new Date(e.at).getFullYear() !==
                                        new Date().getFullYear()
                                        ? "numeric"
                                        : undefined,
                            });
                            if (!groups[k]) groups[k] = [];
                            groups[k].push(e);
                        });

                        var filters = [
                            ["all", "All", "history"],
                            ["upload", "Uploads", "upload"],
                            ["analyze", "Analyses", "sparkles"],
                            ["rewrite", "Rewrites", "pen-line"],
                        ];

                        root.innerHTML =
                            pageHeader(
                                "History",
                                "Everything you've done across your resumes, in time order.",
                            ) +
                            '<div class="inline-flex items-center gap-1 bg-[var(--surface)] border border-[var(--border)] p-1 rounded-full">' +
                            filters
                                .map(function (x) {
                                    const isActive = filter === x[0];
                                    const baseClass =
                                        "h-8 px-3.5 rounded-full text-xs font-medium inline-flex items-center gap-1.5 transition-all ";
                                    const activeClass = isActive
                                        ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-sm"
                                        : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)]";

                                    return (
                                        '<button data-hfilter="' +
                                        x[0] +
                                        '" class="' +
                                        baseClass +
                                        activeClass +
                                        '">' +
                                        icon(x[2], "w-3.5 h-3.5") +
                                        x[1] +
                                        ' <span class="tabular text-[10px] px-1.5 py-0.5 rounded-full bg-[var(--surface-2)]">' +
                                        esc(tot[x[0]] == null ? events.length : tot[x[0]]) +
                                        "</span></button>"
                                    );
                                })
                                .join("") +
                            '</div><div class="space-y-7 mt-6">' +
                            (Object.keys(groups).length === 0
                                ? empty("history", "No history found", "Your activity timeline will appear here once you start interacting with your resumes.")
                                : Object.keys(groups)
                                    .map(function (day) {
                                        var items = groups[day];
                                        return (
                                            '<div><div class="flex items-center gap-3 mb-3"><h3 class="text-xs uppercase tracking-wide font-semibold text-[var(--ink-muted)]">' +
                                            esc(day) +
                                            '</h3><div class="flex-1 h-px bg-[var(--border)]"></div><span class="text-[10px] text-[var(--ink-muted)]">' +
                                            items.length +
                                            "</span></div>" +
                                            card(
                                                items
                                                    .map(function (e, i) {
                                                        return (
                                                            '<a href="' +
                                                            (e.resumeId
                                                                ? "/resumes/" +
                                                                encodeURIComponent(
                                                                    e.resumeId,
                                                                )
                                                                : "#") +
                                                            '" class="flex items-start gap-3 px-5 py-3.5 ' +
                                                            (i
                                                                ? "border-t border-[var(--border)]"
                                                                : "") +
                                                            '"><div class="h-9 w-9 rounded-xl bg-[var(--surface-2)] flex items-center justify-center text-[var(--ink-muted)]">' +
                                                            icon(
                                                                e.type === "upload"
                                                                    ? "upload"
                                                                    : e.type === "rewrite"
                                                                        ? "pen-line"
                                                                        : "sparkles",
                                                            ) +
                                                            '</div><div class="flex-1"><div class="text-sm font-medium">' +
                                                            esc(e.title || "") +
                                                            '</div><div class="text-xs text-[var(--ink-muted)] mt-0.5">' +
                                                            esc(e.subtitle || "") +
                                                            '</div></div><div class="text-right">' +
                                                            badge(
                                                                e.label || e.type,
                                                                e.type === "rewrite"
                                                                    ? "warning"
                                                                    : e.type === "analyze"
                                                                        ? "accent"
                                                                        : "neutral",
                                                            ) +
                                                            '<div class="text-[10px] text-[var(--ink-muted)] mt-1">' +
                                                            relativeTime(e.at) +
                                                            "</div></div></a>"
                                                        );
                                                    })
                                                    .join(""),
                                                "!p-0 overflow-hidden",
                                            ) +
                                            "</div>"
                                        );
                                    })
                                    .join("")) +
                            "</div>";

                        drawIcons();
                        $$("[data-hfilter]").forEach(function (b) {
                            b.onclick = function () {
                                filter = b.dataset.hfilter;
                                render();
                            };
                        });
                    }
                    render();
                } catch (e) {
                    root.innerHTML = empty("history", "Couldn't load history", e.message);
                    drawIcons();
                }
            }

            renderHistory();
        });
    </script>
@endpush