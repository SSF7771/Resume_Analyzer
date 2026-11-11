@extends('layouts.app')
@section('title', 'Export - Resume - Roaster')
@section('page', 'export')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>

            <div id="export-page" class="page-enter">
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
            const $ = Roaster.$;
            const $$ = Roaster.$$;
            const esc = Roaster.esc;
            const icon = Roaster.icon;
            const badge = Roaster.badge;
            const card = Roaster.card;
            const empty = Roaster.empty;
            const pageHeader = Roaster.pageHeader;
            const api = Roaster.api.bind(Roaster);
            const drawIcons = Roaster.drawIcons;

            function getPageId() {
                return decodeURIComponent(location.pathname.split("/")[2] || "");
            }

            async function renderExport() {
                var root = $("#export-page");
                if (!root) return;
                var id = getPageId();

                try {
                    var d = await api("/resumes/" + encodeURIComponent(id));
                    var resume = d.resume || (d.data && d.data.resume) || d;
                    var versions = d.versions || (d.data && d.data.versions) || [];
                    var active =
                        resume.current_version_id ||
                        (versions[versions.length - 1] && versions[versions.length - 1].id);

                    function resumeHtml(v) {
                        const s = (v && v.parsed_sections) || {};
                        const b = s.basics || {};

                        let h = `<div>
                                <h1 class="text-3xl font-bold tracking-tight">${esc(b.name || resume.title || "Resume")}</h1>
                                ${b.title ? `<div class="text-base mt-1 text-[var(--accent-strong)]">${esc(b.title)}</div>` : ""}
                                <div class="text-xs text-gray-500 mt-2">
                                    ${[b.email, b.phone, b.location].filter(Boolean).map(esc).join(" · ")}
                                </div>`;

                        // Summary
                        if (s.summary) {
                            h += `<section class="mt-6"><h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Summary</h2><p class="text-sm mt-2 leading-relaxed">${esc(s.summary)}</p></section>`;
                        }

                        // Experience
                        if (Array.isArray(s.experience) && s.experience.length > 0) {
                            h += `<section class="mt-5"><h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Experience</h2><div class="mt-3 space-y-4">`;
                            h += s.experience
                                .map(
                                    (e) => `
                                <div>
                                    <div class="font-semibold text-sm">${esc(e.role || "")}</div>
                                    <div class="text-xs text-gray-500">${esc(e.company || "")}${e.period ? " · " + esc(e.period) : ""}</div>
                                    ${Array.isArray(e.bullet_points) && e.bullet_points.length > 0
                                            ? `<ul class="list-disc ml-5 mt-1 text-sm space-y-1">${e.bullet_points.map((x) => `<li>${esc(x)}</li>`).join("")}</ul>`
                                            : ""
                                        }
                                </div>
                            `,
                                )
                                .join("");
                            h += `</div></section>`;
                        }

                        // Education
                        if (Array.isArray(s.education) && s.education.length > 0) {
                            h += `<section class="mt-5"><h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Education</h2><div class="mt-3 space-y-2">`;
                            h += s.education
                                .map(
                                    (e) => `
                                <div class="text-sm"><span class="font-semibold">${esc(e.degree || "")}</span>${e.school ? " · " + esc(e.school) : ""}</div>
                            `,
                                )
                                .join("");
                            h += `</div></section>`;
                        }

                        // Skills
                        if (Array.isArray(s.skills) && s.skills.length > 0) {
                            h += `<section class="mt-5"><h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Skills</h2>
                          <div class="text-sm mt-2">${esc(s.skills.join(" · "))}</div></section>`;
                        }

                        // Certifications
                        if (Array.isArray(s.certifications) && s.certifications.length > 0) {
                            h +=
                                `<section class="mt-5"><h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Certifications</h2>
                          <div class="mt-3 space-y-2">` +
                                s.certifications
                                    .map(
                                        (c) => `
                              <div class="text-sm"><span class="font-semibold">${esc(c.name)}</span> ${c.issuer ? " · " + esc(c.issuer) : ""}</div>
                          `,
                                    )
                                    .join("") +
                                `</div></section>`;
                        }

                        return h + "</div>";
                    }

                    function draw() {
                        var v =
                            versions.find(function (x) {
                                return String(x.id) === String(active);
                            }) || versions[versions.length - 1];

                        var buttons = versions
                            .map(function (x) {
                                return (
                                    '<button data-export-version="' +
                                    esc(x.id) +
                                    '" class="h-9 px-3 rounded-full border text-xs font-semibold transition-all ' +
                                    (String(x.id) === String(active)
                                        ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-sm"
                                        : "text-[var(--muted-foreground)] hover:bg-[var(--secondary)]") +
                                    '">' +
                                    esc(x.label || "Version") +
                                    "</button>"
                                );
                            })
                            .join("");

                        root.innerHTML =
                            pageHeader(
                                "Export — " + (resume.title || "Resume"),
                                "Preview a clean ATS-friendly PDF and download when it looks right.",
                                '<a href="/resumes/' +
                                encodeURIComponent(id) +
                                '" class="inline-flex items-center gap-2 h-10 px-4 rounded-2xl border border-[var(--border)] hover:bg-[var(--surface-2)] text-sm font-semibold transition-all shadow-sm">' +
                                icon("arrow-left") +
                                " Back to resume</a>",
                            ) +
                            card(
                                '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"><div><div class="font-display text-base font-semibold">Version</div><div class="text-sm text-[var(--ink-muted)] mt-1">Choose which version to export</div></div><div class="flex items-center gap-2 flex-wrap">' +
                                buttons +
                                '<button id="download-pdf" class="h-10 px-4 rounded-2xl text-white text-sm font-semibold flex items-center gap-2 shadow-md hover:opacity-95 transition-all" style="background:linear-gradient(135deg,#3B6E8C,#1A3644)">' +
                                icon("download") +
                                " Download PDF</button></div></div>" +
                                (v && v.source_type === "rewrite"
                                    ? '<div class="mt-4">' +
                                    badge("AI-improved", "accent") +
                                    ' <span class="text-xs text-[var(--ink-muted)] ml-1">This version was generated by applying AI rewrites.</span></div>'
                                    : "") +
                                "</div>",
                            ) +
                            '<div class="mt-6 rounded-[20px] border border-[var(--border)] bg-white shadow-card p-8 sm:p-10"><div id="resume-print" class="max-w-[780px] mx-auto text-[#111827]">' +
                            resumeHtml(v) +
                            "</div></div>";

                        drawIcons();

                        $$("[data-export-version]").forEach(function (b) {
                            b.onclick = function () {
                                active = b.dataset.exportVersion;
                                draw();
                            };
                        });

                        $("#download-pdf").onclick = function () {
                            var el = $("#resume-print");
                            var filename =
                                (resume.title || "resume")
                                    .replace(/[^a-z0-9\-_ ]/gi, "")
                                    .trim()
                                    .replace(/\s+/g, "_") +
                                "_" +
                                ((v && v.label) || "V1") +
                                ".pdf";

                            if (window.html2pdf) {
                                html2pdf()
                                    .set({
                                        margin: 0,
                                        filename: filename,
                                        image: { type: "jpeg", quality: 0.98 },
                                        html2canvas: {
                                            scale: 2,
                                            useCORS: true,
                                            logging: false,
                                            windowWidth: el.scrollWidth,
                                        },
                                        jsPDF: {
                                            format: "a4",
                                            unit: "in",
                                            orientation: "portrait",
                                        },
                                    })
                                    .from(document.getElementById("resume-print"))
                                    .save();
                            } else {
                                window.print();
                            }
                        };
                    }
                    draw();
                } catch (e) {
                    root.innerHTML = empty(
                        "file-text",
                        "Couldn't load this version",
                        e.message,
                    );
                    drawIcons();
                }
            }

            renderExport();
        });
    </script>
@endpush