@props(['rewrites' => [], 'error' => null])
<x-ui.card id="blade-bullet-rewrites">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="font-display text-base font-semibold">Suggested Rewrites</div>
            <div class="text-sm text-[var(--muted-foreground)] mt-1">Pick the ones you want - applying creates a new
                version.</div>
        </div><x-ui.badge tone="accent">{{ count($rewrites) }} suggestions</x-ui.badge>
    </div>@if(count($rewrites))
    <div
        class="flex items-center justify-between mt-5 p-4 rounded-2xl border border-[var(--border)] bg-[var(--secondary)]/40">
        <div>
            <div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--muted-foreground)]">AI rewrites
            </div>
            <div class="font-display text-3xl font-semibold">{{ count($rewrites) }}</div>
        </div>
        <div class="flex gap-2"><button type="button" data-rewrites-select-all
                class="h-9 px-3 rounded-full border border-[var(--border)] text-xs font-semibold">Select
                all</button><button type="button" data-rewrites-apply-selected
                class="h-9 px-3 rounded-full border border-[var(--border)] text-xs font-semibold">Apply
                selected</button><button type="button" data-rewrites-apply-all
                class="h-9 px-3 rounded-full text-primary-foreground text-xs font-semibold">Apply all → new
                version</button></div>
    </div>
    <div class="space-y-3 mt-4">@foreach($rewrites as $idx => $r)@php($rid = $r['id'] ?? 'idx-' . $idx)
        <div class="rounded-2xl border border-[var(--border)] p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3"><span
                        class="font-display text-xl font-semibold">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</span>@if(!empty($r['section']))<x-ui.badge
                        tone="accent">{{ $r['section'] }}</x-ui.badge>@endif</div><label
                    class="text-xs text-[var(--muted-foreground)] flex gap-2 items-center"><input type="checkbox"
                        class="blade-rewrite-check" value="{{ $rid }}" checked> Will apply</label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-[1fr_36px_1fr] gap-3">
                <div class="rounded-xl bg-[var(--secondary)] p-4">
                    <div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--muted-foreground)]">
                        Original</div>
                    <div class="text-[13.5px] text-[var(--muted-foreground)] mt-1.5 line-through">
                        {{ $r['original'] ?? $r['before'] ?? '' }}</div>
                </div>
                <div class="flex items-center justify-center">
                    <div class="h-9 w-9 rounded-full flex items-center justify-center text-primary-foreground"><i
                            data-lucide="arrow-right" class="w-4 h-4"></i></div>
                </div>
                <div class="rounded-xl p-4 border"
                    style="background:linear-gradient(135deg,var(--secondary),var(--card))">
                    <div class="text-[10px] uppercase tracking-wider font-semibold text-[var(--primary)]">Rewritten
                    </div>
                    <div class="text-[13.5px] font-medium mt-1.5">
                        {{ $r['rewritten'] ?? $r['rewrite'] ?? $r['after'] ?? $r['text'] ?? '' }}</div>
                </div>
            </div>@if(!empty($r['rationale']))
                <div class="mt-4 rounded-xl bg-[var(--secondary)] p-3 text-xs text-[var(--muted-foreground)]"><span
                        class="font-semibold text-[var(--foreground)]">Why this works · </span>{{ $r['rationale'] }}</div>
            @endif
        </div>@endforeach
    </div>@else<div class="mt-5 text-sm text-[var(--muted-foreground)]">No rewrites suggested.</div>@endif@if($error)
    <div class="mt-4 text-xs text-[var(--destructive)] bg-destructive/10 rounded-xl px-3 py-2">{{ $error }}</div>@endif
</x-ui.card>