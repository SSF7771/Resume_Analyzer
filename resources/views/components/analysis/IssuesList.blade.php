@props(['issues' => []])
@php($tones = ['low' => 'neutral', 'medium' => 'warning', 'high' => 'danger'])
<x-ui.card>
    <div class="flex items-center justify-between">
        <div>
            <div class="font-display text-base font-semibold">Issues</div>
            <div class="text-sm text-[var(--muted-foreground)] mt-1">Actionable issues identified by the analysis.</div>
        </div><x-ui.badge tone="accent">{{ count($issues) }} issues</x-ui.badge>
    </div>
    <div class="space-y-3 mt-5">@forelse((array) $issues as $i => $issue)
        <details class="rounded-2xl border border-[var(--border)] bg-[var(--secondary)] p-4">
            <summary class="cursor-pointer flex items-center gap-3 list-none">
                <div class="h-8 w-8 rounded-xl bg-[var(--card)] flex items-center justify-center"><i
                        data-lucide="alert-circle" class="w-4 h-4"></i></div>
                <div class="flex-1">
                    <div class="text-sm font-medium">{{ $issue['title'] ?? $issue['issue'] ?? ('Issue ' . ($i + 1)) }}</div>
                    <div class="text-xs text-[var(--muted-foreground)] mt-1">
                        {{ $issue['description'] ?? $issue['details'] ?? '' }}</div>
                </div>@if(!empty($issue['severity']))<x-ui.badge
                tone="{{ $tones[$issue['severity']] ?? 'neutral' }}">{{ $issue['severity'] }}</x-ui.badge>@endif<i
                    data-lucide="chevron-down" class="w-4 h-4 text-[var(--muted-foreground)]"></i>
            </summary>@if(!empty($issue['recommendation']))
            <div class="pl-11 pt-3 text-xs text-[var(--muted-foreground)]">{{ $issue['recommendation'] }}</div>@endif
    </details>@empty<div class="text-sm text-[var(--muted-foreground)]">No issues recorded.</div>@endforelse
    </div>
</x-ui.card>