<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme ?? 'system' }}"
  @class(['dark' => ($theme ?? 'system') == 'dark'])>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Inline script to detect theme preference and prevent flash/hydration mismatches --}}
  <script>
    (function() {
      var el = document.documentElement;
      var serverTheme = el.getAttribute('data-theme') || 'system';
      var key = '{{ config('app.theme_key', 'theme') }}';

      // read stored value without writing
      var stored = null;
      try {
        stored = window.localStorage ? localStorage.getItem(key) : null;
      } catch (_) {
        stored = null;
      }
      if (!stored) {
        var cookie = '; ' + document.cookie;
        var parts = cookie.split('; ' + key + '=');
        if (parts.length === 2) stored = parts.pop().split(';').shift();
      }

      var prefersDark = typeof window.matchMedia === 'function' && window.matchMedia('(prefers-color-scheme: dark)')
        .matches;
      var appliedMode = stored || serverTheme || 'system';
      var isDark = (appliedMode === 'dark') || (appliedMode === 'system' && prefersDark);
      el.classList.toggle('dark', isDark);
      el.style.colorScheme = isDark ? 'dark' : 'light';
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
