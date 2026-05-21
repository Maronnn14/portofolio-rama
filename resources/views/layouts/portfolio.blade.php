<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Rama Adin - Full Stack Developer')</title>
  <meta name="description" content="@yield('description', 'Full Stack Developer crafting digital experiences with code and creativity.')" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>

  @include('layouts.partials.navbar')

  @yield('content')

  @include('layouts.partials.footer')

  @stack('after_footer')

  <script src="{{ asset('js/api-client.js') }}"></script>
  <script src="{{ asset('js/data.js') }}"></script>
  <script src="{{ asset('js/admin-config.js') }}"></script>
  <script src="{{ asset('js/admin-auth.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
