@extends('layouts.app')
@section('title', 'Dashboard - Roaster')
@section('page', 'dashboard')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 min-w-0 px-4 sm:px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>
            <div id="dashboard-page" class="page-enter">
                <div class="animate-pulse space-y-4">
                    <div class="h-10 w-full bg-[var(--surface-2)] rounded-2xl"></div>
                    <div class="h-64 w-full bg-[var(--surface-2)] rounded-3xl"></div>
                </div>
            </div>
        </main>
    </div>
    @include('components.layout.CommandPalette')
@endsection

@push('scripts')
    <script>
        async function loadDashboard() {
            const root = document.getElementById("dashboard-page");
            if (!root) return;

            try {
                // Fetch the user
                if (!window.__user) {
                    const userRes = await fetch("{{ url('/api/me') }}", {
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                            'Accept': 'application/json'
                        }
                    });
                    const userData = await userRes.json();
                    window.__user = userData.user; // Set the global user
                }

                const response = await fetch("{{ url('/api/dashboard') }}", {
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                        'Accept': 'application/json'
                    }
                });

                const d = await response.json();
                const { totals, kpi, scoreSeries, versionStack, activity } = d;

                // Empty State
                if (!totals.resumes) {
                    root.innerHTML = Roaster.pageHeader("Dashboard", "Keep sharpening your resume") +
                        Roaster.empty(
                            "upload-cloud",
                            "Welcome — let's roast your resume",
                            "Upload a PDF to get an instant ATS score.",
                            `<a href="/resumes" class="roaster-primary-button inline-flex items-center gap-2 h-10 px-4 rounded-2xl text-sm font-semibold text-white">
                                                        ${Roaster.icon("upload-cloud")} Upload your first resume
                                                    </a>`
                        );
                    Roaster.drawIcons();
                    return;
                }

                // Render Dashboard
                root.innerHTML = `
                            ${Roaster.pageHeader("Dashboard", "Sharpen your resume with calm, focused AI insights.")}

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 stagger">
                                ${renderStats(kpi, totals)}
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-6">
                                <div class="lg:col-span-6" data-reveal>
                                    ${Roaster.card(`
                                        <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Score evolution</div>
                                        <div class="font-display text-base font-semibold mt-1">Every analysis, chronologically</div>
                                        <div class="h-[260px] mt-4"><canvas id="dashboard-score-chart"></canvas></div>
                                    `)}
                                </div>
                                <div class="lg:col-span-3" data-reveal style="--reveal-delay:80ms">
                                    ${Roaster.card(`<div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Profile</div>${renderProfile(window.__user)}`)}
                                </div>
                                <div class="lg:col-span-3" data-reveal style="--reveal-delay:160ms">
                                    ${Roaster.card(`<div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Latest ATS</div>${scoreRing(kpi.atsScore?.value ?? "-")}`)}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-6">
                                <div class="lg:col-span-7">
                                    ${Roaster.card(`
                                        <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold mb-3">Recent Versions</div>
                                        <div class="space-y-3">${renderVersionStack(versionStack)}</div>
                                    `)}
                                </div>
                                <div class="lg:col-span-5">
                                    ${Roaster.card(`
                                        <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold mb-3">Activity</div>
                                        <div class="space-y-1">${renderActivity(activity)}</div>
                                    `)}
                                </div>
                            </div>
                        `;

                Roaster.drawIcons();
                initChart(scoreSeries);

            } catch (e) {
                root.innerHTML = Roaster.empty("gauge", "Couldn't load your dashboard", e.message);
                Roaster.drawIcons();
            }
        }

        // Numbers count up when they appear (see data-count in app.js)
        function countNum(v) {
            const n = parseFloat(v);
            return isNaN(n) ? Roaster.esc(v) : `<span data-count="${n}">0</span>`;
        }

        function scoreRing(score) {
            const n = parseFloat(score);
            const val = isNaN(n) ? 0 : n;
            return `
                <div class="relative h-32 w-32 mx-auto mt-4">
                    <svg class="score-ring h-32 w-32 -rotate-90" viewBox="0 0 64 64" data-score-ring="${val}">
                        <defs><linearGradient id="dashRing" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" style="stop-color: var(--primary)"/><stop offset="100%" style="stop-color: var(--accent)"/>
                        </linearGradient></defs>
                        <circle cx="32" cy="32" r="27" fill="none" stroke="var(--border)" stroke-width="6"/>
                        <circle class="progress" cx="32" cy="32" r="27" fill="none" stroke="url(#dashRing)" stroke-width="6"
                            stroke-linecap="round" stroke-dasharray="169.6" stroke-dashoffset="169.6"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="font-display text-3xl font-extrabold tabular">${countNum(score ?? "-")}</div>
                        <div class="text-[10px] text-[var(--muted-foreground)] font-semibold">/ 100</div>
                    </div>
                </div>`;
        }

        function renderProfile(user) {
            return `
            <div class="flex flex-col items-center text-center mt-4">
                <div class="h-16 w-16 rounded-full bg-brand text-white flex items-center justify-center text-xl font-bold shadow-hover">
                    ${Roaster.esc((user.name || "R")[0].toUpperCase())}
                </div>
                <div class="font-display font-semibold mt-3">
                    ${Roaster.esc(user.name || "Account")}
                </div>
                <div class="text-xs text-[var(--ink-muted)] mt-1">
                    ${Roaster.esc(user.email || "")}
                </div>
            </div>
        `;
        }

        function renderStats(kpi, totals) {
            const stats = [
                ["ATS Score", kpi.atsScore?.value ?? "-", "gauge", kpi.atsScore?.delta],
                ["Versions", kpi.versions?.value ?? totals.resumes, "layers", null],
                [
                    "Issues",
                    kpi.issuesIdentified?.value ?? "-",
                    "lightbulb",
                    kpi.issuesIdentified?.delta,
                ],
                [
                    "Keywords",
                    kpi.keywordsMatched?.value ?? "-",
                    "key-round",
                    kpi.keywordsMatched?.delta,
                ],
            ];

            return stats
                .map((s) =>
                    Roaster.card(`
                                <div class="flex items-center gap-3">
                                    <div class="icon-tile h-12 w-12 rounded-2xl shrink-0">
                                        ${Roaster.icon(s[2], "w-5 h-5")}
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-[var(--muted-foreground)]">${s[0]}</div>
                                        <div class="font-display tabular text-3xl font-extrabold tracking-tight mt-0.5">${countNum(s[1])}</div>
                                        ${s[3] != null ? `<div class="inline-flex items-center gap-1 text-[10px] font-bold mt-1 px-1.5 py-0.5 rounded-full ${s[3] >= 0 ? "bg-[var(--success)]/12 text-[var(--success)]" : "bg-[var(--danger)]/12 text-[var(--danger)]"}">${s[3] >= 0 ? "▲" : "▼"} ${Math.abs(s[3])} vs prev</div>` : ""}
                                    </div>
                                </div>
                            `, "card-interactive"),
                )
                .join("");
        }

        function renderVersionStack(versions) {
            if (!versions.length)
                return '<div class="text-sm text-[var(--ink-muted)] py-4 text-center">No versions yet.</div>';

            return versions
                .map(
                    (v) => `
                <div class="flex items-center justify-between p-3 rounded-2xl bg-[var(--surface-2)] border border-transparent hover:border-[var(--primary)]/30 hover:translate-x-1 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <div class="icon-tile h-9 w-9 rounded-xl">
                            ${Roaster.icon(v.title === "Upload" ? "file-text" : "wand-2", "w-4 h-4")}
                        </div>
                        <div>
                            <div class="text-sm font-medium">${Roaster.esc(v.label)}</div>
                            <div class="text-xs text-[var(--ink-muted)]">${Roaster.esc(v.title)}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-display font-semibold">${Roaster.esc(v.score)}</div>
                        <div class="text-[10px] ${v.delta > 0 ? "text-[var(--success)]" : "text-[var(--danger)]"}">
                            ${v.delta > 0 ? "+" : ""}${v.delta}
                        </div>
                    </div>
                </div>
            `,
                )
                .join("");
        }

        function renderActivity(activity) {
            if (!activity.length)
                return '<div class="text-sm text-[var(--ink-muted)] py-4 text-center">No recent activity.</div>';

            return activity
                .map(
                    (e) => `
                            <a href="/resumes/${encodeURIComponent(e.resumeId)}" class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-[var(--surface-2)] transition-colors">
                                <div class="h-8 w-8 rounded-xl bg-[var(--surface-2)] flex items-center justify-center text-[var(--ink-muted)]">
                                    ${Roaster.icon(e.type === "upload" ? "upload" : e.type === "rewrite" ? "wand-2" : "sparkles", "w-4 h-4")}
                                </div>
                                <div>
                                    <div class="text-sm font-medium">${Roaster.esc(e.title)}</div>
                                    <div class="text-xs text-[var(--ink-muted)] mt-0.5">${Roaster.esc(e.subtitle)} · ${Roaster.relativeTime(e.at)}</div>
                                </div>
                            </a>
                        `,
                )
                .join("");
        }

        loadDashboard();
    </script>
@endpush