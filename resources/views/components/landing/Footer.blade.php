<footer class="bg-background border-t border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div
            class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[var(--muted-foreground)]">
            <div class="flex items-center gap-2">@include('components.layout.AILogo')</div>
            <div>© {{ date('Y') }} Roaster. Resume intelligence, calmly delivered.</div>
            <div class="flex gap-4"><a href="/login" class="hover:text-[var(--foreground)]">Sign in</a><a
                    href="/register" class="hover:text-[var(--foreground)]">Create account</a></div>
        </div>
    </div>
</footer>