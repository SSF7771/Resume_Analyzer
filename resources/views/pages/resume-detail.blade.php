@extends('layouts.app')
@section('title', 'Resume - Roaster')
@section('page', 'resume-detail')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>
            <div id="resume-detail-page" class="page-enter">
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

            async function renderResumeDetail() {
                var root = $("#resume-detail-page");
                if (!root) return;
                var id = getPageId();
                try {
                    var d = await api("/resumes/" + encodeURIComponent(id));
                    var resume = d.resume || (d.data && d.data.resume) || d;
                    var versions = d.versions || (d.data && d.data.versions) || [];
                    window.__resumeVersions = versions;
                    var active =
                        resume.current_version_id ||
                        (versions[versions.length - 1] && versions[versions.length - 1].id);
                    var analysis = null;
                    var tab = "score";
                    var targetRole = "";

                    async function loadAnalysis() {
                        if (!active) {
                            analysis = null;
                            render();
                            return;
                        }
                        try {
                            var a = await api(
                                "/resumes/" +
                                encodeURIComponent(id) +
                                "/versions/" +
                                encodeURIComponent(active) +
                                "/analysis",
                            );
                            analysis = a.analysis || (a.data && a.data.analysis) || a;
                        } catch (e) {
                            analysis = null;
                        }

                        render();

                        const contentDiv = $("#analysis-content");
                        if (contentDiv) {
                            contentDiv.innerHTML = analysisContent();
                            drawIcons();
                        }
                    }

                    function analysisContent() {
                        if (!analysis)
                            return empty(
                                "sparkles",
                                "No analysis yet for this version",
                                "Click Analyze above to score this resume version with AI.",
                            );
                        if (tab === "score") {
                            var items = analysis.issues || [];
                            return card(
                                '<div class="font-display text-base font-semibold tracking-tight">Critical Issues & Recommendations</div><div class="space-y-3.5 mt-5">' +
                                (items.length
                                    ? items
                                        .map(function (x, i) {
                                            return (
                                                '<div class="flex gap-4 p-4 rounded-2xl bg-[var(--surface-2)]/60 border border-[var(--border)]/60 hover:border-[var(--border)] transition-all"><div class="h-10 w-10 shrink-0 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center shadow-sm">' +
                                                icon("alert-circle") +
                                                '</div><div class="space-y-1"><div class="text-sm font-semibold tracking-tight">' +
                                                esc(x.title || "Issue " + (i + 1)) +
                                                '</div><div class="text-xs text-[var(--ink-muted)] leading-relaxed">' +
                                                esc(x.explanation || "") +
                                                "</div></div></div>"
                                            );
                                        })
                                        .join("")
                                    : '<div class="text-sm text-[var(--ink-muted)] py-4 text-center">No issues recorded. Great job!</div>') +
                                "</div>",
                            );
                        }
                        if (tab === "strengths") {
                            var s = analysis.strengths || [];
                            return card(
                                '<div class="font-display text-base font-semibold tracking-tight">Key Strengths</div><ul class="space-y-3 mt-5">' +
                                (s.length
                                    ? s
                                        .map(function (x) {
                                            return (
                                                '<li class="flex items-start gap-3.5 text-sm p-4 rounded-2xl bg-[var(--surface-2)]/60 border border-[var(--border)]/60"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-[var(--success)] font-bold text-xs shadow-sm">✓</span><span class="leading-relaxed pt-0.5">' +
                                                esc(
                                                    typeof x === "string"
                                                        ? x
                                                        : x.title ||
                                                        x.description ||
                                                        "",
                                                ) +
                                                '<div class="text-xs text-[var(--ink-muted)] leading-relaxed">' +
                                                esc(x.evidence || "") +
                                                "</span></li>"
                                            );
                                        })
                                        .join("")
                                    : '<li class="text-sm text-[var(--ink-muted)] py-4 text-center">No strengths recorded.</li>') +
                                "</ul>",
                            );
                        }
                        if (tab === "keywords") {
                            return (
                                '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">' +
                                card(
                                    '<div class="flex items-center justify-between"><div class="font-display text-base font-semibold tracking-tight">Present Keywords</div><span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 font-medium">' +
                                    (analysis.keywords_present || []).length +
                                    ' found</span></div><div class="flex flex-wrap gap-2 mt-5">' +
                                    (analysis.keywords_present || [])
                                        .map(function (x) {
                                            return badge(
                                                typeof x === "string" ? x : x.keyword,
                                                "accent",
                                            );
                                        })
                                        .join("") +
                                    "</div>",
                                ) +
                                card(
                                    '<div class="flex items-center justify-between"><div class="font-display text-base font-semibold tracking-tight">Missing Keywords</div><span class="text-xs px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-600 font-medium">' +
                                    (analysis.keywords_missing || []).length +
                                    ' missing</span></div><div class="flex flex-wrap gap-2 mt-5">' +
                                    (analysis.keywords_missing || [])
                                        .map(function (x) {
                                            return badge(
                                                typeof x === "string" ? x : x.keyword,
                                                "danger",
                                            );
                                        })
                                        .join("") +
                                    "</div>",
                                ) +
                                "</div>"
                            );
                        }
                        if (tab === "rewrites") {
                            var rw = analysis.bullet_rewrites || [];
                            window.__analysisId = analysis.id || null;
                            return card(
                                '<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-[var(--border)]"><div><div class="font-display text-base font-semibold tracking-tight">Suggested Bullet Rewrites</div><div class="text-sm text-[var(--ink-muted)] mt-1">Select the AI-optimized bullets you want to implement.</div></div>' +
                                badge(rw.length + " suggestions", "accent") +
                                '</div><div class="space-y-4 mt-5">' +
                                (rw.length
                                    ? rw
                                        .map(function (r, i) {
                                            return (
                                                '<label class="block rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/40 hover:bg-[var(--surface-2)] transition-all p-5 cursor-pointer"><div class="flex items-start gap-4"><input type="checkbox" class="rewrite-check mt-1.5 h-4 w-4 rounded border-[var(--border)] text-[var(--accent)] focus:ring-0 cursor-pointer" value="' +
                                                esc(r.id || i) +
                                                '" checked><div class="flex-1 space-y-3"><div class="space-y-1"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">Original</div><div class="text-sm text-[var(--ink-muted)] line-through leading-relaxed">' +
                                                esc(r.original || r.text || "") +
                                                '</div></div><div class="space-y-1"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--accent-strong)]">Optimized Rewrite</div><div class="text-sm font-medium leading-relaxed">' +
                                                esc(r.rewritten || r.text || "") +
                                                "</div></div></div></div></label>"
                                            );
                                        })
                                        .join("")
                                    : '<div class="text-sm text-[var(--ink-muted)] py-8 text-center">No rewrites suggested.</div>') +
                                `</div><div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-[var(--border)]"><button id="apply-selected" class="h-11 px-5 rounded-2xl border border-[var(--border)] hover:bg-[var(--surface-2)] font-semibold text-sm transition-all shadow-sm">
                                                        Apply selected
                                                        </button>
                                                        <button id="apply-all" class="h-11 px-6 rounded-2xl text-white font-semibold text-sm shadow-md hover:opacity-95 transition-all" style="background:linear-gradient(135deg,#3B6E8C,#1A3644)">
                                                        Apply all → new version
                                                        </button>
                                                    </div>`,
                            );
                        }
                        if (tab === "diff") {
                            if (versions.length < 2)
                                return empty(
                                    "git-compare",
                                    "Need two versions to compare",
                                    "Apply rewrites or upload a new version to see diffs here.",
                                );
                            return '<div id="full-diff-panel"></div>';
                        }
                        return empty(
                            "file-text",
                            "Unknown analysis tab",
                            "Choose another analysis tab.",
                        );
                    }

                    function parsed(v) {
                        if (!v) return "";
                        var s = v.parsed_sections || {},
                            b = s.basics || {};
                        var out = card(
                            '<div class="flex items-center justify-between pb-4 border-b border-[var(--border)]"><div><div class="font-display text-lg font-semibold tracking-tight">Parsed Sections</div><div class="text-sm text-[var(--ink-muted)] mt-0.5">Extracted from <span class="font-medium text-[var(--ink)]">' +
                            esc(v.label || "Version") +
                            "</span></div></div>" +
                            badge("PDF Parsed", "accent") +
                            '</div><div class="space-y-6 mt-6">',
                        );
                        if (b.name || b.title || b.email || b.phone || b.location) {
                            out +=
                                '<div class="p-4 rounded-2xl bg-[var(--surface-2)]/50 border border-[var(--border)]/60">' +
                                (b.name
                                    ? '<div class="font-display text-xl font-bold tracking-tight">' +
                                    esc(b.name) +
                                    "</div>"
                                    : "") +
                                (b.title
                                    ? '<div class="text-sm font-medium text-[var(--accent-strong)] mt-0.5">' +
                                    esc(b.title) +
                                    "</div>"
                                    : "") +
                                '<div class="text-xs text-[var(--ink-muted)] mt-2 flex flex-wrap gap-4 items-center">' +
                                [b.email, b.phone, b.location]
                                    .filter(Boolean)
                                    .map(function (item) {
                                        return (
                                            '<span class="flex items-center gap-1.5 bg-[var(--surface)] px-2.5 py-1 rounded-lg border border-[var(--border)]/60">' +
                                            esc(item) +
                                            "</span>"
                                        );
                                    })
                                    .join("") +
                                "</div></div>";
                        }
                        if (s.summary)
                            out +=
                                '<div class="p-4 rounded-2xl bg-[var(--surface-2)]/30 border border-[var(--border)]/40"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)] mb-1.5">Professional Summary</div><p class="text-sm leading-relaxed text-[var(--ink)]">' +
                                esc(s.summary) +
                                "</p></div>";
                        if (s.experience && s.experience.length)
                            out +=
                                '<div class="space-y-3"><div class="flex items-center justify-between"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">Experience</div><span class="text-xs text-[var(--ink-muted)] font-medium">' +
                                s.experience.length +
                                ' positions</span></div><div class="space-y-3">' +
                                s.experience
                                    .slice(0, 5)
                                    .map(function (e) {
                                        return (
                                            '<div class="p-4 rounded-2xl border border-[var(--border)]/60 bg-[var(--surface-2)]/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2"><div><div class="text-sm font-semibold tracking-tight">' +
                                            esc(e.role || "") +
                                            "</div>" +
                                            (e.company
                                                ? '<div class="text-xs font-medium text-[var(--accent-strong)] mt-0.5">' +
                                                esc(e.company) +
                                                "</div>"
                                                : "") +
                                            "</div>" +
                                            (e.period
                                                ? '<div class="text-xs px-2.5 py-1 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--ink-muted)] font-medium shrink-0 self-start sm:self-auto">' +
                                                esc(e.period) +
                                                "</div>"
                                                : "") +
                                            "</div>"
                                        );
                                    })
                                    .join("") +
                                "</div></div>";
                        if (s.education && s.education.length)
                            out +=
                                '<div class="space-y-3"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">Education</div><div class="space-y-2.5">' +
                                s.education
                                    .map(function (e) {
                                        return (
                                            '<div class="p-3.5 rounded-2xl border border-[var(--border)]/60 bg-[var(--surface-2)]/20 flex items-center justify-between gap-2"><div class="text-sm font-semibold tracking-tight">' +
                                            esc(e.degree || "") +
                                            "</div>" +
                                            (e.school
                                                ? '<div class="text-xs text-[var(--ink-muted)] font-medium">' +
                                                esc(e.school) +
                                                "</div>"
                                                : "") +
                                            "</div>"
                                        );
                                    })
                                    .join("") +
                                "</div></div>";
                        if (s.skills && s.skills.length)
                            out +=
                                '<div class="space-y-3"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">Skills</div><div class="flex flex-wrap gap-2">' +
                                s.skills
                                    .slice(0, 24)
                                    .map(function (x) {
                                        return badge(x, "accent");
                                    })
                                    .join("") +
                                "</div></div>";
                        if (s.projects && s.projects.length)
                            out +=
                                '<div class="space-y-3"><div class="text-[10px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">Projects</div><div class="space-y-2.5">' +
                                s.projects
                                    .slice(0, 5)
                                    .map(function (p) {
                                        return (
                                            '<div class="p-3.5 rounded-2xl border border-[var(--border)]/60 bg-[var(--surface-2)]/25 flex flex-col sm:flex-row sm:items-center justify-between gap-2"><div class="text-sm font-semibold tracking-tight">' +
                                            esc(p.name || "") +
                                            "</div>" +
                                            (p.tech && p.tech.length
                                                ? '<div class="text-xs px-2.5 py-1 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--accent-strong)] font-medium">' +
                                                esc(p.tech.join(" · ")) +
                                                "</div>"
                                                : "") +
                                            "</div>"
                                        );
                                    })
                                    .join("") +
                                "</div></div>";
                        return out + "</div></div>";
                    }

                    function render() {
                        window.__analysisId = (analysis && analysis.id) || null;
                        var v = versions.find(function (x) {
                            return String(x.id) === String(active);
                        });
                        var action =
                            '<div class="flex items-center gap-3"><a href="/resumes" class="inline-flex items-center gap-2 h-10 px-4 rounded-2xl border border-[var(--border)] hover:bg-[var(--surface-2)] text-sm font-semibold transition-all shadow-sm">' +
                            icon("arrow-left") +
                            ' All resumes</a><a href="/resumes/' +
                            encodeURIComponent(id) +
                            '/export" class="inline-flex items-center gap-2 h-10 px-4 rounded-2xl border border-[var(--border)] hover:bg-[var(--surface-2)] text-sm font-semibold transition-all shadow-sm">' +
                            icon("download") +
                            " Export PDF</a></div>";
                        var top = pageHeader(
                            resume.title || "Resume",
                            "Updated " +
                            relativeTime(resume.updated_at) +
                            " · " +
                            versions.length +
                            " version" +
                            (versions.length === 1 ? "" : "s"),
                            action,
                        );
                        top += card(
                            '<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6"><div class="space-y-3 flex-1"><div class="font-display text-base font-semibold tracking-tight">Run AI Analysis</div><div class="text-sm text-[var(--ink-muted)] leading-relaxed">Score this version with Gemini to pinpoint issues, highlight core strengths, and generate targeted rewrites.</div><div class="flex flex-wrap gap-2 pt-1">' +
                            versions
                                .map(function (x) {
                                    return (
                                        '<button data-version="' +
                                        esc(x.id) +
                                        '" class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all shadow-sm ' +
                                        (String(x.id) === String(active)
                                            ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-sm"
                                            : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)]") +
                                        '">' +
                                        esc(x.label || "Version") +
                                        "</button>"
                                    );
                                })
                                .join("") +
                            '</div></div><div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto lg:min-w-[360px]"><input id="target-role" value="' +
                            esc(targetRole) +
                            '" placeholder="Target role (e.g. Senior Frontend Engineer)" class="flex-1 h-12 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-sm outline-none focus:border-[var(--accent)] transition-all shadow-inner"><button id="analyze-btn" class="h-12 px-6 rounded-2xl text-white font-semibold text-sm shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 shrink-0" style="background:linear-gradient(135deg,#3B6E8C,#1A3644)">' +
                            icon("sparkles") +
                            " Analyze</button></div></div>",
                        );
                        if (analysis) {
                            var bd = Object.entries(analysis.score_breakdown || {})
                                .map(function (x) {
                                    var k = x[0],
                                        val = x[1];
                                    return (
                                        '<div class="space-y-1.5"><div class="flex justify-between text-xs font-medium"><span class="capitalize text-[var(--ink-muted)]">' +
                                        esc(k.replace(/_/g, " ")) +
                                        '</span><span class="tabular font-bold text-[var(--ink)]">' +
                                        esc(val) +
                                        '</span></div><div class="bar h-2 rounded-full overflow-hidden bg-[var(--surface-2)]"><span class="block h-full rounded-full transition-all duration-500" style="background:linear-gradient(135deg,#3B6E8C,#1A3644); width:' +
                                        Math.max(0, Math.min(100, Number(val) || 0)) +
                                        '%"></span></div></div>'
                                    );
                                })
                                .join("");
                            top +=
                                '<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-6"><div class="lg:col-span-4 flex flex-col">' +
                                card(
                                    '<div class="text-[11px] font-bold uppercase tracking-wider text-[var(--ink-muted)]">ATS Score Overview</div><div class="font-display text-7xl text-center font-bold tabular py-6 tracking-tighter text-[var(--accent-strong)]">' +
                                    esc(
                                        analysis.ats_score == null
                                            ? "-"
                                            : analysis.ats_score,
                                    ) +
                                    '</div><div class="text-xs text-center font-medium text-[var(--ink-muted)] pb-2">Out of 100 potential points</div>',
                                ) +
                                '</div><div class="lg:col-span-5 flex flex-col">' +
                                card(
                                    '<div class="font-display text-base font-semibold tracking-tight">Score Breakdown</div><div class="space-y-4 mt-5">' +
                                    bd +
                                    "</div>",
                                ) +
                                '</div><div class="lg:col-span-3 flex flex-col">' +
                                card(
                                    '<div class="flex items-center justify-between"><div class="font-display text-base font-semibold tracking-tight">AI Verdict</div>' +
                                    badge(analysis.model || "AI", "accent") +
                                    '</div><p class="text-sm leading-relaxed text-[var(--ink-muted)] mt-5">' +
                                    esc(analysis.summary || "") +
                                    "</p>",
                                ) +
                                "</div></div>";
                            var tabs = [
                                ["score", "Issues"],
                                ["strengths", "Strengths"],
                                ["keywords", "Keywords"],
                                ["rewrites", "Rewrites"],
                            ];

                            if (versions.length >= 2) tabs.push(["diff", "Diff"]);
                            top +=
                                '<div class="mt-8"><div class="inline-flex items-center gap-1.5 bg-[var(--surface)] border border-[var(--border)] p-1.5 rounded-2xl shadow-sm">' +
                                tabs
                                    .map(function (t) {
                                        return (
                                            '<button data-analysis-tab="' +
                                            t[0] +
                                            '" class="h-10 px-4 rounded-xl text-xs font-semibold transition-all ' +
                                            (tab === t[0]
                                                ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-sm"
                                                : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)]") +
                                            '">' +
                                            t[1] +
                                            "</button>"
                                        );
                                    })
                                    .join("") +
                                '</div><div id="analysis-content" class="mt-6">' +
                                analysisContent() +
                                "</div></div>";
                        } else
                            top += empty(
                                "sparkles",
                                "No analysis yet for this version",
                                "Click Analyze above to score this resume version with AI.",
                            );
                        top += '<div class="mt-8">' + parsed(v) + "</div>";
                        root.innerHTML = top;
                        drawIcons();
                        $$("[data-version]").forEach(function (b) {
                            b.onclick = async function () {
                                active = b.dataset.version;
                                await loadAnalysis();
                            };
                        });
                        $("#analyze-btn").onclick = async function () {
                            targetRole = $("#target-role").value.trim();
                            var b = $("#analyze-btn");
                            b.disabled = true;
                            b.textContent = "Analyzing…";
                            try {
                                var x = await api(
                                    "/resumes/" + encodeURIComponent(id) + "/analyze",
                                    {
                                        method: "POST",
                                        body: {
                                            versionId: active,
                                            targetRole: targetRole || undefined,
                                        },
                                    },
                                );
                                analysis = x.analysis || (x.data && x.data.analysis) || x;
                                toast(
                                    "Analysis complete",
                                    "ATS score " +
                                    (analysis.ats_score == null
                                        ? "-"
                                        : analysis.ats_score) +
                                    " / 100",
                                    "success",
                                );
                                await loadAnalysis();
                            } catch (e) {
                                toast("Analysis failed", e.message, "error");
                            } finally {
                                b.disabled = false;
                                b.innerHTML = icon("sparkles") + " Analyze";
                                drawIcons();
                            }
                        };
                        $$("[data-analysis-tab]").forEach(function (b) {
                            b.onclick = function () {
                                tab = b.dataset.analysisTab;
                                render();
                            };
                        });
                        bindRewrite();
                        if (tab === "diff") initDiffPanel();
                    }
                    function bindRewrite() {
                        var s = $("#apply-selected"),
                            a = $("#apply-all");
                        if (!s && !a) return;

                        var apply = async function (all) {
                            var ids = $$(".rewrite-check:checked").map(function (x) {
                                return x.value;
                            });

                            try {
                                var payload = { analysis_id: window.__analysisId };
                                if (!all && ids.length) payload.rewrite_ids = ids;
                                var r = await api(
                                    "/resumes/" + encodeURIComponent(id) + "/rewrite",
                                    { method: "POST", body: payload },
                                );
                                var v = r.version || (r.data && r.data.version);
                                if (v && v.id) {
                                    toast(
                                        "Rewrites applied",
                                        (v.label || "New version") + " created",
                                        "success",
                                    );
                                    location.reload();
                                }
                            } catch (e) {
                                toast("Couldn't apply rewrites", e.message, "error");
                            }
                        };
                        if (s)
                            s.onclick = function () {
                                apply(false);
                            };
                        if (a)
                            a.onclick = function () {
                                apply(true);
                            };
                    }
                    await loadAnalysis();
                } catch (e) {
                    root.innerHTML = empty(
                        "file-text",
                        "Resume not found",
                        e.message,
                        '<a href="/resumes" class="inline-flex items-center gap-2 h-10 px-4 rounded-2xl border border-[var(--border)] hover:bg-[var(--surface-2)] text-sm font-semibold transition-all shadow-sm">Back to resumes</a>',
                    );
                    drawIcons();
                }
            }

            // Helper function for initialization of Version Differencial panel -> resume-detail page
            async function initDiffPanel() {
                var root = Roaster.$("#full-diff-panel");
                if (!root) return;
                var id = getPageId();
                var versions = window.__resumeVersions || [];
                if (versions.length < 2) {
                    root.innerHTML = Roaster.empty(
                        "git-compare",
                        "Need two versions to compare",
                        "Apply rewrites or upload a new version to see diffs here.",
                    );
                    Roaster.drawIcons();
                    return;
                }
                var mode = "words",
                    fromId = versions[versions.length - 2].id,
                    toId = versions[versions.length - 1].id;
                function shell() {
                    root.innerHTML = Roaster.card(
                        '<div class="flex items-start justify-between gap-4 flex-wrap"><div><div class="font-display text-base font-semibold">Version Diff</div><div class="text-sm text-[var(--ink-muted)] mt-1">Compare what changed between any two versions</div></div><div class="inline-flex items-center gap-1 bg-[var(--surface-2)] border border-[var(--border)] p-1 rounded-full">' +
                        ["words", "lines"]
                            .map(function (m) {
                                return (
                                    '<button type="button" data-full-diff-mode="' +
                                    m +
                                    '" class="h-7 px-3 rounded-full text-[11px] font-semibold ' +
                                    (mode === m
                                        ? "bg-[var(--ink)] text-[var(--bg)]"
                                        : "text-[var(--ink-muted)]") +
                                    '">' +
                                    m +
                                    "</button>"
                                );
                            })
                            .join("") +
                        "</div></div>" +
                        '<div class="rounded-2xl border border-[var(--border)] bg-[var(--surface-2)]/60 p-3 mt-5 flex items-center justify-center gap-3 flex-wrap"><span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">From</span><select id="full-diff-from" class="h-8 px-3 rounded-full border border-[var(--border)] bg-[var(--surface)] text-xs">' +
                        versions
                            .map(function (v) {
                                return (
                                    '<option value="' +
                                    Roaster.esc(v.id) +
                                    '" ' +
                                    (String(v.id) === String(fromId)
                                        ? "selected"
                                        : "") +
                                    " " +
                                    (String(v.id) === String(toId) ? "disabled" : "") +
                                    ">" +
                                    Roaster.esc(v.label || "Version") +
                                    "</option>"
                                );
                            })
                            .join("") +
                        '</select><div class="h-8 w-8 rounded-full flex items-center justify-center text-white shadow-card" style="background:linear-gradient(135deg,var(--accent),var(--accent-strong))">' +
                        Roaster.icon("arrow-right", "w-4 h-4") +
                        '</div><span class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">To</span><select id="full-diff-to" class="h-8 px-3 rounded-full border border-[var(--border)] bg-[var(--surface)] text-xs">' +
                        versions
                            .map(function (v) {
                                return (
                                    '<option value="' +
                                    Roaster.esc(v.id) +
                                    '" ' +
                                    (String(v.id) === String(toId) ? "selected" : "") +
                                    " " +
                                    (String(v.id) === String(fromId)
                                        ? "disabled"
                                        : "") +
                                    ">" +
                                    Roaster.esc(v.label || "Version") +
                                    "</option>"
                                );
                            })
                            .join("") +
                        '</select><button id="full-diff-compare" type="button" class="h-8 px-3 rounded-full text-white text-xs font-semibold" style="background:linear-gradient(135deg,var(--accent),var(--accent-strong))">Compare</button></div><div id="full-diff-result" class="mt-5"></div>',
                    );
                    Roaster.drawIcons();
                    Roaster.$("[data-full-diff-mode]").forEach(function (b) {
                        b.onclick = function () {
                            mode = b.dataset.fullDiffMode;
                            shell();
                            load();
                        };
                    });
                    Roaster.$("#full-diff-from").onchange = function () {
                        fromId = this.value;
                        if (String(fromId) === String(toId)) {
                            var alt = versions.find(function (v) {
                                return String(v.id) !== String(fromId);
                            });
                            if (alt) toId = alt.id;
                        }
                        shell();
                        load();
                    };
                    Roaster.$("#full-diff-to").onchange = function () {
                        toId = this.value;
                        if (String(fromId) === String(toId)) {
                            var alt = versions.find(function (v) {
                                return String(v.id) !== String(toId);
                            });
                            if (alt) fromId = alt.id;
                        }
                        shell();
                        load();
                    };
                    Roaster.$("#full-diff-compare").onclick = load;
                }
                async function load() {
                    var box = Roaster.$("#full-diff-result");
                    if (!box) return;
                    if (!fromId || !toId || String(fromId) === String(toId)) {
                        box.innerHTML =
                            '<div class="text-sm text-[var(--ink-muted)]">Select two different versions.</div>';
                        return;
                    }
                    box.innerHTML =
                        '<div class="h-[300px] animate-pulse bg-[var(--surface-2)] rounded-2xl"></div>';
                    try {
                        var d = await api(
                            "/resumes/" +
                            encodeURIComponent(id) +
                            "/diff?from=" +
                            encodeURIComponent(fromId) +
                            "&to=" +
                            encodeURIComponent(toId) +
                            "&mode=" +
                            encodeURIComponent(mode),
                        );

                        var stats = d.stats || {},
                            added = Number(stats.added || 0),
                            removed = Number(stats.removed || 0),
                            net = added - removed,
                            parts = d.parts || [];

                        console.log("Raw parts from API:", parts);
                        var fromLabel =
                            (
                                versions.find(function (v) {
                                    return String(v.id) === String(fromId);
                                }) || {}
                            ).label || "-";
                        var toLabel =
                            (
                                versions.find(function (v) {
                                    return String(v.id) === String(toId);
                                }) || {}
                            ).label || "-";
                        box.innerHTML =
                            '<div class="relative rounded-2xl border border-[var(--border)] p-5 overflow-hidden" style="background:linear-gradient(135deg,var(--accent-soft) 0%,var(--surface-2) 70%,var(--surface) 100%)"><div class="flex items-end gap-8 flex-wrap"><div><div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">Added</div><div class="font-display tabular text-[40px] font-semibold text-[var(--accent-strong)] mt-1">+' +
                            added +
                            '</div><span class="text-[11px] text-[var(--ink-muted)]">chars</span></div><div class="h-10 w-px bg-[var(--border)]"></div><div><div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">Removed</div><div class="font-display tabular text-[40px] font-semibold text-[var(--danger)] mt-1">−' +
                            removed +
                            '</div><span class="text-[11px] text-[var(--ink-muted)]">chars</span></div><div class="h-10 w-px bg-[var(--border)]"></div><div><div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">Net change</div><div class="font-display tabular text-[26px] font-semibold mt-2 ' +
                            (net > 0
                                ? "text-[var(--accent-strong)]"
                                : net < 0
                                    ? "text-[var(--danger)]"
                                    : "text-[var(--ink-muted)]") +
                            '">' +
                            (net > 0 ? "+" : net < 0 ? "" : "±") +
                            net +
                            '</div><span class="text-[11px] text-[var(--ink-muted)]">chars</span></div><div class="ml-auto text-right"><div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">Comparing</div><div class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold">' +
                            Roaster.esc(fromLabel) +
                            Roaster.icon("arrow-right", "w-3 h-3") +
                            Roaster.esc(toLabel) +
                            '</div></div></div></div><div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden mt-5"><div class="h-10 px-4 flex items-center justify-between border-b border-[var(--border)] bg-[var(--surface-2)]/60"><div class="flex items-center gap-1.5 text-[10px] uppercase tracking-wider font-semibold text-[var(--ink-muted)]">' +
                            Roaster.icon("file-text", "w-3 h-3") +
                            " Inline diff · " +
                            Roaster.esc(mode) +
                            '</div><div class="flex items-center gap-3 text-[10px] text-[var(--ink-muted)]"><span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[var(--accent-soft)] border border-[var(--accent)]/30"></span>Added</span><span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[#F8E3E0] border border-[var(--danger)]/30"></span>Removed</span></div></div><div class="p-5 max-h-[600px] overflow-auto font-mono text-[13px] leading-[1.75] whitespace-pre-wrap">' +
                            parts
                                .map(function (p) {
                                    // Force boolean conversion
                                    const isAdded = !!p.added;
                                    const isRemoved = !!p.removed;

                                    const cls =
                                        !isAdded && !isRemoved
                                            ? "text-[var(--ink)]"
                                            : isAdded
                                                ? "bg-[var(--accent-soft)] text-[var(--accent-strong)] font-medium rounded px-0.5 py-0.5"
                                                : "bg-[#F8E3E0] text-[var(--danger)] line-through rounded px-0.5 py-0.5";

                                    return (
                                        '<span class="' +
                                        cls +
                                        '">' +
                                        Roaster.esc(p.value || "") +
                                        "</span>"
                                    );
                                })
                                .join("") +
                            "</div></div>";
                        Roaster.drawIcons();
                    } catch (e) {
                        box.innerHTML =
                            '<div class="text-xs text-[var(--danger)] bg-[#F8E3E0] rounded-xl px-3 py-2">' +
                            Roaster.esc(e.message) +
                            "</div>";
                        Roaster.drawIcons();
                    }
                }
                shell();
                load();
            }

            renderResumeDetail();
        });
    </script>
@endpush