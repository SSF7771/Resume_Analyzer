@extends('layouts.app')
@section('title', 'Your Resumes - Roaster')
@section('page', 'resumes')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.topbar')</div>

            <div id="resumes-page" class="page-enter">
                <div id="resumes-header-container"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">
                    <!-- Upload Section -->
                    <div class="lg:col-span-4">
                        <div class="sticky top-6">
                            <x-ui.card>
                                <div class="font-display text-lg font-semibold">Upload resume</div>
                                <form id="upload-form" class="space-y-4 mt-6">
                                    @csrf
                                    <label
                                        class="group flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--surface-2)] hover:border-[var(--primary)] transition-colors p-8 text-center cursor-pointer">
                                        <input id="resume-file" type="file" name="file" accept="application/pdf"
                                            class="hidden">
                                        <div
                                            class="h-14 w-14 rounded-2xl bg-[var(--accent-soft)] text-[var(--accent-strong)] flex items-center justify-center">
                                            <i data-lucide="upload-cloud" class="w-7 h-7"></i>
                                        </div>
                                        <div class="font-semibold mt-4">Drop PDF here</div>
                                        <div class="text-xs text-[var(--ink-muted)] mt-1">Maximum 5MB</div>
                                        <div id="file-name" class="text-xs font-medium text-[var(--primary)] mt-3"></div>
                                    </label>
                                    <input id="resume-title" placeholder="Optional title"
                                        class="w-full h-12 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] text-[var(--foreground)] placeholder:text-[var(--muted-foreground)] outline-none focus:border-[var(--primary)]">
                                    <button id="upload-btn" type="submit"
                                        class="w-full h-12 rounded-2xl font-semibold bg-[var(--primary)] text-[var(--background)]">
                                        Upload resume
                                    </button>
                                </form>
                            </x-ui.card>
                        </div>
                    </div>

                    <!-- List Section -->
                    <div class="lg:col-span-8">
                        <div id="resume-list" class="space-y-4">
                            <div class="h-32 animate-pulse bg-[var(--surface-2)] rounded-3xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            async function renderResumes() {
                const list = document.getElementById('resume-list');
                if (!list) return;

                document.getElementById('resumes-header-container').innerHTML =
                    Roaster.pageHeader("Your Resumes", "Upload a new PDF to get started.");

                try {
                    const d = await Roaster.api("/resumes");
                    const arr = d.resumes || d || [];

                    if (!arr.length) {
                        list.innerHTML = Roaster.empty(
                            "file-text",
                            "No resumes yet",
                            "Upload your first PDF to begin AI analysis."
                        );
                    } else {
                        list.innerHTML = arr.map(renderResumeItem).join("");
                        bindDeleteEvents();
                    }
                } catch (e) {
                    list.innerHTML = Roaster.empty("file-text", "Couldn't load resumes", e.message);
                }
                Roaster.drawIcons();
            }

            // Upload Form Handler
            function bindUploadForm() {
                const form = document.getElementById("upload-form");
                if (!form) return;

                const fileInput = form.querySelector("#resume-file");
                const fileName = form.querySelector("#file-name");

                fileInput.onchange = function (e) {
                    fileName.textContent = e.target.files[0]?.name || "";
                };

                form.onsubmit = async function (e) {
                    e.preventDefault();
                    const file = fileInput.files[0];

                    if (!file) {
                        Roaster.toast("Choose a PDF first", "", "error");
                        return;
                    }

                    const fd = new FormData();
                    fd.append("file", file);
                    
                    const title = document.getElementById("resume-title").value.trim();
                    if (title) 
                        fd.append("title", title);

                    // Attach CSRF token
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    if (tokenMeta)
                        fd.append("_token", tokenMeta.content);

                    const b = document.getElementById("upload-btn");
                    b.disabled = true;
                    b.textContent = "Uploading...";

                    try {
                        await Roaster.api("/resumes", { method: "POST", body: fd });
                        Roaster.toast("Resume uploaded", "Parsed and ready", "success");

                        // Reset form & reload list
                        form.reset();
                        fileName.textContent = "";
                        renderResumes();
                    } catch (err) {
                        Roaster.toast("Upload failed", err.message, "error");
                    } finally {
                        b.disabled = false;
                        b.textContent = "Upload resume";
                    }
                };
            }

            // Render Individual Row Helper
            function renderResumeItem(r) {
                const versionCount = r.latest_version_number || 1;
                return Roaster.card(`
                    <div class="flex items-center gap-4">
                        <a href="/resumes/${encodeURIComponent(r.id)}" class="flex items-center gap-4 flex-1 group">
                            <div class="h-14 w-14 rounded-2xl bg-[var(--accent-soft)] text-[var(--accent-strong)] flex items-center justify-center shrink-0">
                                ${Roaster.icon("file-text", "w-6 h-6")}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-display text-base font-semibold text-[var(--foreground)] group-hover:text-[var(--primary)]">${Roaster.esc(r.title || "Resume")}</div>
                                <div class="text-xs text-[var(--ink-muted)] mt-1">Updated ${Roaster.relativeTime(r.updated_at)}</div>
                            </div>
                            ${Roaster.badge(versionCount + " version" + (versionCount > 1 ? "s" : ""), "neutral")}
                        </a>
                        <button data-delete-resume="${Roaster.esc(r.id)}" class="h-10 w-10 rounded-full hover:bg-red-50 text-[var(--ink-muted)] hover:text-[var(--danger)] flex items-center justify-center transition-colors">
                            ${Roaster.icon("trash-2", "w-4 h-4")}
                        </button>
                    </div>
                `);
            }

            // Delete Event
            function bindDeleteEvents() {
                Roaster.$$("[data-delete-resume]").forEach(function (b) {
                    b.onclick = async function () {
                        if (!confirm("Delete this resume and all its versions?")) return;

                        try {
                            await Roaster.api("/resumes/" + b.dataset.deleteResume, {
                                method: "DELETE",
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            Roaster.toast("Resume deleted", "", "success");
                            renderResumes(); // Refresh list
                        } catch (e) {
                            Roaster.toast("Couldn't delete resume", e.message, "error");
                        }
                    };
                });
            }

            // Execute on page load
            renderResumes();
            bindUploadForm();
        });
    </script>
@endpush