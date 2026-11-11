@props(['name' => '', 'src' => null, 'size' => 40, 'class' => ''])
@php
  $initials = collect(preg_split('/\s+/', trim($name)))->filter()->take(2)->map(fn($s) => strtoupper(substr($s,0,1)))->implode('');
@endphp
<div {{ $attributes->merge(['class'=>'relative inline-flex items-center justify-center rounded-full bg-[var(--secondary)] text-[var(--primary)] font-semibold overflow-hidden ring-2 ring-[var(--card)] '.$class]) }} style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ $size * .36 }}px">
  @if($src)<img src="{{ $src }}" alt="{{ $name }}" class="w-full h-full object-cover">@else<span>{{ $initials ?: '?' }}</span>@endif
</div>