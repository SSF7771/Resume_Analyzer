<aside
  class="group/sidebar hidden md:flex shrink-0 h-[calc(100vh-32px)] sticky top-4 ml-4 flex-col items-center justify-between rounded-3xl bg-[var(--card)] border border-[var(--border)] shadow-xl shadow-black/5 overflow-hidden w-[88px] hover:w-[248px] transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] no-print">

  <div class="flex flex-col items-center gap-8 w-full">
    <div class="flex items-center h-14 mt-4 w-full px-5 overflow-hidden group rounded-2xl"
      aria-label="Roaster dashboard">
      <div
        class="h-10 w-10 rounded-full bg-[var(--primary)] text-[var(--primary-foreground)] font-bold flex items-center justify-center text-sm ring-2 ring-[var(--card)] shrink-0">
        <i data-lucide="sparkles" class="w-5 h-5"></i>
      </div>

      <div
        class="ml-3 min-w-0 flex-1 opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
        <div class="text-lg font-semibold text-[var(--foreground)] truncate">Roaster</div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col items-center gap-2 w-full px-2">
      @foreach([
              ['/dashboard', 'layout-dashboard', 'Dashboard'],
              ['/resumes', 'file-text', 'Resumes'],
              ['/insights', 'bar-chart-3', 'Insights'],
              ['/versions', 'layers', 'Versions'],
              ['/history', 'history', 'History'],
            ] as $item)
            <?php
        // Adjust this condition based on how the routes are defined
        $isActive = request()->is(trim($item[0], '/')) || request()->is(trim($item[0], '/') . '/*');
                  ?>
            <a href="{{ $item[0] }}"
              class="nav-row relative flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 active:scale-[0.98] {{ $isActive ? 'bg-[var(--secondary)] text-[var(--foreground)] font-semibold shadow-sm' : 'text-[var(--muted-foreground)] hover:bg-[var(--secondary)] hover:text-[var(--foreground)]' }}">

              <!-- Icon with conditional active color highlight -->
              <span class="flex items-center justify-center shrink-0 w-8 {{ $isActive ? 'text-[var(--foreground)]' : '' }}">
                <i data-lucide="{{ $item[1] }}" class="w-[20px] h-[20px]"></i>
              </span>

              <!-- Text label -->
              <span
                class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
                {{ $item[2] }}
              </span>
            </a>
      @endforeach
    </nav>
  </div>

  <!-- Footer Section -->
  <div class="flex flex-col items-center gap-2 w-full px-2">
    <a href="/settings" data-nav="/settings" title="Settings" class="w-full">
      <div
        class="nav-row relative flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 text-[var(--muted-foreground)] hover:bg-[var(--secondary)] hover:text-[var(--foreground)]">
        <span class="flex items-center justify-center shrink-0 w-8"><i data-lucide="settings"
            class="w-[20px] h-[20px]"></i></span>
        <span
          class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">Settings</span>
      </div>
    </a>

    <button data-action="logout" title="Log out" class="w-full text-left">
      <div
        class="nav-row relative flex items-center h-12 w-full rounded-2xl px-3 transition-all duration-200 text-[var(--muted-foreground)] hover:bg-red-50 hover:text-red-600">
        <span class="flex items-center justify-center shrink-0 w-8"><i data-lucide="log-out"
            class="w-[20px] h-[20px]"></i></span>
        <span
          class="ml-3 text-[14px] font-medium whitespace-nowrap opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">Log
          out</span>
      </div>
    </button>

    <!-- User Profile -->
    <div class="flex items-center h-14 mt-4 w-full px-3 overflow-hidden transition-all duration-300">
      <div id="sidebar-avatar"
        class="h-10 w-10 rounded-full bg-[var(--primary)] text-[var(--primary-foreground)] font-bold flex items-center justify-center text-sm ring-2 ring-[var(--card)] shrink-0">
        R
      </div>
      <div
        class="ml-3 min-w-0 flex-1 opacity-0 transition-opacity duration-200 group-hover/sidebar:opacity-100 group-hover/sidebar:delay-100">
        <div id="sidebar-name" class="text-sm font-semibold text-[var(--foreground)] truncate">Account</div>
        <div id="sidebar-email" class="text-[11px] text-[var(--muted-foreground)] truncate"></div>
      </div>
    </div>
  </div>
</aside>