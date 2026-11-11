<div id="command-palette" class="hidden fixed inset-0 z-[80] bg-black/30 backdrop-blur-sm no-print">
    <div
        class="absolute top-20 left-1/2 -translate-x-1/2 w-[min(680px,calc(100vw-32px))] rounded-3xl border border-[var(--border)] bg-[var(--card)] shadow-hover overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-[var(--border)]"><i data-lucide="search"
                class="w-4 h-4 text-[var(--muted-foreground)]"></i><input id="palette-input"
                class="flex-1 bg-transparent outline-none text-sm" placeholder="Search pages…"><kbd
                class="text-[10px] text-[var(--muted-foreground)]">ESC</kbd></div>
        <div id="palette-results" class="max-h-[300px] overflow-y-auto p-2"></div>
    </div>
</div>