@php
  $navItems = [
    ['/dashboard', 'layout-dashboard', 'Dashboard'],
    ['/resumes', 'file-text', 'Resumes'],
    ['/insights', 'bar-chart-3', 'Insights'],
    ['/versions', 'layers', 'Versions'],
    ['/history', 'history', 'History'],
  ];
  $isActive = fn ($path) => request()->is(trim($path, '/')) || request()->is(trim($path, '/') . '/*');
@endphp

<aside
  class="group/sidebar hidden md:flex shrink-0 h-[calc(100vh-32px)] sticky top-4 ml-4 flex-col items-center justify-between rounded-3xl bg-[var(--card)] border border-[var(--border)] shadow-xl shadow-black/5 overflow-hidden w-[88px] hover:w-[248px] transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] no-print">

  <div class="flex flex-col items-center gap-8 w-full">
    <a href="/dashboard" class="flex items-center h-14 mt-4 w-full px-5 overflow-hidden group" aria-label="Roaster dashboard">
      <div class="brand-logo h-11 w-11 rounded-2xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:rotate-[-8deg]">
        <i data-lucide="sparkles" class="w-5 h-5"></i>
      </div>
      <div class="ml-3 min-w-0 flex-1 opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
        <div class="font-display text-lg font-extrabold tracking-tight text-[var(--foreground)] truncate">Roaster</div>
        <div class="text-[10px] text-[var(--muted-foreground)] -mt-0.5">Resume intelligence</div>
      </div>
    </a>

    <!-- Navigation -->
    <nav class="flex flex-col items-center gap-1.5 w-full px-3">
      @foreach ($navItems as $item)
        <a href="{{ $item[0] }}" title="{{ $item[2] }}"
          class="nav-row flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 active:scale-[0.97] {{ $isActive($item[0]) ? 'nav-active' : 'text-[var(--muted-foreground)] hover:bg-[var(--secondary)] hover:text-[var(--foreground)]' }}">
          <span class="flex items-center justify-center shrink-0 w-8">
            <i data-lucide="{{ $item[1] }}" class="w-[20px] h-[20px]"></i>
          </span>
          <span class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
            {{ $item[2] }}
          </span>
        </a>
      @endforeach
    </nav>
  </div>

  <!-- Footer Section -->
  <div class="flex flex-col items-center gap-1.5 w-full px-3 pb-3">
    <a href="/settings" title="Settings"
      class="nav-row flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 {{ $isActive('/settings') ? 'nav-active' : 'text-[var(--muted-foreground)] hover:bg-[var(--secondary)] hover:text-[var(--foreground)]' }}">
      <span class="flex items-center justify-center shrink-0 w-8"><i data-lucide="settings" class="w-[20px] h-[20px]"></i></span>
      <span class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">Settings</span>
    </a>

    <button data-action="logout" title="Log out"
      class="nav-row flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 text-[var(--muted-foreground)] hover:bg-[var(--danger)]/10 hover:text-[var(--danger)]">
      <span class="flex items-center justify-center shrink-0 w-8"><i data-lucide="log-out" class="w-[20px] h-[20px]"></i></span>
      <span class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">Log out</span>
    </button>

    <!-- User Profile -->
    <div class="flex items-center h-14 mt-2 w-full px-1.5 rounded-2xl bg-[var(--surface-2)] overflow-hidden">
      <div id="sidebar-avatar"
        class="h-10 w-10 rounded-full bg-brand text-white font-bold flex items-center justify-center text-sm ring-2 ring-[var(--card)] shrink-0">
        R
      </div>
      <div class="ml-3 min-w-0 flex-1 opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
        <div id="sidebar-name" class="text-sm font-semibold text-[var(--foreground)] truncate">Account</div>
        <div id="sidebar-email" class="text-[11px] text-[var(--muted-foreground)] truncate"></div>
      </div>
    </div>
  </div>
</aside>

<!-- Mobile bottom navigation -->
<nav class="mobile-nav md:hidden fixed bottom-3 inset-x-3 z-50 glass rounded-3xl shadow-hover px-1.5 py-1.5 flex items-center gap-0.5 no-print">
  @foreach (array_merge($navItems, [['/settings', 'settings', 'Settings']]) as $item)
    <a href="{{ $item[0] }}"
      class="flex-1 min-w-0 flex flex-col items-center gap-0.5 px-1 py-1.5 rounded-2xl text-[9.5px] font-semibold transition-all {{ $isActive($item[0]) ? 'nav-active' : 'text-[var(--muted-foreground)]' }}">
      <i data-lucide="{{ $item[1] }}" class="w-5 h-5"></i>
      <span class="truncate max-w-full">{{ $item[2] }}</span>
    </a>
  @endforeach
</nav>
