@extends('layouts.app')
@section('title', 'Sign in - Roaster')
@section('page', 'login')

@section('body')
    <div class="min-h-screen flex lg:grid lg:grid-cols-2">
        <!-- Left Side -->
        <div class="hidden lg:flex flex-col justify-end p-12 xl:p-20 bg-[var(--foreground)] text-[var(--background)]">
            <div class="max-w-xl">
                <div
                    class="inline-block px-3 py-1 rounded-full bg-[var(--background)]/10 text-[11px] uppercase tracking-[0.2em] font-bold mb-6">
                    Roaster AI
                </div>
                <h2 class="font-display text-5xl xl:text-6xl font-bold tracking-tight leading-[1.1]">
                    Sharpen your resume,<br>
                    <em class="font-serif font-light text-[var(--accent)]">with intelligence.</em>
                </h2>
                <p class="text-[var(--background)]/70 mt-8 text-lg leading-relaxed max-w-lg">
                    Score against ATS, fix weak bullets, and ship a stronger version of yourself in minutes.
                </p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex flex-col justify-center items-center p-8 w-full">
            <div class="w-full max-w-sm">
                <div class="mb-8 scale-90 origin-left">@include('components.layout.AILogo')</div>

                <h1 class="font-display text-3xl font-bold tracking-tight">Welcome back</h1>
                <p class="text-[var(--muted-foreground)] mt-2 text-sm">Sign in to keep sharpening your resume.</p>

                <form id="login-form" class="mt-8 space-y-5">
                    @csrf
                    <div class="space-y-1.5">
                        <label
                            class="text-xs font-semibold uppercase tracking-wider text-[var(--muted-foreground)]">Email</label>
                        <input name="email" type="email" required autocomplete="email" placeholder="you@example.com"
                            class="w-full h-12 px-5 rounded-2xl border border-[var(--border)] bg-[var(--card)] focus:border-[var(--primary)] transition-colors outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between">
                            <label
                                class="text-xs font-semibold uppercase tracking-wider text-[var(--muted-foreground)]">Password</label>
                            <button type="button"
                                class="text-xs text-[var(--primary)] font-semibold hover:underline">Forgot?</button>
                        </div>
                        <input name="password" type="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full h-12 px-5 rounded-2xl border border-[var(--border)] bg-[var(--card)] focus:border-[var(--primary)] transition-colors outline-none">
                    </div>

                    <div id="login-error"
                        class="hidden text-xs text-[var(--destructive)] bg-red-50 rounded-xl px-4 py-3 border border-red-100">
                    </div>

                    <button id="login-submit" type="submit"
                        class="w-full h-12 rounded-2xl bg-[var(--primary)] text-[var(--primary-foreground)] font-semibold flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                        Sign in <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <div class="text-sm text-center mt-8 text-[var(--muted-foreground)]">
                    Don't have an account?
                    <a href="/register" class="text-[var(--primary)] font-semibold hover:underline">Create one</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = Roaster.$('#login-form');
            const submitBtn = Roaster.$('#login-submit');
            const errorDiv = Roaster.$('#login-error');

            if (!form) return;

            // Save original button content so we can restore it on failure
            const originalBtnContent = submitBtn ? submitBtn.innerHTML : 'Sign in';

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Reset error state
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('hidden');
                }

                // Set loading state on button (with Lucide spinner icon)
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="inline-flex items-center justify-center gap-2">
                            ${Roaster.icon('loader-2', 'w-4 h-4 animate-spin')}
                            <span>Signing in...</span>
                        </span>
                    `;
                    Roaster.drawIcons();
                }

                // 3. Collect form data
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await Roaster.api("/auth/login", {
                        method: "POST",
                        body: data,
                    });

                    console.log(response);

                    // Safely store token if returned (using optional chaining)
                    if (response?.token) 
                        Roaster.saveToken(response.token);

                    if (Roaster.toast) 
                        Roaster.toast("Success", "Welcome back!", "success");

                    window.location.href = '/dashboard';

                } catch (err) {
                    // Restore submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                        Roaster.drawIcons(); // Re-render arrow/lock icon inside original button
                    }

                    // Extract the best error message (handles Laravel validation error bags)
                    let errorMsg = err.message || "Invalid credentials.";
                    if (err.data?.errors) 
                        errorMsg = Object.values(err.data.errors).flat()[0] || errorMsg;

                    // Display error in page
                    if (errorDiv) {
                        errorDiv.textContent = errorMsg;
                        errorDiv.classList.remove('hidden');
                    }

                    // Trigger error toast
                    if (Roaster.toast)
                        Roaster.toast("Authentication Error", errorMsg, "error");
                    
                }
            });
        });
    </script>
@endpush