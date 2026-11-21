@extends('layouts.app')
@section('title', 'Resume - Roaster')
@section('page', 'resume-detail')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 min-w-0 px-4 sm:px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>
            <div id="resume-detail-page" class="page-enter">
                <!-- Loading skeleton (shaped like the real page) -->
                <div class="animate-pulse space-y-6">
                    <div class="h-4 w-10 rounded-full bg-[var(--surface-2)]"></div>
                    <div class="h-10 w-72 rounded-2xl bg-[var(--surface-2)]"></div>
                    <div class="h-40 rounded-3xl bg-[var(--surface-2)]"></div>
                    <div class="grid lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-4 h-72 rounded-3xl bg-[var(--surface-2)]"></div>
                        <div class="lg:col-span-5 h-72 rounded-3xl bg-[var(--surface-2)]"></div>
                        <div class="lg:col-span-3 h-72 rounded-3xl bg-[var(--surface-2)]"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- AI analysis progress overlay -->
    <div id="analyze-overlay" class="hidden fixed inset-0 z-[90] bg-black/45 backdrop-blur-sm flex items-center justify-center p-4 no-print" role="dialog" aria-modal="true" aria-labelledby="analyze-title">
        <div class="w-full max-w-md rounded-[28px] border border-[var(--border)] bg-[var(--card)] shadow-hover p-7 page-enter">
            <div class="flex items-center gap-4">
                <div class="relative h-14 w-14 shrink-0">
                    <div class="absolute inset-0 rounded-2xl bg-brand opacity-40 blur-lg animate-pulse"></div>
                    <div class="relative h-14 w-14 rounded-2xl brand-logo flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-6 h-6 animate-[spin_3s_linear_infinite]"></i>
                    </div>
                </div>
                <div>
                    <div id="analyze-title" class="font-display text-lg font-extrabold tracking-tight">Analyzing your resume</div>
                    <div class="text-xs text-[var(--muted-foreground)] mt-0.5" id="analyze-role-label">This usually takes 10–30 seconds</div>
                </div>
            </div>

            <div class="mt-6 h-2 rounded-full bg-[var(--secondary)] overflow-hidden">
                <div id="analyze-progress" class="h-full w-0 rounded-full bg-brand transition-[width] duration-700 ease-out"></div>
            </div>

            <ol class="mt-6 space-y-3" id="analyze-steps">
                @foreach ([['file-search', 'Reading your resume'], ['layout-template', 'Checking ATS formatting'], ['key-round', 'Matching role keywords'], ['wand-2', 'Writing bullet rewrites']] as $i => $step)
                    <li data-step="{{ $i }}" class="flex items-center gap-3 text-sm text-[var(--muted-foreground)] transition-colors">
                        <span class="step-dot h-8 w-8 rounded-xl border border-[var(--border)] flex items-center justify-center shrink-0 transition-all">
                            <i data-lucide="{{ $step[0] }}" class="w-4 h-4"></i>
                        </span>
                        <span class="flex-1">{{ $step[1] }}</span>
                        <span class="step-state text-[10px] font-bold uppercase tracking-wider"></span>
                    </li>
                @endforeach
            </ol>

            <div class="mt-6 rounded-2xl bg-[var(--surface-2)] p-4 text-xs text-[var(--muted-foreground)] flex gap-3">
                <i data-lucide="lightbulb" class="w-4 h-4 shrink-0 text-[var(--warning)]"></i>
                <span id="analyze-tip">Tip: bullets that start with a strong verb and end with a number score higher.</span>
            </div>
        </div>
    </div>

    @include('components.layout.CommandPalette')
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const $ = Roaster.$;
            const $$ = Roaster.$$;
            const esc = Roaster.esc;
            const icon = Roaster.icon;
            const card = Roaster.card;
            const relativeTime = Roaster.relativeTime;
            const drawIcons = Roaster.drawIcons;
            const empty = Roaster.empty.bind(Roaster);
            const badge = Roaster.badge.bind(Roaster);
            const pageHeader = Roaster.pageHeader.bind(Roaster);
            const toast = Roaster.toast.bind(Roaster);
            const api = Roaster.api.bind(Roaster);

            // ---------- small view helpers ----------
            const label = (txt) => `<div class="text-[11px] font-bold uppercase tracking-wider text-[var(--muted-foreground)]">${txt}</div>`;

            function band(score) {
                const n = Number(score);
                if (isNaN(n)) return { tone: "var(--muted-foreground)", text: "Not scored", emoji: "" };
                if (n >= 80) return { tone: "var(--success)", text: "Excellent", emoji: "🔥" };
                if (n >= 65) return { tone: "var(--accent)", text: "Good", emoji: "👍" };
                if (n >= 50) return { tone: "var(--warning)", text: "Needs work", emoji: "🛠️" };
                return { tone: "var(--danger)", text: "Critical", emoji: "⚠️" };
            }

            const metricIcons = {
                format: "layout-template", formatting: "layout-template", structure: "layout-template",
                keywords: "key-round", keyword: "key-round", impact: "trending-up", achievements: "trophy",
                clarity: "sparkles", readability: "book-open", experience: "briefcase", skills: "wrench",
                education: "graduation-cap", length: "ruler", grammar: "spell-check",
            };
            const metricIcon = (k) => metricIcons[String(k).toLowerCase().split("_")[0]] || "circle-dot";

            function scoreRing(score, size) {
                const n = Number(score);
                const val = isNaN(n) ? 0 : Math.max(0, Math.min(100, n));
                const b = band(score);
                return `
                    <div class="relative mx-auto" style="width:${size}px;height:${size}px">
                        <div class="absolute inset-4 rounded-full blur-2xl opacity-25" style="background:${b.tone}"></div>
                        <svg class="score-ring relative -rotate-90" style="width:${size}px;height:${size}px" viewBox="0 0 64 64" data-score-ring="${val}">
                            <circle cx="32" cy="32" r="27" fill="none" stroke="var(--secondary)" stroke-width="5"/>
                            <circle class="progress" cx="32" cy="32" r="27" fill="none" stroke="${b.tone}" stroke-width="5"
                                stroke-linecap="round" stroke-dasharray="169.6" stroke-dashoffset="169.6"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="font-display text-6xl font-extrabold tabular tracking-tighter">${isNaN(n) ? "–" : `<span data-count="${val}">0</span>`}</div>
                            <div class="text-xs font-semibold text-[var(--muted-foreground)]">out of 100</div>
                        </div>
                    </div>`;
            }

            function copyBtn(text, title) {
                return `<button type="button" data-copy="${esc(text)}" title="${title || "Copy"}" class="h-8 w-8 rounded-lg flex items-center justify-center text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)] transition-colors shrink-0">${icon("copy", "w-3.5 h-3.5")}</button>`;
            }

            // ---------- analysis progress overlay ----------
            const overlay = {
                timer: null,
                tipTimer: null,
                tips: [
                    "Tip: bullets that start with a strong verb and end with a number score higher.",
                    "Tip: mirror the exact wording from the job post for key skills.",
                    "Tip: keep it to 1–2 pages and use standard section headings.",
                    "Tip: avoid tables and columns — many ATS parsers read them out of order.",
                ],
                open(role) {
                    const el = $("#analyze-overlay");
                    el.classList.remove("hidden");
                    $("#analyze-role-label").textContent = role ? `Target role: ${role}` : "This usually takes 10–30 seconds";
                    let step = 0;
                    this.setStep(0);
                    // Steps advance on a timer while we wait for the API; the last step waits for the real response.
                    this.timer = setInterval(() => {
                        if (step < 3) this.setStep(++step);
                    }, 2600);
                    let t = 0;
                    this.tipTimer = setInterval(() => {
                        t = (t + 1) % this.tips.length;
                        const tip = $("#analyze-tip");
                        tip.style.opacity = 0;
                        setTimeout(() => { tip.textContent = this.tips[t]; tip.style.opacity = 1; }, 200);
                    }, 4500);
                    drawIcons();
                },
                setStep(i) {
                    $$("#analyze-steps [data-step]").forEach((li) => {
                        const n = Number(li.dataset.step);
                        const dot = li.querySelector(".step-dot");
                        const state = li.querySelector(".step-state");
                        li.classList.toggle("text-[var(--foreground)]", n <= i);
                        li.classList.toggle("font-semibold", n === i);
                        dot.style.background = n < i ? "var(--success)" : n === i ? "var(--primary)" : "";
                        dot.style.color = n <= i ? (n < i ? "#fff" : "var(--primary-foreground)") : "";
                        dot.style.borderColor = n <= i ? "transparent" : "";
                        state.textContent = n < i ? "Done" : n === i ? "…" : "";
                        state.style.color = n < i ? "var(--success)" : "";
                    });
                    $("#analyze-progress").style.width = Math.min(92, 12 + i * 26) + "%";
                },
                async close(ok) {
                    clearInterval(this.timer);
                    clearInterval(this.tipTimer);
                    if (ok) {
                        this.setStep(4);
                        $("#analyze-progress").style.width = "100%";
                        await new Promise((r) => setTimeout(r, 650));
                    }
                    $("#analyze-overlay").classList.add("hidden");
                },
            };

            // ---------- page ----------
            async function renderResumeDetail() {
                const root = $("#resume-detail-page");
                if (!root) return;
                const id = getPageId();

                try {
                    const d = await api("/resumes/" + encodeURIComponent(id));
                    const resume = d.resume || (d.data && d.data.resume) || d;
                    const versions = d.versions || (d.data && d.data.versions) || [];
                    window.__resumeVersions = versions;

                    let active = resume.current_version_id || (versions[versions.length - 1] && versions[versions.length - 1].id);
                    let analysis = null;
                    let tab = "score";
                    let targetRole = "";
                    let previousScore = null;

                    async function loadAnalysis() {
                        if (!active) {
                            analysis = null;
                            render();
                            return;
                        }
                        try {
                            const a = await api("/resumes/" + encodeURIComponent(id) + "/versions/" + encodeURIComponent(active) + "/analysis");
                            analysis = a.analysis || (a.data && a.data.analysis) || a;
                        } catch (e) {
                            analysis = null;
                        }
                        render();
                    }

                    // ----- tab contents -----
                    function issuesTab() {
                        const items = analysis.issues || [];
                        if (!items.length)
                            return card(`<div class="py-10 text-center"><div class="text-4xl">🎉</div><div class="font-display font-bold mt-3">No issues found</div><div class="text-sm text-[var(--muted-foreground)] mt-1">This version looks clean.</div></div>`);
                        const sevTone = { high: "var(--danger)", critical: "var(--danger)", medium: "var(--warning)", low: "var(--accent)" };
                        return card(`
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-display text-lg font-bold tracking-tight">Issues & recommendations</div>
                                    <div class="text-sm text-[var(--muted-foreground)] mt-0.5">Click an issue to see why it matters.</div>
                                </div>
                                ${badge(items.length + " to fix", "warning")}
                            </div>
                            <div class="mt-5 space-y-2.5 stagger" data-accordion-list>
                                ${items.map((x, i) => {
                                    const tone = sevTone[String(x.severity || "").toLowerCase()] || "var(--warning)";
                                    return `
                                    <div class="issue rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/50 overflow-hidden transition-colors hover:border-[var(--primary)]/30">
                                        <button type="button" class="issue-trigger w-full flex items-center gap-4 p-4 text-left" aria-expanded="${i === 0}">
                                            <span class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0 font-display text-sm font-extrabold" style="background:color-mix(in oklch, ${tone} 14%, transparent);color:${tone}">${i + 1}</span>
                                            <span class="flex-1 min-w-0">
                                                <span class="block text-sm font-semibold">${esc(x.title || "Issue " + (i + 1))}</span>
                                                ${x.severity ? `<span class="text-[10px] font-bold uppercase tracking-wider" style="color:${tone}">${esc(x.severity)} priority</span>` : ""}
                                            </span>
                                            <span class="issue-chevron h-8 w-8 rounded-full bg-[var(--card)] border border-[var(--border)] flex items-center justify-center transition-transform duration-300 ${i === 0 ? "rotate-180" : ""}">${icon("chevron-down", "w-4 h-4")}</span>
                                        </button>
                                        <div class="issue-panel grid transition-[grid-template-rows] duration-300 ease-out" style="grid-template-rows:${i === 0 ? "1fr" : "0fr"}">
                                            <div class="overflow-hidden">
                                                <p class="px-4 pb-4 pl-[68px] text-sm text-[var(--muted-foreground)] leading-relaxed">${esc(x.explanation || "")}</p>
                                            </div>
                                        </div>
                                    </div>`;
                                }).join("")}
                            </div>`);
                    }

                    function strengthsTab() {
                        const s = analysis.strengths || [];
                        if (!s.length) return card(`<div class="py-10 text-center text-sm text-[var(--muted-foreground)]">No strengths recorded.</div>`);
                        return `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 stagger">
                                ${s.map((x) => {
                                    const title = typeof x === "string" ? x : x.title || x.description || "";
                                    const ev = typeof x === "string" ? "" : x.evidence || (x.title && x.description ? x.description : "");
                                    return `
                                    <div data-spotlight class="roaster-card card-interactive rounded-[22px] p-5 flex gap-4">
                                        <span class="h-10 w-10 rounded-2xl flex items-center justify-center shrink-0 bg-[var(--success)]/12 text-[var(--success)]">${icon("check", "w-5 h-5")}</span>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold leading-relaxed">${esc(title)}</div>
                                            ${ev ? `<div class="text-xs text-[var(--muted-foreground)] leading-relaxed mt-1.5">${esc(ev)}</div>` : ""}
                                        </div>
                                    </div>`;
                                }).join("")}
                            </div>`;
                    }

                    function keywordsTab() {
                        const present = (analysis.keywords_present || []).map((x) => (typeof x === "string" ? x : x.keyword));
                        const missing = (analysis.keywords_missing || []).map((x) => (typeof x === "string" ? x : x.keyword));
                        const total = present.length + missing.length;
                        const pct = total ? Math.round((present.length / total) * 100) : 0;
                        const chip = (k, ok) => `
                            <button type="button" data-copy="${esc(k)}" title="Click to copy"
                                class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold transition-all hover:-translate-y-0.5 ${ok
                                    ? "bg-[var(--success)]/10 text-[var(--success)] border-[var(--success)]/25"
                                    : "bg-[var(--danger)]/8 text-[var(--danger)] border-[var(--danger)]/25"}">
                                ${icon(ok ? "check" : "plus", "w-3 h-3")}${esc(k)}
                                <span class="opacity-0 group-hover:opacity-60 transition-opacity">${icon("copy", "w-3 h-3")}</span>
                            </button>`;
                        return `
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                                <div class="lg:col-span-4">
                                    ${card(`
                                        ${label("Keyword coverage")}
                                        <div class="flex items-end gap-2 mt-4">
                                            <div class="font-display text-5xl font-extrabold tabular"><span data-count="${pct}">0</span>%</div>
                                        </div>
                                        <div class="bar mt-4"><span style="width:${pct}%"></span></div>
                                        <div class="grid grid-cols-2 gap-3 mt-5">
                                            <div class="rounded-2xl bg-[var(--success)]/10 p-3">
                                                <div class="font-display text-2xl font-extrabold text-[var(--success)] tabular">${present.length}</div>
                                                <div class="text-[11px] font-semibold text-[var(--muted-foreground)]">matched</div>
                                            </div>
                                            <div class="rounded-2xl bg-[var(--danger)]/10 p-3">
                                                <div class="font-display text-2xl font-extrabold text-[var(--danger)] tabular">${missing.length}</div>
                                                <div class="text-[11px] font-semibold text-[var(--muted-foreground)]">missing</div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-[var(--muted-foreground)] mt-4 leading-relaxed">Tip: click a missing keyword to copy it, then work it naturally into a bullet.</p>
                                    `)}
                                </div>
                                <div class="lg:col-span-8 space-y-5">
                                    ${card(`
                                        <div class="flex items-center justify-between">
                                            <div class="font-display text-base font-bold">Missing keywords</div>
                                            <input type="search" id="kw-filter" placeholder="Filter…" class="h-9 w-36 sm:w-48 px-3 rounded-xl border border-[var(--border)] bg-[var(--surface-2)] text-xs outline-none">
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-4 stagger" data-kw-list>${missing.length ? missing.map((k) => chip(k, false)).join("") : `<span class="text-sm text-[var(--muted-foreground)]">Nothing missing 🎯</span>`}</div>
                                    `)}
                                    ${card(`
                                        <div class="font-display text-base font-bold">Matched keywords</div>
                                        <div class="flex flex-wrap gap-2 mt-4 stagger" data-kw-list>${present.length ? present.map((k) => chip(k, true)).join("") : `<span class="text-sm text-[var(--muted-foreground)]">No matches yet.</span>`}</div>
                                    `)}
                                </div>
                            </div>`;
                    }

                    function rewritesTab() {
                        const rw = analysis.bullet_rewrites || [];
                        window.__analysisId = analysis.id || null;
                        if (!rw.length) return card(`<div class="py-10 text-center text-sm text-[var(--muted-foreground)]">No rewrites suggested.</div>`);
                        return card(`
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-[var(--border)]">
                                <div>
                                    <div class="font-display text-lg font-bold tracking-tight">Suggested bullet rewrites</div>
                                    <div class="text-sm text-[var(--muted-foreground)] mt-0.5">Pick the ones you like — applying them creates a new version, your original stays safe.</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="rw-select-all" class="h-9 px-3 rounded-xl text-xs font-semibold border border-[var(--border)] hover:bg-[var(--secondary)] transition-colors">Select all</button>
                                    <button type="button" id="rw-select-none" class="h-9 px-3 rounded-xl text-xs font-semibold border border-[var(--border)] hover:bg-[var(--secondary)] transition-colors">Clear</button>
                                </div>
                            </div>
                            <div class="space-y-3 mt-5 stagger">
                                ${rw.map((r, i) => `
                                    <label class="rewrite-card group block rounded-2xl border-2 border-[var(--border)] bg-[var(--surface-2)]/40 p-5 cursor-pointer transition-all duration-300 hover:-translate-y-0.5">
                                        <div class="flex items-start gap-4">
                                            <span class="relative mt-0.5 shrink-0">
                                                <input type="checkbox" class="rewrite-check peer sr-only" value="${esc(r.id || i)}" checked>
                                                <span class="h-6 w-6 rounded-lg border-2 border-[var(--border)] flex items-center justify-center transition-all peer-checked:bg-[var(--primary)] peer-checked:border-[var(--primary)] peer-checked:text-[var(--primary-foreground)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--ring)] text-transparent">${icon("check", "w-3.5 h-3.5")}</span>
                                            </span>
                                            <div class="flex-1 min-w-0 grid md:grid-cols-2 gap-3 md:gap-5">
                                                <div>
                                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--muted-foreground)] flex items-center gap-1.5">${icon("x-circle", "w-3 h-3")} Before</div>
                                                    <div class="text-sm text-[var(--muted-foreground)] line-through decoration-[var(--danger)]/50 leading-relaxed mt-1.5">${esc(r.original || r.text || "")}</div>
                                                </div>
                                                <div class="relative md:pl-5 md:border-l border-[var(--border)]">
                                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--success)] flex items-center gap-1.5">${icon("sparkles", "w-3 h-3")} After</div>
                                                    <div class="text-sm font-semibold leading-relaxed mt-1.5 pr-8">${esc(r.rewritten || r.text || "")}</div>
                                                    <div class="absolute top-0 right-0" onclick="event.preventDefault()">${copyBtn(r.rewritten || r.text || "", "Copy rewrite")}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>`).join("")}
                            </div>
                            <div class="sticky bottom-24 md:bottom-4 z-10 mt-6">
                                <div class="glass rounded-2xl p-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-hover">
                                    <div class="text-sm font-semibold px-2"><span id="rw-count">${rw.length}</span> of ${rw.length} selected</div>
                                    <div class="flex gap-2">
                                        <button id="apply-selected" class="roaster-secondary-button flex-1 sm:flex-none h-11 px-5 rounded-xl font-semibold text-sm">Apply selected</button>
                                        <button id="apply-all" class="roaster-primary-button flex-1 sm:flex-none h-11 px-5 rounded-xl font-semibold text-sm inline-flex items-center justify-center gap-2">${icon("git-branch-plus", "w-4 h-4")} Apply all → new version</button>
                                    </div>
                                </div>
                            </div>`);
                    }

                    function analysisContent() {
                        if (!analysis) return "";
                        if (tab === "score") return issuesTab();
                        if (tab === "strengths") return strengthsTab();
                        if (tab === "keywords") return keywordsTab();
                        if (tab === "rewrites") return rewritesTab();
                        if (tab === "diff") {
                            if (versions.length < 2) return empty("git-compare", "Need two versions to compare", "Apply rewrites or upload a new version to see diffs here.");
                            return '<div id="full-diff-panel"></div>';
                        }
                        return "";
                    }

                    // ----- parsed resume (collapsible) -----
                    function parsed(v) {
                        if (!v) return "";
                        const s = v.parsed_sections || {};
                        const b = s.basics || {};
                        let body = "";
                        if (b.name || b.title || b.email || b.phone || b.location) {
                            body += `
                                <div class="flex items-center gap-4 p-5 rounded-2xl bg-brand-soft border border-[var(--border)]">
                                    <div class="h-14 w-14 rounded-2xl brand-logo flex items-center justify-center font-display text-xl font-extrabold shrink-0">${esc((b.name || "?")[0].toUpperCase())}</div>
                                    <div class="min-w-0">
                                        ${b.name ? `<div class="font-display text-xl font-extrabold tracking-tight">${esc(b.name)}</div>` : ""}
                                        ${b.title ? `<div class="text-sm font-semibold text-[var(--accent-strong)]">${esc(b.title)}</div>` : ""}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            ${[[b.email, "mail"], [b.phone, "phone"], [b.location, "map-pin"]].filter((x) => x[0]).map((x) => `<span class="inline-flex items-center gap-1.5 text-xs bg-[var(--card)] px-2.5 py-1 rounded-lg border border-[var(--border)]">${icon(x[1], "w-3 h-3")}${esc(x[0])}</span>`).join("")}
                                        </div>
                                    </div>
                                </div>`;
                        }
                        if (s.summary)
                            body += `<div>${label("Professional summary")}<p class="text-sm leading-relaxed mt-2">${esc(s.summary)}</p></div>`;
                        if (s.experience && s.experience.length)
                            body += `
                                <div>
                                    <div class="flex items-center justify-between">${label("Experience")}<span class="text-xs text-[var(--muted-foreground)]">${s.experience.length} positions</span></div>
                                    <ol class="relative mt-4 ml-2 border-l-2 border-[var(--border)] space-y-5">
                                        ${s.experience.slice(0, 6).map((e) => `
                                            <li class="relative pl-6">
                                                <span class="absolute -left-[7px] top-1.5 h-3 w-3 rounded-full bg-[var(--card)] border-2 border-[var(--primary)]"></span>
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                                    <div>
                                                        <div class="text-sm font-semibold">${esc(e.role || "")}</div>
                                                        ${e.company ? `<div class="text-xs font-semibold text-[var(--accent-strong)]">${esc(e.company)}</div>` : ""}
                                                    </div>
                                                    ${e.period ? `<span class="text-[11px] px-2 py-0.5 rounded-md bg-[var(--secondary)] text-[var(--muted-foreground)] font-semibold self-start">${esc(e.period)}</span>` : ""}
                                                </div>
                                            </li>`).join("")}
                                    </ol>
                                </div>`;
                        if (s.education && s.education.length)
                            body += `
                                <div>${label("Education")}
                                    <div class="grid sm:grid-cols-2 gap-3 mt-3">
                                        ${s.education.map((e) => `
                                            <div class="flex items-start gap-3 p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/40">
                                                <span class="icon-tile h-9 w-9 rounded-xl shrink-0">${icon("graduation-cap", "w-4 h-4")}</span>
                                                <div><div class="text-sm font-semibold">${esc(e.degree || "")}</div>${e.school ? `<div class="text-xs text-[var(--muted-foreground)]">${esc(e.school)}</div>` : ""}</div>
                                            </div>`).join("")}
                                    </div>
                                </div>`;
                        if (s.skills && s.skills.length)
                            body += `<div>${label("Skills")}<div class="flex flex-wrap gap-2 mt-3">${s.skills.slice(0, 30).map((x) => badge(x, "neutral")).join("")}</div></div>`;
                        if (s.projects && s.projects.length)
                            body += `
                                <div>${label("Projects")}
                                    <div class="space-y-2.5 mt-3">
                                        ${s.projects.slice(0, 5).map((p) => `
                                            <div class="p-3.5 rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                <div class="text-sm font-semibold">${esc(p.name || "")}</div>
                                                ${p.tech && p.tech.length ? `<div class="text-[11px] px-2.5 py-1 rounded-lg bg-[var(--card)] border border-[var(--border)] text-[var(--accent-strong)] font-semibold">${esc(p.tech.join(" · "))}</div>` : ""}
                                            </div>`).join("")}
                                    </div>
                                </div>`;

                        return `
                            <details class="group roaster-card rounded-[22px] overflow-hidden" open>
                                <summary class="list-none cursor-pointer flex items-center justify-between gap-4 p-5 select-none">
                                    <div class="flex items-center gap-3">
                                        <span class="icon-tile h-10 w-10 rounded-xl">${icon("file-text", "w-5 h-5")}</span>
                                        <div>
                                            <div class="font-display text-base font-bold">Parsed resume</div>
                                            <div class="text-xs text-[var(--muted-foreground)]">What the ATS extracted from <span class="font-semibold text-[var(--foreground)]">${esc(v.label || "this version")}</span></div>
                                        </div>
                                    </div>
                                    <span class="h-9 w-9 rounded-full border border-[var(--border)] flex items-center justify-center transition-transform duration-300 group-open:rotate-180">${icon("chevron-down", "w-4 h-4")}</span>
                                </summary>
                                <div class="px-5 pb-6 space-y-6 border-t border-[var(--border)] pt-6">${body || `<div class="text-sm text-[var(--muted-foreground)]">No sections were parsed.</div>`}</div>
                            </details>`;
                    }

                    // ----- main render -----
                    function render() {
                        window.__analysisId = (analysis && analysis.id) || null;
                        const v = versions.find((x) => String(x.id) === String(active));

                        const actions = `
                            <div class="flex items-center gap-2">
                                <a href="/resumes" class="roaster-secondary-button inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-semibold">${icon("arrow-left")} <span class="hidden sm:inline">All resumes</span></a>
                                <a href="/resumes/${encodeURIComponent(id)}/export" class="roaster-primary-button inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-semibold">${icon("download")} Export PDF</a>
                            </div>`;

                        let html = pageHeader(
                            resume.title || "Resume",
                            "Updated " + relativeTime(resume.updated_at) + " · " + versions.length + " version" + (versions.length === 1 ? "" : "s"),
                            actions,
                        );

                        // Analyze control card
                        const roles = ["Frontend Engineer", "Backend Engineer", "Data Analyst", "Product Manager", "UI/UX Designer"];
                        html += card(`
                            <div class="grid lg:grid-cols-12 gap-6 items-center">
                                <div class="lg:col-span-5">
                                    ${label("Version")}
                                    <div class="mt-3 inline-flex flex-wrap gap-1.5 p-1.5 rounded-2xl bg-[var(--surface-2)] border border-[var(--border)]">
                                        ${versions.map((x) => `
                                            <button data-version="${esc(x.id)}" class="px-3.5 h-9 rounded-xl text-xs font-bold transition-all ${String(x.id) === String(active)
                                                ? "bg-[var(--card)] text-[var(--foreground)] shadow-card"
                                                : "text-[var(--muted-foreground)] hover:text-[var(--foreground)]"}">${esc(x.label || "Version")}</button>`).join("")}
                                    </div>
                                </div>
                                <div class="lg:col-span-7">
                                    ${label("Target role")}
                                    <div class="mt-3 flex flex-col sm:flex-row gap-3">
                                        <div class="relative flex-1">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted-foreground)]">${icon("target", "w-4 h-4")}</span>
                                            <input id="target-role" value="${esc(targetRole)}" placeholder="e.g. Senior Frontend Engineer"
                                                class="w-full h-12 pl-11 pr-4 rounded-2xl border border-[var(--border)] bg-[var(--card)] text-sm outline-none">
                                        </div>
                                        <button id="analyze-btn" class="roaster-primary-button h-12 px-6 rounded-2xl font-semibold text-sm flex items-center justify-center gap-2 shrink-0">${icon("sparkles")} ${analysis ? "Re-analyze" : "Analyze with AI"}</button>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 mt-3">
                                        ${roles.map((r) => `<button type="button" data-role="${esc(r)}" class="px-2.5 py-1 rounded-full text-[11px] font-semibold border border-[var(--border)] text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:border-[var(--primary)]/40 transition-colors">${esc(r)}</button>`).join("")}
                                    </div>
                                </div>
                            </div>`);

                        if (analysis) {
                            const b = band(analysis.ats_score);
                            const delta = previousScore != null && analysis.ats_score != null ? Number(analysis.ats_score) - Number(previousScore) : null;
                            const bd = Object.entries(analysis.score_breakdown || {});

                            html += `
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6">
                                    <div class="lg:col-span-4" data-reveal>
                                        ${card(`
                                            <div class="flex items-center justify-between">${label("ATS score")}
                                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:color-mix(in oklch, ${b.tone} 14%, transparent);color:${b.tone}">${b.emoji} ${b.text}</span>
                                            </div>
                                            <div class="py-6">${scoreRing(analysis.ats_score, 200)}</div>
                                            ${delta != null && delta !== 0 ? `<div class="text-center text-xs font-bold ${delta > 0 ? "text-[var(--success)]" : "text-[var(--danger)]"}">${delta > 0 ? "▲" : "▼"} ${Math.abs(delta)} vs. last analysis</div>` : `<div class="text-center text-xs text-[var(--muted-foreground)]">Aim for 80+ to pass most ATS filters</div>`}
                                        `, "h-full")}
                                    </div>
                                    <div class="lg:col-span-5" data-reveal style="--reveal-delay:80ms">
                                        ${card(`
                                            <div class="font-display text-base font-bold">Score breakdown</div>
                                            <div class="space-y-4 mt-5">
                                                ${bd.length ? bd.map(([k, val]) => {
                                                    const n = Math.max(0, Math.min(100, Number(val) || 0));
                                                    const t = band(n).tone;
                                                    return `
                                                    <div class="group">
                                                        <div class="flex items-center justify-between text-sm">
                                                            <span class="flex items-center gap-2 font-semibold capitalize"><span class="h-7 w-7 rounded-lg bg-[var(--secondary)] flex items-center justify-center text-[var(--muted-foreground)] group-hover:text-[var(--foreground)] transition-colors">${icon(metricIcon(k), "w-3.5 h-3.5")}</span>${esc(k.replace(/_/g, " "))}</span>
                                                            <span class="tabular font-extrabold"><span data-count="${n}">0</span></span>
                                                        </div>
                                                        <div class="h-2 rounded-full bg-[var(--secondary)] overflow-hidden mt-2">
                                                            <span class="block h-full rounded-full origin-left animate-[growX_1.2s_cubic-bezier(0.16,1,0.3,1)_both]" style="width:${n}%;background:${t}"></span>
                                                        </div>
                                                    </div>`;
                                                }).join("") : `<div class="text-sm text-[var(--muted-foreground)]">No breakdown available.</div>`}
                                            </div>
                                        `, "h-full")}
                                    </div>
                                    <div class="lg:col-span-3" data-reveal style="--reveal-delay:160ms">
                                        ${card(`
                                            <div class="flex items-center justify-between">
                                                <div class="font-display text-base font-bold">AI verdict</div>
                                                ${badge(analysis.model || "AI", "neutral")}
                                            </div>
                                            <div class="relative mt-5">
                                                <span class="absolute -top-3 -left-1 font-serif text-5xl leading-none text-[var(--border)] select-none">“</span>
                                                <p class="relative text-sm leading-relaxed">${esc(analysis.summary || "No summary provided.")}</p>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 mt-5">
                                                <button type="button" data-goto-tab="score" class="rounded-xl bg-[var(--surface-2)] hover:bg-[var(--secondary)] p-3 text-left transition-colors">
                                                    <div class="font-display text-xl font-extrabold tabular">${(analysis.issues || []).length}</div>
                                                    <div class="text-[11px] text-[var(--muted-foreground)] font-semibold">issues</div>
                                                </button>
                                                <button type="button" data-goto-tab="rewrites" class="rounded-xl bg-[var(--surface-2)] hover:bg-[var(--secondary)] p-3 text-left transition-colors">
                                                    <div class="font-display text-xl font-extrabold tabular">${(analysis.bullet_rewrites || []).length}</div>
                                                    <div class="text-[11px] text-[var(--muted-foreground)] font-semibold">rewrites</div>
                                                </button>
                                            </div>
                                        `, "h-full")}
                                    </div>
                                </div>`;

                            const kwTotal = (analysis.keywords_present || []).length + (analysis.keywords_missing || []).length;
                            const tabs = [
                                ["score", "Issues", (analysis.issues || []).length, "alert-circle"],
                                ["strengths", "Strengths", (analysis.strengths || []).length, "award"],
                                ["keywords", "Keywords", kwTotal ? `${(analysis.keywords_present || []).length}/${kwTotal}` : 0, "key-round"],
                                ["rewrites", "Rewrites", (analysis.bullet_rewrites || []).length, "wand-2"],
                            ];
                            if (versions.length >= 2) tabs.push(["diff", "Diff", "", "git-compare"]);

                            html += `
                                <div class="mt-8" id="analysis-tabs-wrap">
                                    <div class="overflow-x-auto -mx-1 px-1 pb-1">
                                        <div class="relative inline-flex items-center gap-1 bg-[var(--card)] border border-[var(--border)] p-1.5 rounded-2xl shadow-card" id="analysis-tabs" role="tablist">
                                            <span id="tab-indicator" class="absolute top-1.5 bottom-1.5 left-0 rounded-xl bg-[var(--primary)] shadow-card transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"></span>
                                            ${tabs.map((t) => `
                                                <button role="tab" data-analysis-tab="${t[0]}" aria-selected="${tab === t[0]}"
                                                    class="relative z-10 h-10 px-4 rounded-xl text-xs font-bold whitespace-nowrap inline-flex items-center gap-2 transition-colors duration-300">
                                                    ${icon(t[3], "w-3.5 h-3.5")} ${t[1]}
                                                    ${t[2] !== "" && t[2] !== 0 ? `<span class="tab-count px-1.5 py-0.5 rounded-md text-[10px] tabular">${t[2]}</span>` : ""}
                                                </button>`).join("")}
                                        </div>
                                    </div>
                                    <div id="analysis-content" class="mt-6">${analysisContent()}</div>
                                </div>`;
                        } else {
                            html += `<div class="mt-6">${empty("sparkles", "No analysis yet for this version", "Pick a target role above and run the AI analysis to get your ATS score, issues, keywords and rewrites.")}</div>`;
                        }

                        html += `<div class="mt-8" data-reveal>${parsed(v)}</div>`;
                        root.innerHTML = html;
                        drawIcons();
                        bindPage();
                    }

                    // Switch tab without re-rendering (and re-animating) the whole page
                    function setTab(next) {
                        tab = next;
                        const content = $("#analysis-content");
                        if (!content) return;
                        content.classList.remove("page-enter");
                        void content.offsetWidth;
                        content.innerHTML = analysisContent();
                        content.classList.add("page-enter");
                        drawIcons();
                        updateTabs();
                        bindContent();
                        if (tab === "diff") initDiffPanel();
                    }

                    function updateTabs() {
                        const ind = $("#tab-indicator");
                        $$("[data-analysis-tab]").forEach((b) => {
                            const on = b.dataset.analysisTab === tab;
                            b.setAttribute("aria-selected", String(on));
                            b.style.color = on ? "var(--primary-foreground)" : "var(--muted-foreground)";
                            const count = b.querySelector(".tab-count");
                            if (count) {
                                count.style.background = on ? "color-mix(in srgb, var(--primary-foreground) 20%, transparent)" : "var(--secondary)";
                            }
                            if (on && ind) {
                                ind.style.width = b.offsetWidth + "px";
                                ind.style.transform = `translateX(${b.offsetLeft}px)`;
                            }
                        });
                    }

                    function bindContent() {
                        // Issue accordion
                        $$(".issue-trigger").forEach((t) => {
                            t.onclick = () => {
                                const open = t.getAttribute("aria-expanded") === "true";
                                t.setAttribute("aria-expanded", String(!open));
                                t.nextElementSibling.style.gridTemplateRows = open ? "0fr" : "1fr";
                                t.querySelector(".issue-chevron").classList.toggle("rotate-180", !open);
                            };
                        });
                        // Keyword filter
                        const f = $("#kw-filter");
                        if (f)
                            f.oninput = () => {
                                const q = f.value.trim().toLowerCase();
                                $$("[data-kw-list] [data-copy]").forEach((c) => {
                                    c.style.display = !q || c.dataset.copy.toLowerCase().includes(q) ? "" : "none";
                                });
                            };
                        bindRewrite();
                    }

                    function bindPage() {
                        $$("[data-version]").forEach((b) => {
                            b.onclick = async () => {
                                if (b.dataset.version === String(active)) return;
                                active = b.dataset.version;
                                previousScore = null;
                                await loadAnalysis();
                            };
                        });
                        $$("[data-role]").forEach((b) => {
                            b.onclick = () => {
                                const input = $("#target-role");
                                input.value = b.dataset.role;
                                input.focus();
                            };
                        });
                        $$("[data-analysis-tab]").forEach((b) => (b.onclick = () => setTab(b.dataset.analysisTab)));
                        $$("[data-goto-tab]").forEach((b) => {
                            b.onclick = () => {
                                setTab(b.dataset.gotoTab);
                                $("#analysis-tabs-wrap").scrollIntoView({ behavior: "smooth", block: "start" });
                            };
                        });
                        requestAnimationFrame(updateTabs);
                        window.onresize = updateTabs;

                        $("#target-role").onkeydown = (e) => {
                            if (e.key === "Enter") $("#analyze-btn").click();
                        };

                        $("#analyze-btn").onclick = async function () {
                            targetRole = $("#target-role").value.trim();
                            const b = $("#analyze-btn");
                            const before = analysis && analysis.ats_score != null ? Number(analysis.ats_score) : null;
                            b.disabled = true;
                            overlay.open(targetRole);
                            try {
                                const x = await api("/resumes/" + encodeURIComponent(id) + "/analyze", {
                                    method: "POST",
                                    body: { version_id: active, target_role: targetRole || undefined },
                                });
                                analysis = x.analysis || (x.data && x.data.analysis) || x;
                                await overlay.close(true);
                                previousScore = before;
                                const score = analysis.ats_score;
                                toast("Analysis complete", "ATS score " + (score == null ? "-" : score) + " / 100", "success");
                                if (Number(score) >= 80 || (before != null && Number(score) > before)) Roaster.confetti();
                                tab = "score";
                                await loadAnalysis();
                            } catch (e) {
                                await overlay.close(false);
                                toast("Analysis failed", e.message, "error");
                            } finally {
                                const nb = $("#analyze-btn");
                                if (nb) nb.disabled = false;
                            }
                        };

                        bindContent();
                        if (tab === "diff") initDiffPanel();
                    }

                    function bindRewrite() {
                        const s = $("#apply-selected");
                        const a = $("#apply-all");
                        if (!s && !a) return;

                        const checks = $$(".rewrite-check");
                        const count = () => {
                            const n = checks.filter((c) => c.checked).length;
                            const el = $("#rw-count");
                            if (el) el.textContent = n;
                            s.disabled = n === 0;
                            s.style.opacity = n === 0 ? 0.5 : 1;
                        };
                        checks.forEach((c) => (c.onchange = count));
                        $("#rw-select-all").onclick = () => { checks.forEach((c) => (c.checked = true)); count(); };
                        $("#rw-select-none").onclick = () => { checks.forEach((c) => (c.checked = false)); count(); };

                        const apply = async (all, btn) => {
                            const ids = checks.filter((x) => x.checked).map((x) => x.value);
                            const original = btn.innerHTML;
                            btn.disabled = true;
                            btn.innerHTML = icon("loader-2", "w-4 h-4 animate-spin") + " Creating version…";
                            drawIcons();
                            try {
                                const payload = { analysis_id: window.__analysisId };
                                if (!all && ids.length) payload.rewrite_ids = ids;
                                const r = await api("/resumes/" + encodeURIComponent(id) + "/rewrite", { method: "POST", body: payload });
                                const v = r.version || (r.data && r.data.version);
                                if (v && v.id) {
                                    toast("Rewrites applied", (v.label || "New version") + " created", "success");
                                    Roaster.confetti();
                                    setTimeout(() => location.reload(), 1200);
                                    return;
                                }
                            } catch (e) {
                                toast("Couldn't apply rewrites", e.message, "error");
                            }
                            btn.disabled = false;
                            btn.innerHTML = original;
                            drawIcons();
                        };
                        s.onclick = () => apply(false, s);
                        a.onclick = () => apply(true, a);
                    }

                    await loadAnalysis();
                } catch (e) {
                    root.innerHTML = empty(
                        "file-text",
                        "Resume not found",
                        e.message,
                        '<a href="/resumes" class="roaster-secondary-button inline-flex items-center gap-2 h-10 px-4 rounded-xl text-sm font-semibold">Back to resumes</a>',
                    );
                    drawIcons();
                }
            }

            // Click-to-copy for keywords and rewrites
            document.addEventListener("click", async (e) => {
                const btn = e.target.closest("[data-copy]");
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();
                try {
                    await navigator.clipboard.writeText(btn.dataset.copy);
                    toast("Copied to clipboard", btn.dataset.copy.length > 60 ? btn.dataset.copy.slice(0, 60) + "…" : btn.dataset.copy, "info");
                } catch (err) {
                    toast("Couldn't copy", "Your browser blocked clipboard access", "error");
                }
            });

            // ---------- version diff ----------
            async function initDiffPanel() {
                const root = $("#full-diff-panel");
                if (!root) return;
                const id = getPageId();
                const versions = window.__resumeVersions || [];
                if (versions.length < 2) {
                    root.innerHTML = empty("git-compare", "Need two versions to compare", "Apply rewrites or upload a new version to see diffs here.");
                    drawIcons();
                    return;
                }
                let mode = "words";
                let fromId = versions[versions.length - 2].id;
                let toId = versions[versions.length - 1].id;

                const options = (selected, disabled) =>
                    versions.map((v) => `<option value="${esc(v.id)}" ${String(v.id) === String(selected) ? "selected" : ""} ${String(v.id) === String(disabled) ? "disabled" : ""}>${esc(v.label || "Version")}</option>`).join("");

                function shell() {
                    root.innerHTML = card(`
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div>
                                <div class="font-display text-lg font-bold">Version diff</div>
                                <div class="text-sm text-[var(--muted-foreground)] mt-0.5">See exactly what changed between two versions.</div>
                            </div>
                            <div class="inline-flex items-center gap-1 bg-[var(--surface-2)] border border-[var(--border)] p-1 rounded-xl">
                                ${["words", "lines"].map((m) => `<button type="button" data-full-diff-mode="${m}" class="h-8 px-3 rounded-lg text-xs font-bold capitalize transition-all ${mode === m ? "bg-[var(--card)] shadow-card" : "text-[var(--muted-foreground)]"}">${m}</button>`).join("")}
                            </div>
                        </div>
                        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/60 p-3 mt-5 flex items-center justify-center gap-3 flex-wrap">
                            <select id="full-diff-from" class="h-10 px-3 rounded-xl border border-[var(--border)] bg-[var(--card)] text-sm font-semibold">${options(fromId, toId)}</select>
                            <span class="h-9 w-9 rounded-full brand-logo flex items-center justify-center">${icon("arrow-right", "w-4 h-4")}</span>
                            <select id="full-diff-to" class="h-10 px-3 rounded-xl border border-[var(--border)] bg-[var(--card)] text-sm font-semibold">${options(toId, fromId)}</select>
                        </div>
                        <div id="full-diff-result" class="mt-5"></div>`);
                    drawIcons();
                    $$("[data-full-diff-mode]").forEach((b) => (b.onclick = () => { mode = b.dataset.fullDiffMode; shell(); load(); }));
                    $("#full-diff-from").onchange = function () {
                        fromId = this.value;
                        if (String(fromId) === String(toId)) { const alt = versions.find((v) => String(v.id) !== String(fromId)); if (alt) toId = alt.id; }
                        shell(); load();
                    };
                    $("#full-diff-to").onchange = function () {
                        toId = this.value;
                        if (String(fromId) === String(toId)) { const alt = versions.find((v) => String(v.id) !== String(toId)); if (alt) fromId = alt.id; }
                        shell(); load();
                    };
                }

                async function load() {
                    const box = $("#full-diff-result");
                    if (!box) return;
                    box.innerHTML = '<div class="animate-pulse"><div class="h-[260px] bg-[var(--surface-2)] rounded-2xl"></div></div>';
                    try {
                        const d = await api("/resumes/" + encodeURIComponent(id) + "/diff?from=" + encodeURIComponent(fromId) + "&to=" + encodeURIComponent(toId) + "&mode=" + encodeURIComponent(mode));
                        const stats = d.stats || {};
                        const added = Number(stats.added || 0);
                        const removed = Number(stats.removed || 0);
                        const net = added - removed;
                        const parts = d.parts || [];
                        const stat = (title, value, tone) => `
                            <div class="rounded-2xl bg-[var(--surface-2)] p-4">
                                <div class="text-[10px] uppercase tracking-wider font-bold text-[var(--muted-foreground)]">${title}</div>
                                <div class="font-display tabular text-3xl font-extrabold mt-1" style="color:${tone}">${value}</div>
                                <div class="text-[11px] text-[var(--muted-foreground)]">characters</div>
                            </div>`;
                        box.innerHTML = `
                            <div class="grid grid-cols-3 gap-3">
                                ${stat("Added", "+" + added, "var(--success)")}
                                ${stat("Removed", "−" + removed, "var(--danger)")}
                                ${stat("Net", (net > 0 ? "+" : net < 0 ? "" : "±") + net, net > 0 ? "var(--success)" : net < 0 ? "var(--danger)" : "var(--muted-foreground)")}
                            </div>
                            <div class="rounded-2xl border border-[var(--border)] bg-[var(--card)] overflow-hidden mt-4">
                                <div class="h-11 px-4 flex items-center justify-between border-b border-[var(--border)] bg-[var(--surface-2)]/60 text-[11px] text-[var(--muted-foreground)] font-semibold">
                                    <span class="flex items-center gap-1.5">${icon("file-diff", "w-3.5 h-3.5")} Inline diff · ${esc(mode)}</span>
                                    <span class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[var(--success)]/30"></span>Added</span>
                                        <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[var(--danger)]/30"></span>Removed</span>
                                    </span>
                                </div>
                                <div class="p-5 max-h-[600px] overflow-auto font-mono text-[13px] leading-[1.8] whitespace-pre-wrap">${parts.map((p) => {
                                    const cls = p.added
                                        ? "bg-[var(--success)]/15 text-[var(--success)] font-semibold rounded px-0.5"
                                        : p.removed
                                            ? "bg-[var(--danger)]/12 text-[var(--danger)] line-through rounded px-0.5"
                                            : "";
                                    return `<span class="${cls}">${esc(p.value || "")}</span>`;
                                }).join("")}</div>
                            </div>`;
                        drawIcons();
                    } catch (e) {
                        box.innerHTML = `<div class="text-xs font-medium text-[var(--danger)] bg-[var(--danger)]/10 border border-[var(--danger)]/25 rounded-xl px-4 py-3">${esc(e.message)}</div>`;
                    }
                }

                shell();
                load();
            }

            renderResumeDetail();
        });
    </script>
@endpush
