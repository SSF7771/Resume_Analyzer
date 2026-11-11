<header class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5 mb-8 no-print">
  <div class="min-w-0">
    <div class="text-xs text-muted-foreground">Good to see you, <span id="topbar-first-name"
        class="font-medium text-foreground">there</span>.</div>
    <div class="font-display text-lg font-semibold tracking-tight">Keep sharpening.</div>
  </div>
  <div class="flex items-center gap-2 sm:gap-3 shrink-0">
    <button type="button" id="open-search"
      class="roaster-search-trigger hidden lg:flex items-center gap-3 h-11 rounded-full bg-card border border-border pl-4 pr-1.5 shadow-sm transition-shadow hover:shadow-md text-left"
      aria-label="Search">
      <i data-lucide="search" class="w-4 h-4 text-muted-foreground shrink-0"></i>
      <span class="flex-1 text-sm text-muted-foreground truncate">Search resumes, keywords, rewrites...</span>
      <kbd
        class="inline-flex items-center gap-0.5 text-[10px] px-2 h-7 rounded-full bg-secondary text-secondary-foreground border border-border font-semibold">⌘
        K</kbd>
    </button>
    <button type="button" id="mobile-search"
      class="lg:hidden h-10 w-10 rounded-full bg-card border border-border flex items-center justify-center shadow-sm"
      aria-label="Search">
      <i data-lucide="search" class="w-4 h-4"></i>
    </button>
    <button type="button" id="theme-toggle"
      class="h-11 w-11 rounded-full bg-card border border-border flex items-center justify-center shadow-sm"
      aria-label="Toggle theme">
      <i data-lucide="moon" class="w-4 h-4"></i>
    </button>
    <a href="/settings"
      class="h-11 w-11 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center shadow-sm"
      aria-label="Profile">
      <i data-lucide="user" class="w-4 h-4"></i>
    </a>
  </div>
</header>