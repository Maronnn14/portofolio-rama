@php
  $aboutLabels = \App\Models\SiteSetting::navigation()['labels'];

  $aboutLinks = [
      ['label' => $aboutLabels['about_biodata'] ?? 'Biodata & Personal Branding', 'route' => 'about.biodata', 'url' => route('about.biodata'), 'active' => request()->routeIs('about.biodata')],
      ['label' => $aboutLabels['about_cv'] ?? 'Download CV', 'route' => 'about.cv', 'url' => route('about.cv'), 'active' => request()->routeIs('about.cv')],
      ['label' => $aboutLabels['about_interests'] ?? 'Interests & Hobbies', 'route' => 'about.interests', 'url' => route('about.interests'), 'active' => request()->routeIs('about.interests')],
      ['label' => $aboutLabels['about_blog'] ?? 'Daily Blog', 'route' => 'about.blog', 'url' => route('about.blog'), 'active' => request()->routeIs('about.blog', 'about.blog.show')],
  ];
@endphp
<nav class="container about-subnav reveal" aria-label="About sections">
  @foreach($aboutLinks as $link)
    <a href="{{ $link['url'] }}" class="about-subnav__link{{ $link['active'] ? ' active' : '' }}" @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
  @endforeach
</nav>
