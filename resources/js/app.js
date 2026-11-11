// Configuration
const existingConfig = window.Roaster || {};
const apiBase = "/api";
const tokenKey = "auth_token";

// Merge all methods into window.Roaster instead of overwriting it
window.Roaster = Object.assign(window.Roaster || {}, {
    apiBase: apiBase,
    tokenKey: tokenKey,

    // Utilities
    $: (s, c) => (c || document).querySelector(s),
    $$: (s, c) => Array.from((c || document).querySelectorAll(s)),
    esc: (v) => {
        return String(v == null ? "" : v).replace(
            /[&<>"']/g,
            (m) =>
                ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;",
                })[m],
        );
    },

    // Auth Helpers
    token: function () {
        return localStorage.getItem(this.tokenKey);
    },
    saveToken: function (t) {
        if (t) localStorage.setItem(this.tokenKey, t);
    },
    clearToken: function () {
        localStorage.removeItem(this.tokenKey);
    },

    // UI Components
    icon: (name, cls = "w-4 h-4") =>
        `<i data-lucide="${name}" class="${cls}"></i>`,

    drawIcons: () => {
        if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
    },

    badge: function (text, tone = "neutral") {
        const map = {
            neutral: "bg-[var(--secondary)] text-[var(--muted-foreground)]",
            accent: "bg-[var(--accent-soft)] text-[var(--accent-strong)]",
            success: "bg-[var(--accent-soft)] text-[var(--success)]",
            warning: "bg-[var(--accent-soft)] text-[var(--warning)]",
            danger: "bg-[#F8E3E0] text-[var(--danger)]",
        };
        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold ${map[tone] || map.neutral}">${this.esc(text)}</span>`;
    },

    card: (inner, extra = "") =>
        `<div class="rounded-[20px] border border-[var(--border)] shadow-card bg-[var(--surface)] p-5 ${extra}">${inner}</div>`,

    empty: function (ic, title, desc, action = "") {
        return `
            <div class="rounded-[20px] border border-[var(--border)] bg-[var(--surface)] shadow-card p-8 flex flex-col items-center justify-center text-center min-h-[220px]">
                <div class="h-14 w-14 rounded-2xl bg-[var(--accent-soft)] text-[var(--accent-strong)] flex items-center justify-center mb-4">
                    ${this.icon(ic, "w-6 h-6")}
                </div>
                <div class="font-display text-lg font-semibold">${this.esc(title)}</div>
                <p class="text-sm text-[var(--ink-muted)] mt-1 max-w-sm">${this.esc(desc)}</p>
                ${action ? `<div class="mt-4">${action}</div>` : ""}
            </div>`;
    },

    pageHeader: function (title, desc = "", action = "") {
        return `
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-display text-3xl font-semibold tracking-tight">${this.esc(title)}</h2>
                    <p class="text-sm text-[var(--ink-muted)] mt-1">${this.esc(desc)}</p>
                </div>
                ${action}
            </div>`;
    },

    relativeTime: function (date) {
        if (!date) return "";
        const d = new Date(date);
        const sec = Math.max(0, Math.floor((Date.now() - d.getTime()) / 1000));
        const m = Math.floor(sec / 60);
        const h = Math.floor(m / 60);
        const day = Math.floor(h / 24);
        if (sec < 45) return "just now";
        if (m < 60) return m + "m ago";
        if (h < 24) return h + "h ago";
        if (day < 7) return day + "d ago";
        return d.toLocaleDateString();
    },

    toast: function (title, description, variant) {
        var root = this.$("#toast-root");
        if (!root) return;

        var err = variant === "error";
        var el = document.createElement("div");
        el.className =
            "pointer-events-auto bg-[var(--surface)] border border-[var(--border)] shadow-hover rounded-2xl p-3.5 flex items-start gap-3 page-enter";

        el.innerHTML =
            '<div class="h-8 w-8 rounded-xl flex items-center justify-center ' +
            (err
                ? "bg-[#F8E3E0] text-[var(--danger)]"
                : "bg-[var(--accent-soft)] text-[var(--accent-strong)]") +
            '">' +
            this.icon(err ? "alert-circle" : "check-circle-2", "w-4 h-4") +
            '</div><div class="flex-1"><div class="text-sm font-semibold">' +
            this.esc(title) +
            "</div>" +
            (description
                ? '<div class="text-xs text-[var(--ink-muted)] mt-0.5">' +
                  this.esc(description) +
                  "</div>"
                : "") +
            '</div><button class="text-[var(--ink-muted)] text-lg leading-none">×</button>';

        root.appendChild(el);
        this.drawIcons();

        var close = function () {
            el.remove();
        };
        el.querySelector("button").onclick = close;
        setTimeout(close, 4200);
    },

    // API Helper
    api: async function (path, options = {}) {
        const headers = new Headers(options.headers || {});
        headers.set("Accept", "application/json");

        const t = this.token();
        if (t) headers.set("Authorization", "Bearer " + t);

        let body = options.body;
        if (body && !(body instanceof FormData) && typeof body !== "string") {
            headers.set("Content-Type", "application/json");
            body = JSON.stringify(body);
        }

        const base = (this.apiBase || "/api").replace(/\/+$/, "");
        const url = path.startsWith("http") ? path : `${base}${path.startsWith("/") ? path : "/" + path}`;

        console.log("URL being used: ", url);

        const res = await fetch(url, {
            method: options.method || "GET",
            headers,
            body,
        });

        const data = await res.json().catch(() => null);
        if (!res.ok) {
            const msg =
                data?.message ||
                (data?.errors ? Object.values(data.errors).flat()[0] : null) ||
                "Request failed";
            const err = new Error(msg);
            err.status = res.status;
            err.data = data;
            throw err;
        }
        return data;
    },
});

// Helper for chart's responsive coloring
function getComputedColor(variableName) {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(variableName)
        .trim();
}

async function api(path, options) {
    options = options || {};
    var headers = new Headers(options.headers || {});
    headers.set("Accept", "application/json");

    var t = Roaster?.token ? Roaster.token() : null;
    if (t) headers.set("Authorization", "Bearer " + t);

    var body = options.body;
    if (body && !(body instanceof FormData) && typeof body !== "string") {
        headers.set("Content-Type", "application/json");
        body = JSON.stringify(body);
    }

    // Determine Base URL safely from window.ROASTER or fallback to "/api"
    var base = (window.Roaster?.apiBase || (typeof cfg !== "undefined" && cfg.apiBase) || "/api").replace(/\/+$/, "");

    // If path is already a full URL (http/https), don't prepend base
    var url = /^https?:\/\//i.test(path)
        ? path
        : base + (path.startsWith("/") ? path : "/" + path);

    console.log("Fetching URL:", url); // <--- DEBUG 1: See exact URL

    var res = await fetch(url, {
        method: options.method || "GET",
        headers: headers,
        body: body,
    });

    var data = null,
        ct = res.headers.get("content-type") || "";
    if (ct.indexOf("application/json") >= 0) {
        try {
            data = await res.json();
        } catch (e) {
            data = null;
        }
    } else {
        try {
            data = await res.text();
        } catch (e) {
            data = null;
        }
    }

    if (!res.ok) {
        console.error("API Error Status:", res.status); // <--- DEBUG 2: See HTTP Status
        console.error("API Error Response Body:", data);

        var msg =
            (data && data.message) ||
            (data && data.errors
                ? Object.values(data.errors).flat()[0]
                : null) ||
            "Request failed";
        var err = new Error(msg);
        err.status = res.status;
        err.data = data;
        throw err;
    }

    return data;
}

function hydrateUser(u) {
    if (!u) return;
    var name = u.name || "Account";
    if (Roaster.$("#sidebar-avatar"))
        Roaster.$("#sidebar-avatar").textContent = (name[0] || "R").toUpperCase();
    if (Roaster.$("#sidebar-name")) Roaster.$("#sidebar-name").textContent = name;
    if (Roaster.$("#sidebar-email")) Roaster.$("#sidebar-email").textContent = u.email || "";
    if (Roaster.$("#topbar-first-name"))
        Roaster.$("#topbar-first-name").textContent = name.split(" ")[0] || "there";
}

async function ensureAuth() {
    if (!Roaster.token()) {
        location.href = "/login";
        return null;
    }
    try {
        var d = await Roaster.api("/me");
        var u = d.user || d;
        hydrateUser(u);
        return u;
    } catch (e) {
        Roaster.clearToken();
        location.href = "/login";
        return null;
    }
}

function initTheme() {
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

    function updateToggleButton(theme) {
        var b = Roaster.$("#theme-toggle");
        if (!b) return;

        // If theme is dark, show 'sun' (meaning click to go light).
        // If theme is light, show 'moon' (meaning click to go dark).
        var iconName = (theme === "dark") ? "sun" : "moon";
        
        // Only update if it's different to prevent unnecessary DOM flashing
        if (b.getAttribute("data-current-icon") !== iconName) {
            b.innerHTML = Roaster.icon(iconName, "w-4 h-4");
            b.setAttribute("data-current-icon", iconName);
            Roaster.drawIcons();
        }
    }

    function applyTheme(theme) {
        if (theme === "dark") 
            document.documentElement.classList.add("dark");
        else
            document.documentElement.classList.remove("dark");

        // Update charts if they exist
        if (window.myChartInstance) {
            const gridColor = getComputedStyle(document.documentElement).getPropertyValue("--grid-color").trim();
            const borderColor = getComputedStyle(document.documentElement).getPropertyValue("--chart-accent").trim();
            window.myChartInstance.data.datasets[0].borderColor = borderColor;
            window.myChartInstance.options.scales.y.grid.color = gridColor;
            window.myChartInstance.update();
        }

        // Update the icon based on the active theme
        updateToggleButton(theme);
    }

    // Determine theme's current state (Checks LocalStorage -> OS Preference)
    var savedTheme = localStorage.getItem("arr-theme");
    var initialTheme = savedTheme === "light" || savedTheme === "dark" 
        ? savedTheme 
        : (mediaQuery.matches ? "dark" : "light");

    applyTheme(initialTheme);

    // Handle button click toggle
    var b = Roaster.$("#theme-toggle");
    if (b) {
        b.onclick = function () {
            // Check the DOM class directly to see what the active theme is right now
            var isDark = document.documentElement.classList.contains("dark");
            var newTheme = isDark ? "light" : "dark";

            localStorage.setItem("arr-theme", newTheme);
            applyTheme(newTheme);
        };
    }

    // Listen for real-time OS changes (if user didn't explicitly override)
    mediaQuery.addEventListener("change", (e) => {
        if (!localStorage.getItem("arr-theme")) {
            applyTheme(e.matches ? "dark" : "light");
        }
    });

    // MutationObserver -> This watches the <html> tag for class changes (in case another script changes the theme)
    // and automatically forces the icon to sync up
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === "class") {
                var currentTheme = document.documentElement.classList.contains("dark") ? "dark" : "light";
                updateToggleButton(currentTheme);
            }
        });
    });
    
    observer.observe(document.documentElement, { attributes: true });
}

function initShell() {
    initTheme();
    initCommandPalette();

    var path = location.pathname;
    $Roaster.$("[data-nav]").forEach(function (a) {
        var target = a.getAttribute("data-nav");
        var row = a.querySelector(".nav-row");
        if (!row) return;
        var active = path === target || path.indexOf(target + "/") === 0;
        if (active) {
            row.classList.add(
                "bg-[var(--ink)]",
                "text-[var(--bg)]",
                "shadow-card",
            );
        }
    });
    $Roaster.$('[data-action="logout"]').forEach(function (b) {
        b.onclick = async function () {
            try {
                if (Roaster.token()) await Roaster.api("/logout", { method: "POST" });
            } catch (e) {}

            Roaster.clearToken();
            location.href = "/login";
        };
    });
    var open = function () {
        Roaster.$("#command-palette") &&
            Roaster.$("#command-palette").classList.remove("hidden");
    };
    var close = function () {
        Roaster.$("#command-palette") && Roaster.$("#command-palette").classList.add("hidden");
    };
    Roaster.$("#open-search") && Roaster.$("#open-search").addEventListener("click", open);
    Roaster.$("#mobile-search") && Roaster.$("#mobile-search").addEventListener("click", open);
    document.addEventListener("keydown", function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "f") {
            e.preventDefault();
            var p = Roaster.$("#command-palette");
            if (p) p.classList.toggle("hidden");
        }
        if (e.key === "Escape") close();
    });
}

// Search bar
function initCommandPalette() {
    const input = Roaster.$("#palette-input");
    const results = Roaster.$("#palette-results");

    if (!input) return;

    input.addEventListener("input", async function (e) {
        const query = e.target.value.trim();

        if (query.length < 2) {
            results.innerHTML = "";
            return;
        }

        try {
            // Search via your API
            const data = await Roaster.api("/search?q=" + encodeURIComponent(query));

            if (!data.length) {
                results.innerHTML =
                    '<div class="p-4 text-sm text-[var(--ink-muted)]">No results found.</div>';
                return;
            }

            // Render results
            results.innerHTML = data
                .map(
                    (r) => `
                <a href="/resumes/${r.id}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--surface-2)] transition-colors">
                    <div class="h-8 w-8 rounded-lg bg-[var(--accent-soft)] flex items-center justify-center">
                        ${Roaster.icon("file-text", "w-4 h-4")}
                    </div>
                    <div>
                        <div class="text-sm font-medium">${Roaster.esc(r.title)}</div>
                    </div>
                </a>
            `,
                )
                .join("");

            Roaster.drawIcons();
        } catch (e) {
            console.error("Search failed", e);
        }
    });
}

function initChart(scoreData) {
    const ctx = Roaster.$("#dashboard-score-chart");
    if (!ctx || !window.Chart) return;

    new Chart(ctx, {
        type: "line",
        data: {
            labels: scoreData.map((_, i) => "ATS Score #" + (i + 1)),
            datasets: [
                {
                    data: scoreData.map((p) => p.score),
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
                        color: getComputedStyle(document.documentElement)
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

function getPageId() {
    return decodeURIComponent(location.pathname.split("/")[2] || "");
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
        $Roaster.$("[data-full-diff-mode]").forEach(function (b) {
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
            var d = await Roaster.api(
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

async function authPage(type) {
    var form = Roaster.$("#" + type + "-form");
    if (!form) return;
    form.onsubmit = async function (e) {
        e.preventDefault();
        var data = Object.fromEntries(new FormData(form).entries());
        var btn = Roaster.$("#" + type + "-submit"),
            err = Roaster.$("#" + type + "-error");
        btn.disabled = true;
        err.classList.add("hidden");

        try {
            console.log("Submitting to API:", "/" + type, data);
            var d = await api("/" + type, { 
                method: "POST", 
                body: data 
            });

            console.log("Response from api():", d);

            if (!d || typeof d !== "object") 
                throw new Error("Server returned an invalid response (expected JSON, got: " + typeof d + ")");

            if (!d.token) 
                throw new Error((d && d.message) || "No authentication token was returned by the server.");

            Roaster.saveToken(d.token);
            location.href = "/dashboard";
        } catch (ex) {
            console.error("Auth error details:", ex);
            if (err) {
                err.textContent = ex.message || "Failed to authenticate.";
                err.classList.remove("hidden");
            } else {
                alert(ex.message || "Failed to authenticate.");
            }
        } finally {
            btn.disabled = false;
        }
    };
}

function landing() {
    var c = Roaster.$("#landing-preview-chart");
    if (c && window.Chart)
        new Chart(c, {
            type: "line",
            data: {
                labels: ["V1", "V2", "V3", "V4", "V5"],
                datasets: [
                    {
                        data: [62, 71, 78, 82, 86],
                        borderColor: "#8AB4CC",
                        backgroundColor: "rgba(138,180,204,.12)",
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        min: 50,
                        max: 100,
                        grid: { color: "rgba(255,255,255,.08)" },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
}

async function main() {
    Roaster.drawIcons();
    initTheme();
    var page = document.body.dataset.page;
    if (page === "landing") {
        landing();
        return;
    }
    if (page === "login" || page === "register") {
        await authPage(page);
        Roaster.drawIcons();
        return;
    }
    var u = await ensureAuth();

    if (!u) return;

    window.__user = u;

    initShell();

    Roaster.drawIcons();
}

document.addEventListener("DOMContentLoaded", function () {
    main();
});
