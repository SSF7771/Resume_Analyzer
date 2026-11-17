@extends('layouts.app')
@section('title', 'Get Started - Roaster')
@section('page', 'register')

@section('body')
<div class="min-h-screen flex lg:grid lg:grid-cols-2">
    @include('components.auth.AuthVisual', ['headline' => 'Your resume,', 'accent' => 'intelligently sharpened.', 'subhead' => 'Drop your PDF, get an ATS score, fix weak bullets, and land interviews — powered by AI.'])

        <!-- Right Side -->
    <div class="relative flex flex-col justify-center items-center p-6 sm:p-8 w-full page-enter">
        <div class="w-full max-w-sm">
            <div class="mb-8 scale-90 origin-left">@include('components.layout.AILogo')</div>
            
            <h1 class="font-display text-3xl font-bold tracking-tight">Create your account</h1>
            <p class="text-[var(--muted-foreground)] mt-2 text-sm">Free to start. No credit card required.</p>

            <form id="register-form" class="mt-8 space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-[var(--muted-foreground)]">Full name</label>
                    <input name="name" required autocomplete="name" placeholder="Ada Lovelace"
                        class="w-full h-12 px-5 rounded-2xl border border-[var(--border)] bg-[var(--card)] outline-none shadow-card">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-[var(--muted-foreground)]">Email</label>
                    <input name="email" type="email" required autocomplete="email" placeholder="you@example.com"
                        class="w-full h-12 px-5 rounded-2xl border border-[var(--border)] bg-[var(--card)] outline-none shadow-card">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-[var(--muted-foreground)]">Password</label>
                    <input name="password" type="password" data-strength required minlength="8" autocomplete="new-password"
                        placeholder="Min. 8 characters"
                        class="w-full h-12 px-5 rounded-2xl border border-[var(--border)] bg-[var(--card)] outline-none shadow-card">
                </div>
                
                <div id="register-error" class="hidden text-xs font-medium text-[var(--danger)] bg-[var(--danger)]/10 rounded-xl px-4 py-3 border border-[var(--danger)]/25"></div>
                
                <button id="register-submit" type="submit"
                    class="roaster-primary-button w-full h-12 rounded-2xl font-semibold flex items-center justify-center gap-2">
                    Create account 
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="text-sm text-center mt-8 text-[var(--muted-foreground)]">
                Already have an account? 
                <a href="/login" class="text-[var(--primary)] font-semibold hover:underline">Sign in</a>
            </div>

            <p class="text-[10px] text-center mt-8 text-[var(--muted-foreground)] leading-relaxed">
                By creating an account, you agree to our Terms. <br>
                We never share your resume data with third parties.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = Roaster.$('#register-form');
            const submitBtn = Roaster.$('#register-submit');
            const errorDiv = Roaster.$('#register-error');

            if (!form) return;

            // Save original button content so we can restore it on failure
            const originalBtnContent = submitBtn ? submitBtn.innerHTML : 'Register';

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
                            <span>Registering...</span>
                        </span>
                    `;
                    Roaster.drawIcons();
                }

                // Collect form data
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await Roaster.api("/auth/register", {
                        method: "POST",
                        body: data,
                    });

                    console.log(response);

                    // Safely store token if returned (using optional chaining)
                    if (response?.token) 
                        Roaster.saveToken(response.token);

                    if (Roaster.toast) 
                        Roaster.toast("Success", "Welcome!", "success");

                    window.location.href = '/dashboard';

                } catch (err) {
                    // Restore submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnContent;
                        Roaster.drawIcons(); // Re-render arrow/lock icon inside original button
                    }

                    // Extract the best error message (handles Laravel validation error bags)
                    let errorMsg = err.message || "Credentials didn't meet the regulations";
                    if (err.data?.errors) 
                        errorMsg = Object.values(err.data.errors).flat()[0] || errorMsg;

                    // Display error in page
                    if (errorDiv) {
                        errorDiv.textContent = errorMsg;
                        errorDiv.classList.remove('hidden');
                        form.classList.remove('shake'); void form.offsetWidth; form.classList.add('shake');
                    }
                }
            });
        });
    </script>
@endpush