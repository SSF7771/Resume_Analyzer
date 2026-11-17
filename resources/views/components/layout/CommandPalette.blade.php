<div id="command-palette" class="hidden fixed inset-0 z-[80] bg-black/40 backdrop-blur-sm no-print">
    <div
        class="absolute top-20 left-1/2 -translate-x-1/2 w-[min(640px,calc(100vw-32px))] rounded-3xl border border-[var(--border)] bg-[var(--card)] shadow-hover overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-[var(--border)]">
            <i data-lucide="search" class="w-4 h-4 text-[var(--primary)]"></i>
            <input id="palette-input" class="flex-1 bg-transparent !border-0 !shadow-none outline-none text-sm"
                placeholder="Search your resumes…" autocomplete="off">
            <kbd class="text-[10px] px-2 py-1 rounded-md bg-[var(--secondary)] text-[var(--muted-foreground)] font-semibold">ESC</kbd>
        </div>
        <div id="palette-results" class="max-h-[320px] overflow-y-auto p-2">
            <div class="px-3 pt-2 pb-1 text-[10px] uppercase tracking-wider font-bold text-[var(--muted-foreground)]">Jump to</div>
            @foreach ([['/dashboard', 'layout-dashboard', 'Dashboard'], ['/resumes', 'file-text', 'Resumes'], ['/insights', 'bar-chart-3', 'Insights'], ['/versions', 'layers', 'Versions'], ['/history', 'history', 'History'], ['/settings', 'settings', 'Settings']] as $p)
                <a href="{{ $p[0] }}" class="flex items-center gap-3 p-3 rounded-xl transition-colors">
                    <div class="icon-tile h-8 w-8 rounded-lg"><i data-lucide="{{ $p[1] }}" class="w-4 h-4"></i></div>
                    <span class="text-sm font-medium">{{ $p[2] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
