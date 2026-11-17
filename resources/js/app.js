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
            neutral: "bg-[var(--secondary)] text-[var(--secondary-foreground)] border-[var(--border)]",
            accent: "bg-[var(--accent-soft)] text-[var(--accent-strong)] border-[var(--primary)]/20",
            success: "bg-[var(--success)]/12 text-[var(--success)] border-[var(--success)]/25",
            warning: "bg-[var(--warning)]/15 text-[var(--warning)] border-[var(--warning)]/30",
            danger: "bg-[var(--danger)]/12 text-[var(--danger)] border-[var(--danger)]/25",
        };
        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[10px] font-bold ${map[tone] || map.neutral}">${this.esc(text)}</span>`;
    },

    card: (inner, extra = "") =>
        `<div data-spotlight class="roaster-card rounded-[22px] p-5 ${extra}">${inner}</div>`,

    empty: function (ic, title, desc, action = "") {
        return `
            <div class="relative overflow-hidden rounded-[24px] border border-dashed border-[var(--border)] bg-[var(--surface)] p-10 flex flex-col items-center justify-center text-center min-h-[260px]">
                <div class="absolute -top-16 left-1/2 -translate-x-1/2 h-40 w-40 rounded-full bg-brand opacity-15 blur-3xl pointer-events-none"></div>
                <div class="relative float-slow h-16 w-16 rounded-3xl brand-logo flex items-center justify-center mb-5">
                    ${this.icon(ic, "w-7 h-7")}
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
                    <div class="h-1 w-10 rounded-full bg-brand mb-3"></div>
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight">${this.esc(title)}</h2>
                    <p class="text-sm text-[var(--ink-muted)] mt-1.5">${this.esc(desc)}</p>
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

        var tones = {
            error: { icon: "alert-circle", color: "var(--danger)" },
            warning: { icon: "alert-triangle", color: "var(--warning)" },
            info: { icon: "info", color: "var(--primary)" },
            success: { icon: "check-circle-2", color: "var(--success)" },
        };
        var t = tones[variant] || tones.success;
        var duration = 4200;

        var el = document.createElement("div");
        el.className =
            "toast pointer-events-auto glass shadow-hover rounded-2xl p-3.5 pr-3 flex items-start gap-3";
        el.setAttribute("role", "status");
        el.innerHTML =
            '<div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0" style="background:color-mix(in oklch,' +
            t.color + ' 15%,transparent);color:' + t.color + '">' +
            this.icon(t.icon, "w-4 h-4") +
            '</div><div class="flex-1 min-w-0 pt-0.5"><div class="text-sm font-bold">' +
            this.esc(title) +
            "</div>" +
            (description
                ? '<div class="text-xs text-[var(--muted-foreground)] mt-0.5">' + this.esc(description) + "</div>"
                : "") +
            '</div><button type="button" aria-label="Close" class="h-7 w-7 rounded-lg flex items-center justify-center text-[var(--muted-foreground)] hover:bg-[var(--secondary)] transition-colors">' +
            this.icon("x", "w-4 h-4") +
            '</button><span class="toast-progress" style="background:' + t.color + ";animation-duration:" + duration + 'ms"></span>';

        root.appendChild(el);
        this.drawIcons();

        var closed = false;
        var close = function () {
            if (closed) return;
            closed = true;
            el.classList.add("leaving");
            setTimeout(function () { el.remove(); }, 300);
        };
        el.querySelector("button").onclick = close;

        // Pause auto-dismiss while hovering
        var timer = setTimeout(close, duration);
        var bar = el.querySelector(".toast-progress");
        el.addEventListener("mouseenter", function () {
            clearTimeout(timer);
            bar.style.animationPlayState = "paused";
        });
        el.addEventListener("mouseleave", function () {
            bar.style.animationPlayState = "running";
            timer = setTimeout(close, 1500);
        });
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

        // console.log("URL being used: ", url);

        const res = await fetch(url, {
            method: options.method || "GET",
            headers,
            body,
        });

        const rawText = await res.text();

        // Debug
        // console.log("HTTP Status:", res.status);
        // console.log("Raw Response from server:", rawText);

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
            // the icon spins in when it changes (skip on first paint)
            var spin = b.hasAttribute("data-current-icon") ? " theme-icon-in" : "";
            b.innerHTML = Roaster.icon(iconName, "w-4 h-4" + spin);
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

    // Public helper so other UI (e.g. Settings > Appearance) can switch theme with the same animation
    Roaster.setTheme = function (theme, origin) {
        localStorage.setItem("arr-theme", theme);
        if (document.documentElement.classList.contains("dark") === (theme === "dark")) return Promise.resolve();
        switchThemeAnimated(theme, origin || Roaster.$("#theme-toggle") || document.body, applyTheme);
        return new Promise(function (r) { setTimeout(r, 30); });
    };

    // Handle button click toggle
    var b = Roaster.$("#theme-toggle");
    if (b) {
        b.onclick = function () {
            // Check the DOM class directly to see what the active theme is right now
            var isDark = document.documentElement.classList.contains("dark");
            var newTheme = isDark ? "light" : "dark";

            localStorage.setItem("arr-theme", newTheme);
            switchThemeAnimated(newTheme, b, applyTheme);
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

// Theme switch animation: a circle grows out of the toggle button and reveals
// the new theme (View Transitions API). Browsers without it get a smooth
// colour cross-fade instead.
function switchThemeAnimated(theme, button, apply) {
    var root = document.documentElement;
    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!document.startViewTransition || reduce) {
        root.classList.add("theme-switching");
        apply(theme);
        clearTimeout(switchThemeAnimated.t);
        switchThemeAnimated.t = setTimeout(function () {
            root.classList.remove("theme-switching");
        }, 500);
        return;
    }

    var r = button.getBoundingClientRect();
    var x = r.left + r.width / 2;
    var y = r.top + r.height / 2;
    var radius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

    root.classList.add("theme-vt");
    var transition = document.startViewTransition(function () {
        apply(theme);
    });
    transition.finished.finally(function () {
        root.classList.remove("theme-vt");
    });

    transition.ready.then(function () {
        root.animate(
            {
                clipPath: [
                    "circle(0px at " + x + "px " + y + "px)",
                    "circle(" + radius + "px at " + x + "px " + y + "px)",
                ],
            },
            {
                duration: 650,
                easing: "cubic-bezier(0.65, 0, 0.35, 1)",
                pseudoElement: "::view-transition-new(root)",
            },
        );
    });
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
        if (active) row.classList.add("nav-active");
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
        var p = Roaster.$("#command-palette");
        if (!p) return;
        p.classList.remove("hidden");
        var input = Roaster.$("#palette-input");
        if (input) setTimeout(function () { input.focus(); }, 30);
    };
    var close = function () {
        Roaster.$("#command-palette") &&
            Roaster.$("#command-palette").classList.add("hidden");
    };
    Roaster.$("#open-search") &&
        Roaster.$("#open-search").addEventListener("click", open);
    Roaster.$("#mobile-search") &&
        Roaster.$("#mobile-search").addEventListener("click", open);
    // Click on the dark backdrop closes the palette
    var palette = Roaster.$("#command-palette");
    if (palette)
        palette.addEventListener("click", function (e) {
            if (e.target === palette) close();
        });

    document.addEventListener("keydown", function (e) {
        var k = e.key.toLowerCase();
        if ((e.metaKey || e.ctrlKey) && (k === "f" || k === "k")) {
            e.preventDefault();
            var p = Roaster.$("#command-palette");
            if (p && p.classList.contains("hidden")) open();
            else close();
        }
        if (e.key === "Escape") close();
    });
}

// Search bar
function initCommandPalette() {
    const input = Roaster.$("#palette-input");
    const results = Roaster.$("#palette-results");

    if (!input) return;

    const defaultResults = results.innerHTML;

    input.addEventListener("input", async function (e) {
        const query = e.target.value.trim();

        if (query.length < 2) {
            results.innerHTML = defaultResults;
            Roaster.drawIcons();
            return;
        }

        results.innerHTML =
            '<div class="p-4 text-sm text-[var(--ink-muted)] flex items-center gap-2">' +
            Roaster.icon("loader-2", "w-4 h-4 animate-spin") + " Searching…</div>";
        Roaster.drawIcons();

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
                    <div class="icon-tile h-8 w-8 rounded-lg">
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

// Vertical gradient fill for line charts
function makeGradient(canvas, height) {
    const g = canvas.getContext("2d").createLinearGradient(0, 0, 0, height || 240);
    const rgb = getComputedColor("--chart-fill-rgb") || "71, 98, 130";
    g.addColorStop(0, `rgba(${rgb}, 0.3)`);
    g.addColorStop(1, `rgba(${rgb}, 0)`);
    return g;
}

function chartTooltip() {
    return {
        backgroundColor: getComputedColor("--card") || "#fff",
        titleColor: getComputedColor("--foreground") || "#111",
        bodyColor: getComputedColor("--muted-foreground") || "#555",
        borderColor: getComputedColor("--border") || "#ddd",
        borderWidth: 1,
        padding: 12,
        cornerRadius: 12,
        displayColors: false,
        titleFont: { weight: "700" },
    };
}

if (window.Chart) {
    Chart.defaults.font.family = '"Plus Jakarta Sans", ui-sans-serif, system-ui, sans-serif';
}

function initChart(scoreData) {
    const ctx = Roaster.$("#dashboard-score-chart");
    if (!ctx || !window.Chart) return;

    window.myChartInstance = new Chart(ctx, {
        type: "line",
        data: {
            labels: scoreData.map((_, i) => "ATS Score #" + (i + 1)),
            datasets: [
                {
                    data: scoreData.map((p) => p.score),
                    borderColor: getComputedColor("--chart-accent"),
                    borderWidth: 3,
                    backgroundColor: makeGradient(ctx, 260),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: getComputedColor("--card"),
                    pointBorderColor: getComputedColor("--chart-accent"),
                    pointBorderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1400, easing: "easeOutQuart" },
            interaction: { intersect: false, mode: "index" },
            plugins: {
                legend: { display: false },
                tooltip: chartTooltip(),
            },
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
                    ticks: { stepSize: 20, color: getComputedColor("--muted-foreground"), font: { size: 11 } },
                    border: { display: false },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: getComputedColor("--muted-foreground"),
                        font: { size: 11 },
                        maxRotation: 0,
                        callback: (_, i) => "#" + (i + 1),
                    },
                    border: { display: false },
                },
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
                        borderColor: getComputedColor("--chart-accent"),
                        borderWidth: 3,
                        backgroundColor: makeGradient(c, 112),
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: getComputedColor("--chart-accent"),
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1600, easing: "easeOutQuart" },
                interaction: { intersect: false, mode: "index" },
                plugins: { legend: { display: false }, tooltip: chartTooltip() },
                scales: {
                    y: {
                        min: 50,
                        max: 100,
                        grid: { color: getComputedColor("--grid-color") },
                        ticks: { color: getComputedColor("--muted-foreground"), font: { size: 10 } },
                        border: { display: false },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: getComputedColor("--muted-foreground"), font: { size: 10 } },
                        border: { display: false },
                    },
                },
            },
        });
}

async function main() {
    Roaster.drawIcons();
    initTheme();
    initInteractions();
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
window.makeGradient = makeGradient;
window.chartTooltip = chartTooltip;
/* 
    data-reveal[="left|right|zoom"]  fade/slide in when scrolled into view
    data-count="86"                  number counts up when visible
    data-spotlight                   glow follows the mouse inside a card
    data-tilt                        3D tilt on mouse move
    data-magnetic                    button gently follows the cursor
    data-typewriter data-words='[]'  rotating typed words
    data-tabs / data-tab / data-tab-panel   auto-rotating tabs
    data-accordion                   FAQ accordion
    data-steps                       animated connector line
    data-score-ring="86"             SVG ring fills to the score
    data-dropzone                    drag & drop for a file input inside
    input[type=password]             gets a show/hide toggle
    input[data-strength]             password strength meter
*/
const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const isTouch = window.matchMedia("(hover: none)").matches;

const revealObserver =
    "IntersectionObserver" in window
        ? new IntersectionObserver(
              (entries) => {
                  entries.forEach((entry) => {
                      if (!entry.isIntersecting) return;
                      const el = entry.target;
                      el.classList.add("is-visible");
                      if (el.hasAttribute("data-count")) countUp(el);
                      if (el.hasAttribute("data-score-ring")) fillRing(el);
                      if (el.hasAttribute("data-steps-line")) el.style.transform = "scaleX(1)";
                      revealObserver.unobserve(el);
                  });
              },
              { threshold: 0.15, rootMargin: "0px 0px -40px 0px" },
          )
        : null;

function observe(el) {
    if (el.__observed) return;
    el.__observed = true;
    if (revealObserver) revealObserver.observe(el);
    else {
        el.classList.add("is-visible");
        if (el.hasAttribute("data-count")) countUp(el);
        if (el.hasAttribute("data-score-ring")) fillRing(el);
    }
}

function countUp(el) {
    const target = parseFloat(el.getAttribute("data-count"));
    if (isNaN(target)) return;
    const decimals = (String(target).split(".")[1] || "").length;
    if (prefersReducedMotion) {
        el.textContent = target.toFixed(decimals);
        return;
    }
    const duration = 1400;
    const start = performance.now();
    const step = (now) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 4);
        el.textContent = (target * eased).toFixed(decimals);
        if (p < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

function fillRing(svg) {
    const score = Math.max(0, Math.min(100, parseFloat(svg.getAttribute("data-score-ring")) || 0));
    const circle = svg.querySelector("circle.progress");
    if (!circle) return;
    const len = parseFloat(circle.getAttribute("stroke-dasharray"));
    requestAnimationFrame(() => {
        circle.style.strokeDashoffset = String(len * (1 - score / 100));
    });
}

function bindSpotlight(el) {
    if (el.__spot || isTouch) return;
    el.__spot = true;
    el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect();
        el.style.setProperty("--mx", e.clientX - r.left + "px");
        el.style.setProperty("--my", e.clientY - r.top + "px");
    });
}

function bindTilt(el) {
    if (el.__tilt || isTouch || prefersReducedMotion) return;
    el.__tilt = true;
    el.style.transition = "transform .4s cubic-bezier(.16,1,.3,1)";
    el.style.transformStyle = "preserve-3d";
    el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        el.style.transform = `perspective(1000px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg)`;
    });
    el.addEventListener("pointerleave", () => {
        el.style.transform = "";
    });
}

function bindMagnetic(el) {
    if (el.__mag || isTouch || prefersReducedMotion) return;
    el.__mag = true;
    el.addEventListener("pointermove", (e) => {
        const r = el.getBoundingClientRect();
        const x = e.clientX - r.left - r.width / 2;
        const y = e.clientY - r.top - r.height / 2;
        el.style.transform = `translate(${x * 0.18}px, ${y * 0.25}px)`;
    });
    el.addEventListener("pointerleave", () => {
        el.style.transform = "";
    });
}

// Click ripple on primary buttons (delegated, works for JS-rendered buttons too)
function initRipple() {
    document.addEventListener("pointerdown", (e) => {
        const btn = e.target.closest(".roaster-primary-button, .roaster-blue-gradient-btn");
        if (!btn || btn.disabled) return;
        const r = btn.getBoundingClientRect();
        const size = Math.max(r.width, r.height);
        const span = document.createElement("span");
        span.className = "ripple";
        span.style.width = span.style.height = size + "px";
        span.style.left = e.clientX - r.left - size / 2 + "px";
        span.style.top = e.clientY - r.top - size / 2 + "px";
        btn.appendChild(span);
        setTimeout(() => span.remove(), 650);
    });
}

function initScrollEffects() {
    const bar = Roaster.$("#scroll-progress");
    const nav = Roaster.$("#landing-nav");
    const spyLinks = Roaster.$$("[data-scrollspy]");
    let ticking = false;

    const update = () => {
        const h = document.documentElement;
        const max = h.scrollHeight - h.clientHeight;
        if (bar) bar.style.transform = `scaleX(${max > 0 ? h.scrollTop / max : 0})`;
        if (nav) nav.classList.toggle("nav-scrolled", h.scrollTop > 12);

        if (spyLinks.length) {
            let current = null;
            spyLinks.forEach((a) => {
                const sec = document.getElementById(a.dataset.scrollspy);
                if (sec && sec.getBoundingClientRect().top < window.innerHeight * 0.4) current = a;
            });
            spyLinks.forEach((a) => {
                const on = a === current;
                a.classList.toggle("bg-[var(--card)]", on);
                a.classList.toggle("text-[var(--foreground)]", on);
                a.classList.toggle("shadow-card", on);
            });
        }
        ticking = false;
    };

    window.addEventListener(
        "scroll",
        () => {
            if (!ticking) {
                requestAnimationFrame(update);
                ticking = true;
            }
        },
        { passive: true },
    );
    update();
}

function initMobileMenu() {
    const btn = Roaster.$("#mobile-menu-toggle");
    const menu = Roaster.$("#mobile-menu");
    if (!btn || !menu) return;
    const set = (open) => {
        menu.style.gridTemplateRows = open ? "1fr" : "0fr";
        btn.setAttribute("aria-expanded", String(open));
        btn.innerHTML = Roaster.icon(open ? "x" : "menu", "w-5 h-5");
        Roaster.drawIcons();
    };
    btn.addEventListener("click", () => set(btn.getAttribute("aria-expanded") !== "true"));
    Roaster.$$("a", menu).forEach((a) => a.addEventListener("click", () => set(false)));
}

function initTypewriter() {
    Roaster.$$("[data-typewriter]").forEach((el) => {
        let words;
        try {
            words = JSON.parse(el.dataset.words || "[]");
        } catch (e) {
            return;
        }
        if (!words.length || prefersReducedMotion) return;
        el.classList.add("type-caret");
        let w = 0;
        let i = words[0].length;
        let deleting = true;
        const tick = () => {
            const word = words[w];
            i += deleting ? -1 : 1;
            el.textContent = word.slice(0, i);
            let delay = deleting ? 45 : 90;
            if (!deleting && i === word.length) {
                deleting = true;
                delay = 1800;
            } else if (deleting && i === 0) {
                deleting = false;
                w = (w + 1) % words.length;
                delay = 300;
            }
            setTimeout(tick, delay);
        };
        setTimeout(tick, 2200);
    });
}

function initTabs() {
    Roaster.$$("[data-tabs]").forEach((root) => {
        const tabs = Roaster.$$("[data-tab]", root);
        const panels = Roaster.$$("[data-tab-panel]", root);
        if (!tabs.length) return;
        const interval = 5000;
        let index = 0;
        let timer = null;

        const show = (i, auto) => {
            index = i;
            tabs.forEach((t, n) => {
                const on = n === i;
                t.setAttribute("aria-selected", String(on));
                const prog = t.querySelector(".tab-progress");
                if (!prog) return;
                prog.classList.toggle("hidden", !on || !auto);
                const bar = prog.querySelector("span");
                bar.style.transition = "none";
                bar.style.transform = "scaleX(0)";
                if (on && auto) {
                    requestAnimationFrame(() =>
                        requestAnimationFrame(() => {
                            bar.style.transition = `transform ${interval}ms linear`;
                            bar.style.transform = "scaleX(1)";
                        }),
                    );
                }
            });
            const name = tabs[i].dataset.tab;
            panels.forEach((p) => {
                const on = p.dataset.tabPanel === name;
                p.classList.toggle("hidden", !on);
                if (on) {
                    p.classList.remove("page-enter");
                    void p.offsetWidth;
                    p.classList.add("page-enter");
                    // replay bar animations inside the panel
                    Roaster.$$(".bar > span", p).forEach((b) => {
                        b.style.animation = "none";
                        void b.offsetWidth;
                        b.style.animation = "";
                    });
                }
            });
        };

        const start = () => {
            if (prefersReducedMotion) return;
            clearInterval(timer);
            timer = setInterval(() => show((index + 1) % tabs.length, true), interval);
        };

        tabs.forEach((t, n) =>
            t.addEventListener("click", () => {
                // user picked a tab: stop auto-rotating
                clearInterval(timer);
                show(n, false);
            }),
        );
        show(0, !prefersReducedMotion);
        start();
    });
}

function initAccordion() {
    Roaster.$$("[data-accordion]").forEach((root) => {
        Roaster.$$(".accordion-item", root).forEach((item) => {
            const trigger = item.querySelector(".accordion-trigger");
            trigger.addEventListener("click", () => {
                const open = trigger.getAttribute("aria-expanded") === "true";
                // close others
                Roaster.$$(".accordion-item", root).forEach((other) => {
                    other.querySelector(".accordion-trigger").setAttribute("aria-expanded", "false");
                    other.querySelector(".accordion-panel").style.gridTemplateRows = "0fr";
                    other.querySelector(".accordion-icon").style.transform = "";
                    other.classList.remove("border-[var(--primary)]/40");
                });
                if (!open) {
                    trigger.setAttribute("aria-expanded", "true");
                    item.querySelector(".accordion-panel").style.gridTemplateRows = "1fr";
                    item.querySelector(".accordion-icon").style.transform = "rotate(45deg)";
                    item.classList.add("border-[var(--primary)]/40");
                }
            });
        });
    });
}

// Drag & drop + client-side validation for any [data-dropzone] wrapping a file input
function bindDropzone(zone) {
    if (zone.__dz) return;
    const input = zone.querySelector('input[type="file"]');
    if (!input) return;
    zone.__dz = true;
    zone.classList.add("dropzone");
    const maxMb = parseFloat(zone.dataset.maxMb || "5");

    const accept = (file) => {
        if (!file) return false;
        const isPdf = file.type === "application/pdf" || /\.pdf$/i.test(file.name);
        if (!isPdf) {
            Roaster.toast("Only PDF files are supported", file.name, "error");
            return false;
        }
        if (file.size > maxMb * 1024 * 1024) {
            Roaster.toast("File is too large", `Maximum size is ${maxMb}MB`, "error");
            return false;
        }
        return true;
    };

    ["dragenter", "dragover"].forEach((ev) =>
        zone.addEventListener(ev, (e) => {
            e.preventDefault();
            zone.classList.add("is-dragover");
        }),
    );
    ["dragleave", "dragend"].forEach((ev) =>
        zone.addEventListener(ev, (e) => {
            if (!zone.contains(e.relatedTarget)) zone.classList.remove("is-dragover");
        }),
    );
    zone.addEventListener("drop", (e) => {
        e.preventDefault();
        zone.classList.remove("is-dragover");
        const file = e.dataTransfer.files[0];
        if (!accept(file)) return;
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        input.dispatchEvent(new Event("change", { bubbles: true }));
    });
    input.addEventListener("change", () => {
        const file = input.files[0];
        if (file && !accept(file)) {
            input.value = "";
            zone.classList.remove("has-file");
            return;
        }
        zone.classList.toggle("has-file", !!file);
        if (file) {
            const size = (file.size / 1024 / 1024).toFixed(2);
            const label = zone.querySelector("#file-name");
            // page scripts set the name; append size after they run
            setTimeout(() => {
                if (label && label.textContent && !label.dataset.sized) {
                    label.innerHTML =
                        Roaster.icon("file-check-2", "w-3.5 h-3.5 inline-block -mt-0.5 mr-1") +
                        Roaster.esc(file.name) + ` <span class="text-[var(--muted-foreground)]">· ${size} MB</span>`;
                    Roaster.drawIcons();
                }
            }, 0);
        }
    });
}

function bindPasswordToggle(input) {
    if (input.__pw || input.closest("[data-no-toggle]")) return;
    input.__pw = true;
    const wrap = document.createElement("div");
    wrap.className = "relative";
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);
    input.style.paddingRight = "3rem";
    const btn = document.createElement("button");
    btn.type = "button";
    btn.setAttribute("aria-label", "Show password");
    btn.className =
        "absolute right-2 top-1/2 -translate-y-1/2 h-9 w-9 rounded-xl flex items-center justify-center text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)] transition-colors";
    btn.innerHTML = Roaster.icon("eye", "w-4 h-4");
    wrap.appendChild(btn);
    btn.addEventListener("click", () => {
        const show = input.type === "password";
        input.type = show ? "text" : "password";
        btn.setAttribute("aria-label", show ? "Hide password" : "Show password");
        btn.innerHTML = Roaster.icon(show ? "eye-off" : "eye", "w-4 h-4");
        Roaster.drawIcons();
    });
}

function bindStrength(input) {
    if (input.__str) return;
    input.__str = true;
    const meter = document.createElement("div");
    meter.className = "mt-2";
    meter.innerHTML =
        '<div class="grid grid-cols-4 gap-1.5">' +
        '<span class="h-1.5 rounded-full bg-[var(--border)] transition-colors duration-300"></span>'.repeat(4) +
        '</div><div class="text-[11px] mt-1.5 text-[var(--muted-foreground)] font-medium" data-strength-label>Use 8+ characters with numbers & symbols</div>';
    (input.closest(".relative") || input).insertAdjacentElement("afterend", meter);
    const bars = Roaster.$$("span", meter);
    const label = meter.querySelector("[data-strength-label]");
    const levels = [
        ["Too short", "var(--danger)"],
        ["Weak", "var(--danger)"],
        ["Okay", "var(--warning)"],
        ["Good", "var(--success)"],
        ["Strong", "var(--success)"],
    ];
    input.addEventListener("input", () => {
        const v = input.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        if (v.length < 8) score = Math.min(score, 1);
        bars.forEach((b, i) => (b.style.background = i < score ? levels[score][1] : ""));
        label.textContent = v ? levels[score][0] : "Use 8+ characters with numbers & symbols";
        label.style.color = v ? levels[score][1] : "";
    });
}

function greet() {
    const el = Roaster.$("#topbar-greeting");
    if (!el) return;
    const h = new Date().getHours();
    el.textContent = h < 11 ? "Good morning" : h < 15 ? "Good afternoon" : h < 19 ? "Good evening" : "Good night";
}

// Applies the behaviours to an element subtree (used on load and for JS-rendered content)
function enhance(root) {
    const scope = root || document;
    const pick = (sel) => {
        const list = Roaster.$$(sel, scope);
        if (scope.matches && scope.matches(sel)) list.unshift(scope);
        return list;
    };
    pick("[data-reveal], [data-count], [data-score-ring], [data-steps-line]").forEach(observe);
    pick("[data-spotlight]").forEach(bindSpotlight);
    pick("[data-tilt]").forEach(bindTilt);
    pick("[data-magnetic]").forEach(bindMagnetic);
    pick("[data-dropzone]").forEach(bindDropzone);
    pick('input[type="password"]').forEach(bindPasswordToggle);
    pick("input[data-strength]").forEach(bindStrength);
}

function initInteractions() {
    if (window.__interactionsReady) return;
    window.__interactionsReady = true;
    document.documentElement.classList.add("interactive");

    initRipple();
    initScrollEffects();
    initMobileMenu();
    initTypewriter();
    initTabs();
    initAccordion();
    greet();
    enhance(document);
    Roaster.drawIcons();

    // Pages render most of their content with JS (innerHTML) after API calls,
    // so watch for new nodes and enhance them too.
    let pending = [];
    let scheduled = false;
    new MutationObserver((mutations) => {
        mutations.forEach((m) =>
            m.addedNodes.forEach((n) => {
                if (n.nodeType === 1) pending.push(n);
            }),
        );
        if (!scheduled && pending.length) {
            scheduled = true;
            requestAnimationFrame(() => {
                const nodes = pending;
                pending = [];
                scheduled = false;
                nodes.forEach((n) => n.isConnected && enhance(n));
            });
        }
    }).observe(document.body, { childList: true, subtree: true });
}

Roaster.enhance = enhance;
window.initInteractions = initInteractions;

// Lightweight confetti burst (no library). Used when a score improves or rewrites are applied.
Roaster.confetti = function (opts) {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const o = Object.assign({ count: 140, duration: 2600 }, opts || {});
    const canvas = document.createElement("canvas");
    canvas.className = "no-print";
    canvas.style.cssText = "position:fixed;inset:0;width:100%;height:100%;pointer-events:none;z-index:250";
    document.body.appendChild(canvas);
    const ctx = canvas.getContext("2d");
    const dpr = window.devicePixelRatio || 1;
    canvas.width = innerWidth * dpr;
    canvas.height = innerHeight * dpr;
    ctx.scale(dpr, dpr);

    const colors = ["#1f2937", "#475569", "#94a3b8", "#cbd5e1", "#5b7fa6", "#22c55e", "#e2e8f0"];
    const pieces = Array.from({ length: o.count }, () => {
        const fromLeft = Math.random() < 0.5;
        return {
            x: fromLeft ? -10 : innerWidth + 10,
            y: innerHeight * (0.55 + Math.random() * 0.3),
            vx: (fromLeft ? 1 : -1) * (6 + Math.random() * 9),
            vy: -(10 + Math.random() * 12),
            w: 6 + Math.random() * 6,
            h: 8 + Math.random() * 10,
            r: Math.random() * Math.PI,
            vr: (Math.random() - 0.5) * 0.35,
            c: colors[(Math.random() * colors.length) | 0],
        };
    });

    const start = performance.now();
    (function frame(now) {
        const t = now - start;
        ctx.clearRect(0, 0, innerWidth, innerHeight);
        pieces.forEach((p) => {
            p.vy += 0.38;
            p.vx *= 0.985;
            p.x += p.vx;
            p.y += p.vy;
            p.r += p.vr;
            ctx.save();
            ctx.globalAlpha = Math.max(0, 1 - t / o.duration);
            ctx.translate(p.x, p.y);
            ctx.rotate(p.r);
            ctx.fillStyle = p.c;
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, Math.cos(p.r * 2) * p.h);
            ctx.restore();
        });
        if (t < o.duration) requestAnimationFrame(frame);
        else canvas.remove();
    })(start);
};
