@extends('layouts.app')
@section('title', 'Roaster - Sharpen your resume')
@section('page', 'landing')
@section('body')
  <div class="min-h-screen bg-background text-foreground overflow-x-hidden">
    @include('components.landing.navbar')
    <main>
      @include('components.landing.HeroSection')
      @include('components.landing.FeaturesSection')
      @include('components.landing.HowItWorks')
      @include('components.landing.DashboardPreviewSection')
      @include('components.landing.BenefitsSection')
      @include('components.landing.ctasection')
    </main>
    @include('components.landing.Footer')
  </div>
@endsection