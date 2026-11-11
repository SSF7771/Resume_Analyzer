@props(['headline' => '', 'subhead' => ''])
<div class="min-h-screen grid lg:grid-cols-2 bg-[var(--card)]">
    <div class="hidden lg:flex p-10 xl:p-16">
        <div class="text-primary-foreground self-end max-w-xl">
            <div class="text-[11px] uppercase tracking-[.14em] font-semibold text-primary-foreground/60 mb-5">Roaster AI
            </div>
            <h2 class="font-display text-5xl font-semibold tracking-tight leading-[1.03]">{!! $headline !!}</h2>
            <p class="text-primary-foreground/60 mt-5 text-base leading-relaxed">{{ $subhead }}</p>
        </div>
    </div>
    <div class="flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">{{ $slot }}</div>
    </div>
</div>