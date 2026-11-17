<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Roaster')</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap"
    rel="stylesheet">

  <!-- Tailwindcss + app css/js -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script>
    window.Roaster = Object.assign(window.Roaster || {}, {
      apiBase: '/api',
      tokenKey: 'auth_token'
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
</head>

<body data-page="@yield('page', 'unknown')"
  class="min-h-screen bg-[var(--background)] text-[var(--foreground)] antialiased">
  <div id="scroll-progress" class="no-print"></div>

  @yield('body')

  <div id="toast-root"
    class="fixed top-5 right-5 z-[100] flex flex-col gap-2.5 w-[360px] max-w-[calc(100vw-32px)] pointer-events-none">
  </div>

  <script>
    lucide.createIcons();
  </script>

  <!-- For local environment and testing -->
  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>

</html>
