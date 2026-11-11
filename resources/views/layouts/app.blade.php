<!DOCTYPE html>
<html lang="en" class="dark"> <!-- Remove data-theme, add 'dark' or 'light' class -->

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Roaster')</title>
  <!-- Tailwindcss + Public css file -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @php
    $appUrl = config('app.url');
    // Ensure scheme exists if VERCEL_URL was used
    if ($appUrl && !str_starts_with($appUrl, 'http://') && !str_starts_with($appUrl, 'https://'))
      $appUrl = 'https://' . $appUrl;
    
    $apiBase = rtrim($appUrl, '/') . '/api';
  @endphp

  <script>
    window.Roaster = Object.assign(window.Roaster || {}, {
      apiBase: "{{ $apiBase }}",
      tokenKey: 'auth_token'
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body data-page="@yield('page', 'unknown')"
  class="min-h-screen bg-[var(--background)] text-[var(--foreground)] antialiased">
  @yield('body')

  <div id="toast-root"
    class="fixed top-20 right-10 z-[100] flex flex-col gap-2.5 w-[360px] max-w-[calc(100vw-32px)] pointer-events-none">
  </div>

  <script>
    window.ROASTER = {
      apiBase: @json(url('/api')),
      tokenKey: 'auth_token'
    };
  </script>

  <script>
    lucide.createIcons();
  </script>

  @stack('scripts')
</body>

</html>