@php
  use App\Models\PersonalInfo;
  use App\Support\Navigation;

  $navItems = Navigation::items();
  $logo = PersonalInfo::initials($personal);
@endphp
<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="navbar__inner">
    <a href="{{ route('home') }}" class="navbar__logo" aria-label="{{ $siteSettings['site_title'] ?? 'Home' }}">{{ $logo }}<span>.</span></a>

    <div class="navbar__links">
      @foreach($navItems as $item)
        @if(empty($item['children']))
          <a href="{{ $item['url'] }}" class="navbar__link{{ $item['active'] ? ' active' : '' }}" @if($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
        @else
          <div class="navbar__item navbar__item--dropdown" data-dropdown>
            <button type="button" class="navbar__link navbar__trigger{{ $item['active'] ? ' active' : '' }}" aria-expanded="false" aria-haspopup="true" aria-controls="nav-menu-{{ $loop->index }}">
              {{ $item['label'] }}
              <svg class="navbar__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="navbar__dropdown" id="nav-menu-{{ $loop->index }}">
              @foreach($item['children'] as $child)
                <a href="{{ $child['url'] }}" class="navbar__dropdown-link{{ $child['active'] ? ' active' : '' }}" @if($child['active']) aria-current="page" @endif>{{ $child['label'] }}</a>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </div>

    <div class="flex items-center">
      <button type="button" class="navbar__theme" id="theme-toggle" aria-pressed="false" aria-label="Switch to dark mode" title="Switch to dark mode">
        <svg class="navbar__theme-icon navbar__theme-icon--moon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
        <svg class="navbar__theme-icon navbar__theme-icon--sun" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></svg>
      </button>
      <a href="{{ url('/admin') }}" class="navbar__admin-lock" id="navbar-admin-lock" aria-label="Admin Panel"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></a>
      <button class="navbar__hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu"><span></span><span></span><span></span></button>
    </div>
  </div>

  <div class="navbar__mobile-overlay" id="mobile-overlay"></div>
  <div class="navbar__mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="Mobile navigation">
    <div class="navbar__mobile-header">
      <span class="navbar__mobile-logo">{{ $logo }}<span>.</span></span>
      <button type="button" class="navbar__mobile-close" aria-label="Close menu" data-close-mobile>&times;</button>
    </div>
    <nav class="navbar__mobile-nav">
      @foreach($navItems as $item)
        @if(empty($item['children']))
          <a href="{{ $item['url'] }}" class="navbar__mobile-link{{ $item['active'] ? ' active' : '' }}" @if($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
        @else
          <div class="navbar__mobile-group" data-mobile-group>
            <button type="button" class="navbar__mobile-trigger{{ $item['active'] ? ' active' : '' }}" aria-expanded="false" aria-controls="mobile-submenu-{{ $loop->index }}">
              {{ $item['label'] }}
              <svg class="navbar__chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="navbar__mobile-submenu" id="mobile-submenu-{{ $loop->index }}" hidden>
              @foreach($item['children'] as $child)
                <a href="{{ $child['url'] }}" class="navbar__mobile-sublink{{ $child['active'] ? ' active' : '' }}" @if($child['active']) aria-current="page" @endif>{{ $child['label'] }}</a>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </nav>
    <div class="navbar__mobile-footer" id="navbar-mobile-footer">
      <button type="button" class="navbar__mobile-theme" data-theme-toggle aria-label="Switch theme">
        <span class="navbar__mobile-theme-dot" aria-hidden="true"></span>
        <span data-theme-label>Dark mode</span>
      </button>
      <a href="{{ url('/admin') }}" class="navbar__mobile-admin-btn" id="mobile-admin-login-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Admin Panel
      </a>
    </div>
  </div>
</nav>
