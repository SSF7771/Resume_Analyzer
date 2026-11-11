@props(['checked'=>false,'label'=>null])
<button type="button" role="checkbox" aria-checked="{{ $checked ? 'true':'false' }}" data-checkbox class="inline-flex items-center gap-2 select-none focus-visible:outline-none" {{ $attributes }}>
  <span class="h-4 w-4 rounded-md border flex items-center justify-center transition-colors {{ $checked ? 'bg-[var(--primary)] border-transparent text-white':'bg-[var(--card)] border-[var(--border)] text-transparent' }}"><i data-lucide="check" class="w-3 h-3" stroke-width="3"></i></span>
  @if($label)<span class="text-xs text-[var(--muted-foreground)]">{{ $label }}</span>@endif
</button>