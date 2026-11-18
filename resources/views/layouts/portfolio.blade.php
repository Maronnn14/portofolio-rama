<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  @php
    $siteTitle = $siteSettings['site_title'] ?? 'Portfolio';
    // Inline @section('title', '...') content is escaped by Blade before it is
    // stored, so decode once here and let {{ }} escape the final value.
    $pageTitle = trim(html_entity_decode($__env->yieldContent('title'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $fullTitle = $pageTitle !== '' ? $pageTitle.' · '.$siteTitle : $siteTitle;
    $pageDescription = trim(html_entity_decode($__env->yieldContent('description'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $description = $pageDescription !== '' ? $pageDescription : ($siteSettings['meta_description'] ?? '');
    $ogImage = $ogImage ?? $siteSettings['og_image'] ?? $personal['profileImage'] ?? null;
    $ogImage = $ogImage ? \App\Models\Project::mediaUrl($ogImage) : null;
    $canonical = url()->current();
    $socialLinks = $socials->pluck('url')->filter()->values();
  @endphp

  <title>{{ $fullTitle }}</title>
  <meta name="description" content="{{ $description }}" />
  <meta name="robots" content="@yield('robots', 'index, follow')" />
  <link rel="canonical" href="{{ $canonical }}" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <meta property="og:type" content="{{ $ogType ?? 'website' }}" />
  <meta property="og:site_name" content="{{ $siteTitle }}" />
  <meta property="og:title" content="{{ $fullTitle }}" />
  <meta property="og:description" content="{{ $description }}" />
  <meta property="og:url" content="{{ $canonical }}" />
  @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:image" content="{{ $ogImage }}" />
  @else
    <meta name="twitter:card" content="summary" />
  @endif
  <meta name="twitter:title" content="{{ $fullTitle }}" />
  <meta name="twitter:description" content="{{ $description }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
  <script>try{var s=@json(\App\Models\SiteSetting::getSetting('appearance') ?? []);var a=(s&&s.accentColor)?s:JSON.parse(localStorage.getItem('portfolio_appearance')||'{}');if(a.accentColor){var h=a.accentColor.replace('#',''),n=parseInt(h,16),r=(n>>16)&255,g=(n>>8)&255,b=n&255,root=document.documentElement.style,l='#'+[r+(255-r)*0.15,g+(255-g)*0.15,b+(255-b)*0.15].map(function(v){return Math.round(Math.max(0,Math.min(255,v))).toString(16).padStart(2,'0')}).join(''),d='#'+[r*0.85,g*0.85,b*0.85].map(function(v){return Math.round(Math.max(0,Math.min(255,v))).toString(16).padStart(2,'0')}).join('');root.setProperty('--accent',a.accentColor);root.setProperty('--accent-light',l);root.setProperty('--accent-dark',d);root.setProperty('--accent-glow','rgba('+r+','+g+','+b+',0.25)');}if(a.theme){document.documentElement.setAttribute('data-theme',a.theme);}}catch(e){}</script>

  @if($socialLinks->isNotEmpty())
    <script type="application/ld+json">
      {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => \App\Models\PersonalInfo::displayName($personal),
        'jobTitle' => $personal['role'] ?? null,
        'url' => url('/'),
        'image' => $ogImage,
        'email' => $personal['email'] ?? null,
        'sameAs' => $socialLinks,
      ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
  @endif
</head>
<body>

  <a class="skip-link" href="#main-content">Skip to content</a>

  @include('layouts.partials.navbar')

  <main id="main-content">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

  @stack('after_footer')

  <script src="{{ asset('js/api-client.js') }}"></script>
  <script src="{{ asset('js/data.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
