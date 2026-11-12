@extends('layouts.app')
@section('title', 'Versions - Roaster')
@section('page', 'versions')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>

            <div id="versions-page" class="page-enter">
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

            async function renderVersions() {
                var root = $("#versions-page");
                if (!root) return;
                try {
                    var d = await api("/versions");

                    var vs = d.versions || [],
                        tot = d.totals || { all: vs.length, uploads: 0, rewrites: 0 };
                    var filter = "all",
                        query = "";

                    function render() {
                        var f = vs.filter(function (v) {
                            var ok =
                                filter === "all" ||
                                (filter === "upload"
                                    ? v.source_type === "upload"
                                    : v.source_type === "rewrite");

                            var q =
                                !query ||
                                String(v.resume_title || "")
                                    .toLowerCase()
                                    .indexOf(query.toLowerCase()) >= 0 ||
                                String(v.label || "")
                                    .toLowerCase()
                                    .indexOf(query.toLowerCase()) >= 0;
                            return ok && q;
                        });

                        root.innerHTML =
                            pageHeader(
                                "Versions",
                                "Every iteration across every resume, in one place.",
                            ) +
                            '<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">' +
                            [
                                ["Total versions", tot.all, "layers"],
                                ["Uploads", tot.uploads, "file-text"],
                                ["Rewrites", tot.rewrites, "wand-2"],
                            ]
                                .map(function (x) {
                                    return card(
                                        '<div class="flex items-center gap-3"><div class="h-10 w-10 rounded-2xl bg-[var(--accent-soft)] text-[var(--accent-strong)] flex items-center justify-center">' +
                                        icon(x[2]) +
                                        '</div><div><div class="text-xs text-[var(--ink-muted)]">' +
                                        x[0] +
                                        '</div><div class="font-display text-2xl font-semibold tabular">' +
                                        esc(x[1]) +
                                        "</div></div></div>",
                                    );
                                })
                                .join("") +
                            '</div><div class="flex flex-wrap items-center justify-between gap-3 mt-6"><div class="inline-flex items-center gap-1 bg-[var(--surface)] border border-[var(--border)] p-1 rounded-full">' +
                            [
                                ["all", "All versions"],
                                ["upload", "Uploads"],
                                ["rewrite", "Rewrites"],
                            ]
                                .map(function (x) {
                                    const isActive = filter === x[0];
                                    const baseClass =
                                        "h-8 px-3.5 rounded-full text-xs font-medium transition-all ";
                                    const activeClass = isActive
                                        ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-sm"
                                        : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)]";

                                    return (
                                        '<button data-filter="' +
                                        x[0] +
                                        '" class="' +
                                        baseClass +
                                        activeClass +
                                        '">' +
                                        x[1] +
                                        "</button>"
                                    );
                                })
                                .join("") +
                            '</div><input id="version-search" value="' +
                            esc(query) +
                            '" placeholder="Search resume or version label..." class="w-full sm:w-[320px] h-10 px-4 rounded-full border border-[var(--border)] bg-[var(--surface)] outline-none text-sm"></div><div id="version-list-container" class="space-y-3 mt-5">' +
                            (f.length
                                ? f
                                    .map(function (v) {
                                        return card(
                                            '<a href="/resumes/' +
                                            encodeURIComponent(v.resume_id) +
                                            '" class="flex items-center gap-4"><div class="h-12 w-12 rounded-2xl bg-[var(--accent-soft)] flex items-center justify-center">' +
                                            icon(
                                                v.source_type === "upload"
                                                    ? "file-text"
                                                    : "wand-2",
                                            ) +
                                            '</div><div class="flex-1 min-w-0"><div class="font-display font-semibold">' +
                                            esc(v.label || "") +
                                            '</div><div class="text-md text-[var(--ink-muted)] font-semibold truncate">' +
                                            esc(v.resume_title || "") +
                                            '</div><div class="text-xs text-[var(--ink-muted)] mt-0.5">' +
                                            (v.source_type === "upload"
                                                ? "Uploaded"
                                                : "Rewritten") +
                                            " " +
                                            relativeTime(v.created_at) +
                                            "</div></div>" +
                                            (v.score != null
                                                ? '<div class="text-right"><div class="font-display text-xl font-semibold tabular">' +
                                                esc(v.score) +
                                                '</div><div class="text-[10px] uppercase text-[var(--ink-muted)]">ATS</div></div>'
                                                : "") +
                                            badge(
                                                v.source_type || "version",
                                                v.source_type === "rewrite"
                                                    ? "accent"
                                                    : "neutral",
                                            ) +
                                            icon("chevron-right") +
                                            "</a>",
                                        );
                                    })
                                    .join("")
                                : empty(
                                    "layers",
                                    "No versions match",
                                    vs.length
                                        ? "Try a different filter or search term."
                                        : "Upload a resume to start creating versions.",
                                )) +
                            "</div>";

                        drawIcons();

                        $$("[data-filter]").forEach(function (b) {
                            b.onclick = function () {
                                filter = b.dataset.filter;
                                render();
                            };
                        });

                        const searchInput = $("#version-search");
                        searchInput.oninput = function (e) {
                            query = e.target.value;

                            var filtered = vs.filter(function (v) {
                                var ok =
                                    filter === "all" ||
                                    (filter === "upload"
                                        ? v.source_type === "upload"
                                        : v.source_type === "rewrite");

                                var q =
                                    !query ||
                                    String(v.resume_title || "")
                                        .toLowerCase()
                                        .indexOf(query.toLowerCase()) >= 0 ||
                                    String(v.label || "")
                                        .toLowerCase()
                                        .indexOf(query.toLowerCase()) >= 0;
                                return ok && q;
                            });

                            const listContainer = document.getElementById("version-list-container");
                            if (listContainer) {
                                listContainer.innerHTML = filtered.length
                                    ? filtered
                                        .map(function (v) {
                                            return card(
                                                '<a href="/resumes/' +
                                                encodeURIComponent(v.resume_id) +
                                                '" class="flex items-center gap-4"><div class="h-12 w-12 rounded-2xl bg-[var(--accent-soft)] flex items-center justify-center">' +
                                                icon(
                                                    v.source_type === "upload"
                                                        ? "file-text"
                                                        : "wand-2",
                                                ) +
                                                '</div><div class="flex-1 min-w-0"><div class="font-display font-semibold">' +
                                                esc(v.label || "") +
                                                '</div><div class="text-md text-[var(--ink-muted)] font-semibold truncate">' +
                                                esc(v.resume_title || "") +
                                                '</div><div class="text-xs text-[var(--ink-muted)] mt-0.5">' +
                                                (v.source_type === "upload"
                                                    ? "Uploaded"
                                                    : "Rewritten") +
                                                " " +
                                                relativeTime(v.created_at) +
                                                "</div></div>" +
                                                (v.score != null
                                                    ? '<div class="text-right"><div class="font-display text-xl font-semibold tabular">' +
                                                    esc(v.score) +
                                                    '</div><div class="text-[10px] uppercase text-[var(--ink-muted)]">ATS</div></div>'
                                                    : "") +
                                                badge(
                                                    v.source_type || "version",
                                                    v.source_type === "rewrite"
                                                        ? "accent"
                                                        : "neutral",
                                                ) +
                                                icon("chevron-right") +
                                                "</a>",
                                            );
                                        })
                                        .join("")
                                    : empty(
                                        "layers",
                                        "No versions match",
                                        vs.length
                                            ? "Try a different filter or search term."
                                            : "Upload a resume to start creating versions.",
                                    );

                                drawIcons();
                            }
                        };
                    }
                    render();
                } catch (e) {
                    root.innerHTML = empty("layers", "Couldn't load versions", e.message);
                    drawIcons();
                }
            }

            renderVersions();
        });
    </script>
@endpush