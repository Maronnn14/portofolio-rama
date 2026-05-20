<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin Dashboard - Rama Adin')</title>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('css/variables.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/reset.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/components.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
</head>
<body class="admin-body">
  @yield('content')

  <script src="{{ asset('js/api-client.js') }}"></script>
  <script src="{{ asset('js/data.js') }}"></script>
  <script src="{{ asset('js/admin-config.js') }}"></script>
  <script src="{{ asset('js/admin-auth.js') }}"></script>
  <script src="{{ asset('js/admin-ui.js') }}"></script>
  <script src="{{ asset('js/admin-router.js') }}"></script>
  <script src="{{ asset('js/admin-overview.js') }}"></script>
  <script src="{{ asset('js/admin-profile.js') }}"></script>
  <script src="{{ asset('js/admin-experience.js') }}"></script>
  <script src="{{ asset('js/admin-skills.js') }}"></script>
  <script src="{{ asset('js/admin-projects.js') }}"></script>
  <script src="{{ asset('js/admin-socials.js') }}"></script>
  <script src="{{ asset('js/admin-messages.js') }}"></script>
  <script src="{{ asset('js/admin-gallery.js') }}"></script>
  <script src="{{ asset('js/admin-appearance.js') }}"></script>
  <script src="{{ asset('js/admin-settings.js') }}"></script>
  @stack('scripts')
</body>
</html>
