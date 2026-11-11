@props(['version' => [], 'title' => 'Resume', 'user' => []])
@php($s = $version['parsed_sections'] ?? []) @php($b = $s['basics'] ?? [])
<div id="resume-print" class="max-w-[780px] mx-auto bg-white text-foreground p-8 sm:p-10 print-card">
  <h1 class="text-3xl font-bold tracking-tight">{{ $b['name'] ?? $title }}</h1>
  @if(!empty($b['title']))
    <div class="text-base mt-1">{{ $b['title'] }}</div>
  @endif
  @if(collect([$b['email'] ?? null, $b['phone'] ?? null, $b['location'] ?? null])->filter()->isNotEmpty())
    <div class="text-xs text-gray-500 mt-2">
      {{ collect([$b['email'] ?? null, $b['phone'] ?? null, $b['location'] ?? null])->filter()->implode(' · ') }}
    </div>
  @endif
  @if(!empty($s['summary']))
    <section class="mt-6">
      <h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Summary</h2>
      <p class="text-sm mt-2 leading-relaxed">{{ $s['summary'] }}</p>
    </section>
  @endif
  @if(!empty($s['experience']))
    <section class="mt-5">
      <h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Experience</h2>
      <div class="mt-3 space-y-4">
        @foreach($s['experience'] as $e)
          <div>
            <div class="font-semibold text-sm">{{ $e['role'] ?? '' }}</div>
            <div class="text-xs text-gray-500">
              {{ $e['company'] ?? '' }}{{ !empty($e['period']) ? ' · ' . $e['period'] : '' }}
            </div>
            @if(!empty($e['bullets']))
              <ul class="list-disc ml-5 mt-1 text-sm space-y-1">
                @foreach($e['bullets'] as $x)
                  <li>{{ $x }}</li>
                @endforeach
              </ul>
            @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif
  @if(!empty($s['education']))
    <section class="mt-5">
      <h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Education</h2>
      <div class="mt-3 space-y-2">
        @foreach($s['education'] as $e)
          <div class="text-sm"><span
              class="font-semibold">{{ $e['degree'] ?? '' }}</span>{{ !empty($e['school']) ? ' · ' . $e['school'] : '' }}
          </div>
        @endforeach
      </div>
    </section>
  @endif
  @if(!empty($s['skills']))
    <section class="mt-5">
      <h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Skills</h2>
      <div class="text-sm mt-2">{{ implode(' · ', (array) $s['skills']) }}</div>
    </section>
  @endif
  @if(!empty($s['projects']))
    <section class="mt-5">
      <h2 class="text-sm font-bold uppercase tracking-wider border-b border-gray-300 pb-1">Projects</h2>
      <div class="mt-3 space-y-2">
        @foreach($s['projects'] as $p)
          <div class="text-sm"><span
              class="font-semibold">{{ $p['name'] ?? '' }}</span>{{ !empty($p['tech']) ? ' · ' . implode(' · ', (array) $p['tech']) : '' }}
          </div>
        @endforeach
      </div>
    </section>
  @endif
</div>