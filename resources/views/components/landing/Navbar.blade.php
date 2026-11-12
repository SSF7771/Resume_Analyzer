<nav class="sticky top-0 z-40 bg-background/90 backdrop-blur border-b border-[var(--border)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3 group">
                        <div
                                class="h-10 w-10 rounded-full bg-[var(--primary)] text-[var(--primary-foreground)] font-bold flex items-center justify-center text-sm ring-2 ring-[var(--card)] shrink-0">
                                <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                                <div class="text-lg font-semibold text-[var(--foreground)] truncate">Roaster</div>
                        </div>
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm text-[var(--muted-foreground)]">
                        <a href="#features" class="hover:text-[var(--foreground)]">Features</a>
                        <a href="#how-it-works" class="hover:text-[var(--foreground)]">How it works</a>
                        <a href="#benefits" class="hover:text-[var(--foreground)]">Benefits</a>
                </div>
                <div class="flex items-center gap-0.5 text-md text-[var(--muted-foreground)]">
                        <button type="button" id="theme-toggle"
                                class="h-11 w-11 rounded-full bg-card border border-border flex items-center justify-center shadow-sm"
                                aria-label="Toggle theme">
                                <i data-lucide="moon" class="w-4 h-4"></i>
                                <a href="/login" class="hover:text-[var(--foreground)]">Sign in</a>
                                <a href="/register"
                                        class="inline-flex items-center h-10 px-4 rounded-full text-primary-foreground font-semibold hover:text-[var(--foreground)]">
                                        Get Started
                                </a>
                </div>
                <div class="md:hidden flex items-center gap-2">
                        <a href="/login" class="text-xs font-semibold text-[var(--muted-foreground)]">Sign in</a>
                        <a href="/register" class="h-9 px-3 rounded-full text-xs font-semibold text-white flex items-center">Start</a>
                </div>
        </div>
</nav>