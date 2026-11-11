@props(['user' => [], 'stats' => []])<x-ui.card>
    <div class="flex flex-col items-center text-center">
        <div
            class="h-16 w-16 rounded-full bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center font-semibold text-xl">
            {{ strtoupper(substr($user['name'] ?? 'R', 0, 1)) }}</div>
        <div class="font-display font-semibold mt-3">{{ $user['name'] ?? 'Account' }}</div>
        <div class="text-xs text-[var(--muted-foreground)] mt-1">{{ $user['email'] ?? '' }}</div>
    </div>
    <div class="grid grid-cols-3 gap-2 mt-5">@foreach($stats as $s)
        <div class="text-center">
            <div class="font-display font-semibold">{{ $s['value'] ?? 0 }}</div>
            <div class="text-[10px] text-[var(--muted-foreground)] mt-1">{{ $s['label'] ?? '' }}</div>
    </div>@endforeach
    </div>
</x-ui.card>