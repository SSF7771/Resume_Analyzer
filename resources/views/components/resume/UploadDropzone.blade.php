@props(['compact' => false])<label
    class="block rounded-3xl border-2 border-dashed border-[var(--border)] bg-[var(--secondary)] p-7 text-center cursor-pointer hover:bg-[var(--card)] transition-colors"><input
        id="resume-file" type="file" name="file" accept="application/pdf" class="hidden">
    <div
        class="h-12 w-12 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] mx-auto flex items-center justify-center">
        <i data-lucide="upload-cloud" class="w-5 h-5"></i>
    </div>
    <div class="font-semibold mt-3">Drop your PDF here</div>
    <div class="text-xs text-[var(--muted-foreground)] mt-1">PDF only</div>
    <div id="file-name" class="text-xs text-[var(--primary)] mt-3"></div>
</label>