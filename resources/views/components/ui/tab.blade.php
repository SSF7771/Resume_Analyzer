@props(['value'=>null])
<div {{ $attributes->merge(['class'=>'space-y-0']) }} data-tabs data-tabs-value="{{ $value }}">{{ $slot }}</div>