@props(['present' => [], 'missing' => []])
<x-ui.card>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div>
      <div class="text-xs font-semibold">Present keywords</div>
      <div class="flex flex-wrap gap-2 mt-3">@foreach((array) $present as $item)<x-ui.badge
      tone="accent">{{ is_string($item) ? $item : ($item['keyword'] ?? '') }}</x-ui.badge>@endforeach</div>
    </div>
    <div>
      <div class="text-xs font-semibold">Missing keywords</div>
      <div class="flex flex-wrap gap-2 mt-3">@foreach((array) $missing as $item)<x-ui.badge
      tone="danger">{{ is_string($item) ? $item : ($item['keyword'] ?? '') }}</x-ui.badge>@endforeach</div>
    </div>
  </div>
</x-ui.card>