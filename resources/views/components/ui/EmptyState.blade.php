@props(['icon'=>null,'title'=>'','description'=>null])
<x-ui.card {{ $attributes->merge(['class'=>'flex flex-col items-center text-center py-12']) }}>
  @if($icon)<div class="h-14 w-14 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center mb-3"><i data-lucide="{{ $icon }}" class="w-6 h-6"></i></div>@endif
  <div class="font-display text-lg font-semibold tracking-tight">{{ $title }}</div>
  @if($description)<p class="text-sm text-[var(--muted-foreground)] mt-1 max-w-sm">{{ $description }}</p>@endif
  @if(isset($action))<div class="mt-5">{{ $action }}</div>@endif
</x-ui.card>