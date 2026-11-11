@extends('layouts.app')
@section('title', 'Coming soon - Roaster')
@section('page', 'placeholder')
@section('body')
    <div class="min-h-screen flex bg-[var(--background)]">
        <div class="no-print">@include('components.layout.Sidebar')</div>
        <main class="flex-1 px-6 md:px-8 py-6 max-w-[1600px] mx-auto w-full">
            <div class="no-print">@include('components.layout.Topbar')</div>
            <div class="page-enter"><x-ui.card class="h-[60vh] flex flex-col items-center justify-center text-center">
                    <div
                        class="h-14 w-14 rounded-2xl bg-[var(--secondary)] text-[var(--primary)] flex items-center justify-center mb-4">
                        <i data-lucide="construction" class="w-6 h-6"></i>
                    </div>
                    <div class="font-display text-xl font-semibold tracking-tight">Coming soon</div>
                    <p class="text-sm text-[var(--muted-foreground)] mt-1 max-w-sm">Coming up in the next milestone.</p>
                </x-ui.card></div>
        </main>
    </div>
@endsection