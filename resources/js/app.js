// Configuration
const existingConfig = window.Roaster || {};
const apiBase = existingConfig.apiBase || "/api";
const tokenKey = existingConfig.tokenKey || "auth_token";

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
        if (window.lucide && window.lucide.createIcons)
            window.lucide.createIcons();
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
        const url = path.startsWith("http")
            ? path
            : `${base}${path.startsWith("/") ? path : "/" + path}`;

        console.log("URL being used: ", url);

        const res = await fetch(url, {
            method: options.method || "GET",
            headers,
            body,
        });

        const rawText = await res.text();
        console.log("HTTP Status:", res.status);
        console.log("Raw Response from server:", rawText);

        let data = null;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            console.error("JSON Parse Error:", e);
            console.error("Failed to parse this raw text as JSON:", rawText);
        }
        
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

function hydrateUser(u) {
    if (!u) return;
    var name = u.name || "Account";
    if (Roaster.$("#sidebar-avatar"))
        Roaster.$("#sidebar-avatar").textContent = (
            name[0] || "R"
        ).toUpperCase();
    if (Roaster.$("#sidebar-name"))
        Roaster.$("#sidebar-name").textContent = name;
    if (Roaster.$("#sidebar-email"))
        Roaster.$("#sidebar-email").textContent = u.email || "";
    if (Roaster.$("#topbar-first-name"))
        Roaster.$("#topbar-first-name").textContent =
            name.split(" ")[0] || "there";
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
        var iconName = theme === "dark" ? "sun" : "moon";

        // Only update if it's different to prevent unnecessary DOM flashing
        if (b.getAttribute("data-current-icon") !== iconName) {
            b.innerHTML = Roaster.icon(iconName, "w-4 h-4");
            b.setAttribute("data-current-icon", iconName);
            Roaster.drawIcons();
        }
    }

    function applyTheme(theme) {
        if (theme === "dark") document.documentElement.classList.add("dark");
        else document.documentElement.classList.remove("dark");

        // Update charts if they exist
        if (window.myChartInstance) {
            const gridColor = getComputedStyle(document.documentElement)
                .getPropertyValue("--grid-color")
                .trim();
            const borderColor = getComputedStyle(document.documentElement)
                .getPropertyValue("--chart-accent")
                .trim();
            window.myChartInstance.data.datasets[0].borderColor = borderColor;
            window.myChartInstance.options.scales.y.grid.color = gridColor;
            window.myChartInstance.update();
        }

        // Update the icon based on the active theme
        updateToggleButton(theme);
    }

    // Determine theme's current state (Checks LocalStorage -> OS Preference)
    var savedTheme = localStorage.getItem("arr-theme");
    var initialTheme =
        savedTheme === "light" || savedTheme === "dark"
            ? savedTheme
            : mediaQuery.matches
              ? "dark"
              : "light";

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
                var currentTheme = document.documentElement.classList.contains(
                    "dark",
                )
                    ? "dark"
                    : "light";
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
    Roaster.$$("[data-nav]").forEach(function (a) {
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
    Roaster.$$('[data-action="logout"]').forEach(function (b) {
        b.onclick = async function () {
            try {
                if (Roaster.token())
                    await Roaster.api("/logout", { method: "POST" });
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
        Roaster.$("#command-palette") &&
            Roaster.$("#command-palette").classList.add("hidden");
    };
    Roaster.$("#open-search") &&
        Roaster.$("#open-search").addEventListener("click", open);
    Roaster.$("#mobile-search") &&
        Roaster.$("#mobile-search").addEventListener("click", open);
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
            const data = await Roaster.api(
                "/search?q=" + encodeURIComponent(query),
            );

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

// To make some functions be available globally, use window.[function_name] = [function_name]
window.initChart = initChart;
window.landing = landing;
window.initTheme = initTheme;
window.getComputedColor = getComputedColor;
window.getComputedColor = getComputedColor;
window.getPageId = getPageId;