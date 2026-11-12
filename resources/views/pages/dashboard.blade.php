@extends('layouts.app')
@section('title', 'Dashboard - Roaster')
@section('page', 'dashboard')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                                ${renderStats(kpi, totals)}
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-6">
                                <div class="lg:col-span-7">
                                    ${Roaster.card(`
                                        <div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Score evolution</div>
                                        <div class="font-display text-base font-semibold mt-1">Every analysis, chronologically</div>
                                        <div class="h-[260px] mt-4"><canvas id="dashboard-score-chart"></canvas></div>
                                    `)}
                                </div>
                                <div class="lg:col-span-3">
                                    ${Roaster.card(`<div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Profile</div>${renderProfile(window.__user)}`)}
                                </div>
                                <div class="lg:col-span-2">
                                    ${Roaster.card(`<div class="text-[11px] uppercase tracking-wide text-[var(--muted-foreground)] font-semibold">Latest ATS</div><div class="font-display text-5xl font-semibold tabular mt-5">${Roaster.Roaster.esc(kpi.atsScore?.value ?? "-")}</div><div class="text-xs text-[var(--muted-foreground)] mt-1">/ 100</div>`)}
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

        function renderProfile(user) {
            return `
            <div class="flex flex-col items-center text-center mt-4">
                <div class="h-16 w-16 rounded-full bg-[var(--accent-soft)] text-[var(--accent-strong)] flex items-center justify-center text-xl font-semibold">
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
                                    <div class="h-9 w-9 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center">
                                        ${Roaster.icon(s[2])}
                                    </div>
                                    <div>
                                        <div class="text-xs text-[var(--muted-foreground)]">${s[0]}</div>
                                        <div class="font-display tabular text-3xl font-semibold tracking-tight mt-1">${Roaster.esc(s[1])}</div>
                                        ${s[3] != null ? `<div class="text-[10px] text-[var(--success)] mt-1">${s[3] >= 0 ? "+" : ""}${s[3]} vs prev</div>` : ""}
                                    </div>
                                </div>
                            `),
                )
                .join("");
        }

        function renderVersionStack(versions) {
            if (!versions.length)
                return '<div class="text-sm text-[var(--ink-muted)] py-4 text-center">No versions yet.</div>';

            return versions
                .map(
                    (v) => `
                <div class="flex items-center justify-between p-3 rounded-2xl bg-[var(--surface-2)]">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-[var(--accent-soft)] flex items-center justify-center">
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