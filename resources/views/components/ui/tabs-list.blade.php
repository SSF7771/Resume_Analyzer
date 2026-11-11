@props(['class'=>''])
<div {{ $attributes->merge(['class'=>'inline-flex items-center gap-1 bg-[var(--secondary)] border border-[var(--border)] p-1 rounded-full '.$class]) }}>{{ $slot }}</div>