<nav id="landing-nav" class="sticky top-0 z-40 bg-transparent backdrop-blur-xl transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5 group">
            <div
                class="brand-logo h-10 w-10 rounded-2xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:rotate-[-8deg] group-hover:scale-105">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
            </div>
            <span class="font-display text-xl font-extrabold tracking-tight">Roaster</span>
        </a>

        <div class="hidden md:flex items-center gap-1 p-1 rounded-full glass text-sm font-medium">
            <a href="#features" data-scrollspy="features"
                class="px-4 py-1.5 rounded-full text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-colors">Features</a>
            <a href="#how-it-works" data-scrollspy="how-it-works"
                class="px-4 py-1.5 rounded-full text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-colors">How
                it works</a>
            <a href="#benefits" data-scrollspy="benefits"
                class="px-4 py-1.5 rounded-full text-[var(--muted-foreground)] hover:text-[var(--foreground)] transition-colors">Benefits</a>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button type="button" id="theme-toggle"
                class="h-10 w-10 rounded-full glass flex items-center justify-center hover:scale-105 transition-transform"
                aria-label="Toggle theme"><i data-lucide="moon" class="w-4 h-4"></i>
            </button>
            <a href="/login"
                class="hidden sm:inline-flex text-sm font-semibold text-[var(--muted-foreground)] hover:text-[var(--foreground)] px-2 transition-colors">Sign
                in</a>
            <a href="/register"
                class="roaster-primary-button hidden sm:inline-flex items-center gap-1.5 h-10 px-5 rounded-full text-sm font-semibold">
                Get started <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
            <button type="button" id="mobile-menu-toggle" aria-label="Open menu" aria-expanded="false"
                class="md:hidden h-10 w-10 rounded-full glass flex items-center justify-center">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu"
        class="md:hidden grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-out">
        <div class="overflow-hidden">
            <div class="px-4 pb-4 pt-1 flex flex-col gap-1 text-sm font-medium">
                <a href="#features" class="px-4 py-3 rounded-2xl hover:bg-[var(--secondary)]">Features</a>
                <a href="#how-it-works" class="px-4 py-3 rounded-2xl hover:bg-[var(--secondary)]">How it works</a>
                <a href="#benefits" class="px-4 py-3 rounded-2xl hover:bg-[var(--secondary)]">Benefits</a>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <a href="/login"
                        class="roaster-secondary-button h-11 rounded-2xl flex items-center justify-center font-semibold">Sign
                        in</a>
                    <a href="/register"
                        class="roaster-primary-button h-11 rounded-2xl flex items-center justify-center font-semibold">Get
                        started</a>
                </div>
            </div>
        </div>
    </div>
</nav>
