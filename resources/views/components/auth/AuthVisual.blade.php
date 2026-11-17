{{-- Decorative left panel for login/register. Usage: @include('components.auth.AuthVisual', ['headline' => '...', 'accent' => '...', 'subhead' => '...']) --}}
<div class="auth-panel hidden lg:flex flex-col justify-between p-12 xl:p-16">
    <div class="aurora"><span></span></div>
    <div class="absolute inset-0 pointer-events-none opacity-40"
        style="background-image: radial-gradient(rgb(255 255 255 / .18) 1px, transparent 1px); background-size: 24px 24px; mask-image: radial-gradient(ellipse at 30% 40%, #000 20%, transparent 70%);"></div>

    <a href="/" class="relative flex items-center gap-2.5 w-fit">
        <div class="h-10 w-10 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center"><i data-lucide="flame" class="w-5 h-5"></i></div>
        <span class="font-display text-xl font-extrabold tracking-tight">Roaster</span>
    </a>

    <!-- Floating preview cards -->
    <div class="relative my-10 h-[260px]">
        <div class="float-slow absolute left-0 top-0 w-[300px] rounded-3xl bg-white/10 backdrop-blur-xl border border-white/20 p-5 shadow-2xl">
            <div class="flex items-center justify-between text-xs text-white/70 font-semibold">
                <span>ATS score</span><span class="px-2 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300">+12</span>
            </div>
            <div class="flex items-end gap-2 mt-2">
                <span class="font-display text-5xl font-extrabold tabular" data-count="91">0</span>
                <span class="text-white/60 text-sm mb-1.5">/ 100</span>
            </div>
            <div class="mt-4 h-2 rounded-full bg-white/15 overflow-hidden">
                <div class="h-full w-[91%] rounded-full bg-gradient-to-r from-slate-300 to-sky-200 origin-left animate-[growX_1.4s_cubic-bezier(0.16,1,0.3,1)_both]"></div>
            </div>
        </div>
        <div class="float-slower absolute right-0 xl:right-10 top-28 w-[280px] rounded-3xl bg-white/10 backdrop-blur-xl border border-white/20 p-5 shadow-2xl">
            <div class="flex items-center gap-2 text-xs text-white/70 font-semibold"><i data-lucide="wand-2" class="w-3.5 h-3.5"></i> AI rewrite</div>
            <div class="text-sm text-white/50 line-through mt-2">Worked on backend stuff.</div>
            <div class="text-sm font-semibold mt-1">Built 14 REST endpoints serving 50k daily users.</div>
        </div>
    </div>

    <div class="relative max-w-xl">
        <h2 class="font-display text-5xl xl:text-6xl font-extrabold tracking-tight leading-[1.05]">
            {{ $headline }}<br>
            <span class="font-serif italic font-normal bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">{{ $accent }}</span>
        </h2>
        <p class="text-white/70 mt-6 text-lg leading-relaxed max-w-lg">{{ $subhead }}</p>

        <div class="flex items-center gap-3 mt-8">
            <div class="flex -space-x-2">
                @foreach (['A', 'R', 'S', 'D'] as $i => $l)
                    <span class="h-9 w-9 rounded-full ring-2 ring-[oklch(0.19_0.012_255)] flex items-center justify-center text-xs font-bold"
                        style="background: oklch({{ 0.45 + $i * 0.08 }} 0.03 {{ 240 + $i * 8 }})">{{ $l }}</span>
                @endforeach
            </div>
            <div class="text-sm text-white/70">Powered by <span class="font-bold text-white">Gemini AI</span></div>
        </div>
    </div>
</div>
