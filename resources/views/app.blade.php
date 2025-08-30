<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($theme ?? 'system') == 'dark'])>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Inline script to detect theme preference and prevent flash/hydration mismatches --}}
  <script>
    (function() {
      const theme = '{{ $theme ?? 'system' }}';

      // Apply theme immediately to prevent flash
      if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
      }

      // Sync server-side theme with client-side storage to prevent hydration mismatches
      try {
        // For first-time visitors (no cookie), ensure localStorage matches server default
        if ('{{ $theme }}' === 'system' || !'{{ $theme }}') {
          // Only set if localStorage is empty to avoid overriding user preferences
          if (!localStorage.getItem('theme')) {
            localStorage.setItem('theme', 'system');
          }
        } else {
          // Sync existing server-side theme preference with client-side storage
          localStorage.setItem('theme', theme);
        }
      } catch (e) {
        // Ignore if localStorage is not available
      }
    })();
  </script>

  {{-- Inline style to set the HTML background color based on our theme in app.css --}}
  <style>
    html {
      background-color: oklch(1 0 0);
    }

    html.dark {
      background-color: oklch(0.145 0 0);
    }
  </style>

  <title inertia>{{ config('app.name', 'Laravel') }}</title>

  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=outfit:100,200,300,400,500,600,700,800,900" rel="stylesheet" />

  @routes
  @vite(['resources/js/app.ts'])
  @inertiaHead
</head>

<body class="font-sans antialiased">
  @inertia
</body>

</html>
