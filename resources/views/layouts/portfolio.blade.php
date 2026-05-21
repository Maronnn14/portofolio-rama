<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Rama Adin - Full Stack Developer')</title>
  <meta name="description" content="@yield('description', 'Full Stack Developer crafting digital experiences with code and creativity.')" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
  <script>try{var a=JSON.parse(localStorage.getItem('portfolio_appearance')||'{}');if(a.accentColor){var h=a.accentColor.replace('#',''),n=parseInt(h,16),r=(n>>16)&255,g=(n>>8)&255,b=n&255,root=document.documentElement.style,l='#'+[r+(255-r)*0.15,g+(255-g)*0.15,b+(255-b)*0.15].map(function(v){return Math.round(Math.max(0,Math.min(255,v))).toString(16).padStart(2,'0')}).join(''),d='#'+[r*0.85,g*0.85,b*0.85].map(function(v){return Math.round(Math.max(0,Math.min(255,v))).toString(16).padStart(2,'0')}).join('');root.setProperty('--accent',a.accentColor);root.setProperty('--accent-light',l);root.setProperty('--accent-dark',d);root.setProperty('--accent-glow','rgba('+r+','+g+','+b+',0.15)');root.setProperty('--accent-glow-strong','rgba('+r+','+g+','+b+',0.3)');root.setProperty('--border-accent','rgba('+r+','+g+','+b+',0.3)');root.setProperty('--shadow-glow','0 0 30px rgba('+r+','+g+','+b+',0.15)');root.setProperty('--shadow-glow-strong','0 0 50px rgba('+r+','+g+','+b+',0.25)');root.setProperty('--color-accent',a.accentColor);root.setProperty('--color-accent-light',l);root.setProperty('--color-accent-dark',d)}}catch(e){}</script>
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
