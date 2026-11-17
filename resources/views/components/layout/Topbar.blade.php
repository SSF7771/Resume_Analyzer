<header class="flex items-center justify-between gap-4 mb-8 no-print">
  <div class="min-w-0 flex items-center gap-3">
    <a href="/dashboard" class="md:hidden brand-logo h-10 w-10 rounded-2xl flex items-center justify-center shrink-0" aria-label="Roaster">
      <i data-lucide="flame" class="w-5 h-5"></i>
    </a>
    <div class="min-w-0">
      <div class="text-xs text-[var(--muted-foreground)] flex items-center gap-1.5">
        <span><span id="topbar-greeting">Good to see you</span>, <span id="topbar-first-name" class="font-semibold text-[var(--foreground)]">there</span></span>
        <span class="inline-block origin-[70%_70%] animate-[wave_2.2s_ease-in-out_infinite]">👋</span>
      </div>
      <div class="font-display text-lg font-bold tracking-tight truncate">Keep <span class="text-gradient">sharpening.</span></div>
    </div>
  </div>
  <div class="flex items-center gap-2 sm:gap-3 shrink-0">
    <button type="button" id="open-search"
      class="hidden lg:flex items-center gap-3 h-11 w-[340px] rounded-full bg-[var(--card)] border border-[var(--border)] pl-4 pr-1.5 shadow-card transition-all hover:border-[var(--primary)]/50 hover:shadow-hover text-left"
      aria-label="Search">
      <i data-lucide="search" class="w-4 h-4 text-[var(--muted-foreground)] shrink-0"></i>
      <span class="flex-1 text-sm text-[var(--muted-foreground)] truncate">Search resumes…</span>
      <kbd class="inline-flex items-center gap-0.5 text-[10px] px-2 h-7 rounded-full bg-[var(--secondary)] text-[var(--secondary-foreground)] border border-[var(--border)] font-semibold">Ctrl F</kbd>
    </button>
    <button type="button" id="mobile-search"
      class="lg:hidden h-11 w-11 rounded-full bg-[var(--card)] border border-[var(--border)] flex items-center justify-center shadow-card"
      aria-label="Search">
      <i data-lucide="search" class="w-4 h-4"></i>
    </button>
    <button type="button" id="theme-toggle"
      class="h-11 w-11 rounded-full bg-[var(--card)] border border-[var(--border)] flex items-center justify-center shadow-card hover:rotate-12 transition-transform"
      aria-label="Toggle theme">
      <i data-lucide="moon" class="w-4 h-4"></i>
    </button>
    <a href="/settings"
      class="hidden sm:flex h-11 w-11 rounded-full bg-brand text-white items-center justify-center shadow-card hover:scale-105 transition-transform"
      aria-label="Profile">
      <i data-lucide="user" class="w-4 h-4"></i>
    </a>
  </div>
</header>
