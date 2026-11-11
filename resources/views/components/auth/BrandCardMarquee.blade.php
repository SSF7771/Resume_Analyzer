<div class="grid grid-cols-3 gap-3 mt-8">@foreach(['OpenAI', 'Stripe', 'Linear', 'Notion', 'Vercel', 'Figma'] as $brand)
    <div
        class="rounded-2xl border border-[var(--border)] bg-[var(--card)] py-3 text-center text-xs font-semibold text-[var(--muted-foreground)]">
{{ $brand }}</div>@endforeach
</div>