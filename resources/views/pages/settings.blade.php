@extends('layouts.app')
@section('title', 'Settings - Roaster')
@section('page', 'settings')

@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 min-w-0 px-4 sm:px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>

            <div id="settings-page" class="page-enter">
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

            async function renderSettings() {
                var root = $("#settings-page");
                if (!root) return;
                try {
                    var me = await api("/me");
                    var u = me.user || me;
                    var tab = "profile";

                    function render() {
                        var th = document.documentElement.classList.contains("dark") ? "dark" : "light";
                        var content = "";

                        if (tab === "profile") {
                            content = card(
                                '<div class="font-display text-lg font-bold">Profile</div><div class="text-sm text-[var(--ink-muted)] mt-1">Your display name appears on the dashboard greeting and on your resumes.</div><form id="profile-form" class="space-y-4 mt-6"><div class="flex items-center gap-4"><div class="h-16 w-16 rounded-2xl brand-logo font-display font-extrabold flex items-center justify-center text-xl">' +
                                esc((u.name || "?")[0].toUpperCase()) +
                                '</div><div class="text-xs text-[var(--ink-muted)]">Avatar is generated from your initial.</div></div><div><label class="text-xs font-medium mb-1.5 block">Full name</label><input id="profile-name" value="' +
                                esc(u.name || "") +
                                '" maxlength="80" class="w-full h-11 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] outline-none"></div><div><label class="text-xs font-medium mb-1.5 block">Email</label><input value="' +
                                esc(u.email || "") +
                                '" disabled class="w-full h-11 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface-2)] outline-none"></div><div class="flex justify-end"><button class="roaster-primary-button h-11 px-5 rounded-2xl font-semibold cursor-pointer">Save changes</button></div></form>',
                                "max-w-2xl",
                            );
                        } else if (tab === "appearance") {
                            content = card(
                                '<div class="font-display text-lg font-bold">Appearance</div><div class="text-sm text-[var(--ink-muted)] mt-1">Pick a theme. Your choice is remembered on this device.</div><div class="grid grid-cols-2 gap-3 mt-6"><button data-theme-choice="light" class="group text-left p-4 rounded-2xl border-2 cursor-pointer transition-all hover:-translate-y-0.5 ' +
                                (th === "light"
                                    ? "border-[var(--primary)] bg-[var(--accent-soft)]/50 shadow-card"
                                    : "border-[var(--border)] hover:border-[var(--primary)]/40") +
                                '"><div class="h-20 rounded-xl bg-[#f6f7f9] border border-[#e3e6eb] p-2.5 flex gap-2"><div class="w-5 rounded-md bg-white border border-[#e3e6eb]"></div><div class="flex-1 space-y-1.5"><div class="h-2 w-2/3 rounded bg-[#2b313b]"></div><div class="h-2 w-1/2 rounded bg-[#c9ced6]"></div><div class="h-6 rounded-md bg-white border border-[#e3e6eb]"></div></div></div><div class="flex items-center gap-2 mt-3">' +
                                icon("sun", "w-4 h-4") +
                                '<div class="text-sm font-bold">Light</div></div><div class="text-[11px] text-[var(--ink-muted)] mt-0.5">Bright, clean, neutral tones</div></button><button data-theme-choice="dark" class="group text-left p-4 rounded-2xl border-2 cursor-pointer transition-all hover:-translate-y-0.5 ' +
                                (th === "dark"
                                    ? "border-[var(--primary)] bg-[var(--accent-soft)]/50 shadow-card"
                                    : "border-[var(--border)] hover:border-[var(--primary)]/40") +
                                '"><div class="h-20 rounded-xl bg-[#16191f] border border-[#2a2f38] p-2.5 flex gap-2"><div class="w-5 rounded-md bg-[#1d2128] border border-[#2a2f38]"></div><div class="flex-1 space-y-1.5"><div class="h-2 w-2/3 rounded bg-[#e8ebf0]"></div><div class="h-2 w-1/2 rounded bg-[#4a515c]"></div><div class="h-6 rounded-md bg-[#1d2128] border border-[#2a2f38]"></div></div></div><div class="flex items-center gap-2 mt-3">' +
                                icon("moon", "w-4 h-4") +
                                '<div class="text-sm font-bold">Dark</div></div><div class="text-[11px] text-[var(--ink-muted)] mt-0.5">Calm, low-glare night</div></button></div>',
                                "max-w-2xl",
                            );
                        } else {
                            content = card(
                                '<div class="font-display text-lg font-bold">Password</div><div class="text-sm text-[var(--ink-muted)] mt-1">Use at least 8 characters. Mix letters, numbers, and a symbol for a stronger password.</div><form id="password-form" class="space-y-4 mt-6"><input id="current-password" type="password" placeholder="Current password" class="w-full h-11 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] outline-none"><input id="new-password" type="password" data-strength minlength="8" placeholder="New password" class="w-full h-11 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] outline-none"><input id="confirm-password" type="password" minlength="8" placeholder="Confirm new password" class="w-full h-11 px-4 rounded-2xl border border-[var(--border)] bg-[var(--surface)] outline-none"><div class="flex justify-end"><button class="roaster-primary-button h-11 px-5 rounded-2xl font-semibold cursor-pointer">Update Password</button></div></form>',
                                "max-w-2xl",
                            );
                        }

                        root.innerHTML =
                            pageHeader(
                                "Settings",
                                "Manage your account, look & feel, and password.",
                            ) +
                            '<div class="inline-flex flex-wrap items-center gap-1 bg-[var(--card)] border border-[var(--border)] p-1.5 rounded-full shadow-card">' +
                            [
                                ["profile", "Profile", "user"],
                                ["appearance", "Appearance", "palette"],
                                ["password", "Password", "lock"],
                            ]
                                .map(function (x) {
                                    const isActive = tab === x[0];
                                    const baseClass =
                                        "h-9 px-4 rounded-full text-xs font-bold transition-all duration-300 inline-flex items-center gap-1.5 ";
                                    const activeClass = isActive
                                        ? "bg-[var(--primary)] text-[var(--primary-foreground)] shadow-card"
                                        : "text-[var(--muted-foreground)] hover:text-[var(--foreground)] hover:bg-[var(--secondary)]";

                                    return (
                                        '<button data-tab="' +
                                        x[0] +
                                        '" class="' +
                                        baseClass +
                                        activeClass +
                                        '">' +
                                        icon(x[2], "w-3.5 h-3.5") +
                                        x[1] +
                                        "</button>"
                                    );
                                })
                                .join("") +
                            '</div><div class="mt-6 page-enter">' +
                            content +
                            "</div>";

                        drawIcons();

                        $$("[data-tab]").forEach(function (b) {
                            b.onclick = function () {
                                tab = b.dataset.tab;
                                render();
                            };
                        });

                        if (tab === "profile") {
                            $("#profile-form").onsubmit = async function (e) {
                                e.preventDefault();
                                try {
                                    var z = await api("/profile", {
                                        method: "PATCH",
                                        body: { name: $("#profile-name").value.trim() },
                                    });
                                    u = z.user || u;
                                    if (typeof hydrateUser === "function") hydrateUser(u);
                                    toast("Profile updated", "", "success");
                                    render();
                                } catch (err) {
                                    toast("Couldn't update profile", err.message, "error");
                                }
                            };
                        }

                        if (tab === "password") {
                            $("#password-form").onsubmit = async function (e) {
                                e.preventDefault();
                                var a = $("#current-password").value,
                                    b = $("#new-password").value,
                                    c = $("#confirm-password").value;
                                if (b.length < 8 || b !== c) {
                                    toast(
                                        "Check your new password",
                                        "Use at least 8 characters and make both fields match.",
                                        "error",
                                    );
                                    return;
                                }
                                try {
                                    await api("/password", {
                                        method: "PATCH",
                                        body: { currentPassword: a, newPassword: b },
                                    });
                                    toast("Password changed", "", "success");
                                    $("#password-form").reset();
                                } catch (err) {
                                    toast("Couldn't change password", err.message, "error");
                                }
                            };
                        }

                        if (tab === "appearance") {
                            $$("[data-theme-choice]").forEach(function (b) {
                                b.onclick = async function () {
                                    const choice = b.dataset.themeChoice;
                                    if (Roaster.setTheme) await Roaster.setTheme(choice, b);
                                    else {
                                        localStorage.setItem("arr-theme", choice);
                                        document.documentElement.classList.toggle("dark", choice === "dark");
                                    }
                                    render();
                                };
                            });
                        }
                    }
                    render();
                } catch (e) {
                    root.innerHTML = empty("settings", "Couldn't load settings", e.message);
                    drawIcons();
                }
            }

            renderSettings();
        });
    </script>
@endpush