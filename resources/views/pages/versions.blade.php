@extends('layouts.app')
@section('title', 'Versions - Roaster')
@section('page', 'versions')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 min-w-0 px-4 sm:px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
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
                    var vs = d.versions || [];
                    var tot = d.totals || { all: vs.length, uploads: 0, rewrites: 0 };
                    var filter = "all";
                    var query = "";

                    function scoreTone(n) {
                        n = Number(n);
                        if (n >= 80) return "var(--success)";
                        if (n >= 65) return "var(--accent)";
                        if (n >= 50) return "var(--warning)";
                        return "var(--danger)";
                    }

                    function matches(v) {
                        var ok = filter === "all" || (filter === "upload" ? v.source_type === "upload" : v.source_type === "rewrite");
                        var q = query.toLowerCase();
                        var hit = !q || String(v.resume_title || "").toLowerCase().indexOf(q) >= 0 || String(v.label || "").toLowerCase().indexOf(q) >= 0;
                        return ok && hit;
                    }

                    function row(v) {
                        var isUpload = v.source_type === "upload";
                        var score = v.score != null ? Number(v.score) : null;
                        return `
                            <a href="/resumes/${encodeURIComponent(v.resume_id)}" data-spotlight
                                class="roaster-card card-interactive group flex items-center gap-4 rounded-[22px] p-4 sm:p-5">
                                <div class="h-12 w-12 rounded-2xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:rotate-[-6deg] ${isUpload ? "bg-[var(--accent-soft)] text-[var(--accent-strong)]" : "brand-logo"}">
                                    ${icon(isUpload ? "file-text" : "wand-2", "w-5 h-5")}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-display font-bold">${esc(v.label || "")}</span>
                                        ${badge(isUpload ? "upload" : "rewrite", isUpload ? "neutral" : "accent")}
                                    </div>
                                    <div class="text-sm font-semibold text-[var(--muted-foreground)] truncate mt-0.5">${esc(v.resume_title || "")}</div>
                                    <div class="text-xs text-[var(--muted-foreground)] mt-0.5">${isUpload ? "Uploaded" : "Rewritten"} ${relativeTime(v.created_at)}</div>
                                </div>
                                ${score != null ? `
                                    <div class="hidden sm:block w-32">
                                        <div class="flex items-baseline justify-between">
                                            <span class="text-[10px] uppercase font-bold tracking-wider text-[var(--muted-foreground)]">ATS</span>
                                            <span class="font-display text-xl font-extrabold tabular">${esc(score)}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-[var(--secondary)] overflow-hidden mt-1.5">
                                            <span class="block h-full rounded-full origin-left animate-[growX_1s_cubic-bezier(0.16,1,0.3,1)_both]" style="width:${Math.max(0, Math.min(100, score))}%;background:${scoreTone(score)}"></span>
                                        </div>
                                    </div>
                                    <div class="sm:hidden font-display text-xl font-extrabold tabular" style="color:${scoreTone(score)}">${esc(score)}</div>` : ""}
                                <span class="text-[var(--muted-foreground)] transition-transform duration-300 group-hover:translate-x-1">${icon("chevron-right", "w-5 h-5")}</span>
                            </a>`;
                    }

                    function renderList() {
                        var f = vs.filter(matches);
                        var list = $("#version-list-container");
                        list.innerHTML = f.length
                            ? f.map(row).join("")
                            : empty("layers", "No versions match", vs.length ? "Try a different filter or search term." : "Upload a resume to start creating versions.");
                        list.classList.remove("stagger");
                        void list.offsetWidth;
                        list.classList.add("stagger");
                        $("#version-count").textContent = f.length + " shown";
                        drawIcons();
                    }

                    function statCard(title, value, ic) {
                        var n = parseFloat(value);
                        return card(`
                            <div class="flex items-center gap-4">
                                <span class="icon-tile h-12 w-12 rounded-2xl">${icon(ic, "w-5 h-5")}</span>
                                <div>
                                    <div class="text-xs font-semibold text-[var(--muted-foreground)]">${title}</div>
                                    <div class="font-display text-3xl font-extrabold tabular">${isNaN(n) ? esc(value ?? "-") : `<span data-count="${n}">0</span>`}</div>
                                </div>
                            </div>`, "card-interactive");
                    }

                    root.innerHTML =
                        pageHeader("Versions", "Every iteration across every resume, in one place.") +
                        `<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 stagger">
                            ${statCard("Total versions", tot.all, "layers")}
                            ${statCard("Uploads", tot.uploads, "file-text")}
                            ${statCard("Rewrites", tot.rewrites, "wand-2")}
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-7">
                            <div class="flex items-center gap-3">
                                <div class="inline-flex items-center gap-1 bg-[var(--card)] border border-[var(--border)] p-1.5 rounded-full shadow-card">
                                    ${[["all", "All"], ["upload", "Uploads"], ["rewrite", "Rewrites"]].map((x) => `
                                        <button data-filter="${x[0]}" class="h-9 px-4 rounded-full text-xs font-bold transition-all duration-300">${x[1]}</button>`).join("")}
                                </div>
                                <span id="version-count" class="text-xs font-semibold text-[var(--muted-foreground)]"></span>
                            </div>
                            <div class="relative sm:w-[320px]">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--muted-foreground)]">${icon("search", "w-4 h-4")}</span>
                                <input id="version-search" placeholder="Search resume or version…" class="w-full h-11 pl-10 pr-4 rounded-full border border-[var(--border)] bg-[var(--card)] outline-none text-sm">
                            </div>
                        </div>
                        <div id="version-list-container" class="space-y-3 mt-5"></div>`;

                    function paintFilters() {
                        $$("[data-filter]").forEach(function (b) {
                            var on = b.dataset.filter === filter;
                            b.className = "h-9 px-4 rounded-full text-xs font-bold transition-all duration-300 " +
                                (on ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-card" : "text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)]");
                        });
                    }

                    $$("[data-filter]").forEach(function (b) {
                        b.onclick = function () {
                            filter = b.dataset.filter;
                            paintFilters();
                            renderList();
                        };
                    });
                    $("#version-search").oninput = function (e) {
                        query = e.target.value;
                        renderList();
                    };
                    paintFilters();
                    renderList();
                } catch (e) {
                    root.innerHTML = empty("layers", "Couldn't load versions", e.message);
                    drawIcons();
                }
            }

            renderVersions();
        });
    </script>
@endpush