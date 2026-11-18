<nav class="navbar" role="navigation" aria-label="Main navigation">
  <div class="navbar__inner">
    <a href="{{ url('/') }}" class="navbar__logo" aria-label="Home">R<span>.</span></a>
    <div class="navbar__links">
      <a href="{{ url('/') }}" class="navbar__link{{ request()->is('/') || request()->is('index.html') ? ' active' : '' }}">Home</a>
      <a href="{{ url('/about.html') }}" class="navbar__link{{ request()->is('about.html') ? ' active' : '' }}">About</a>
      <a href="{{ url('/skills.html') }}" class="navbar__link{{ request()->is('skills.html') ? ' active' : '' }}">Skills</a>
      <a href="{{ url('/projects.html') }}" class="navbar__link{{ request()->is('projects.html') || request()->is('project-detail.html') ? ' active' : '' }}">Projects</a>
      <a href="{{ url('/contact.html') }}" class="navbar__link{{ request()->is('contact.html') ? ' active' : '' }}">Contact</a>
    </div>
    <div class="flex items-center">
      <a href="{{ url('/admin') }}" class="navbar__admin-lock" id="navbar-admin-lock" aria-label="Admin Panel"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></a>
      <button class="navbar__hamburger" aria-label="Toggle menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
  <div class="navbar__mobile-overlay" id="mobile-overlay"></div>
  <div class="navbar__mobile-menu" id="mobile-menu" role="dialog" aria-label="Mobile navigation">
    <div class="navbar__mobile-header">
      <span class="navbar__mobile-logo">R<span>.</span></span>
    </div>
    <nav class="navbar__mobile-nav">
      <a href="{{ url('/') }}" class="navbar__mobile-link{{ request()->is('/') || request()->is('index.html') ? ' active' : '' }}">Home</a>
      <a href="{{ url('/about.html') }}" class="navbar__mobile-link{{ request()->is('about.html') ? ' active' : '' }}">About</a>
      <a href="{{ url('/skills.html') }}" class="navbar__mobile-link{{ request()->is('skills.html') ? ' active' : '' }}">Skills</a>
      <a href="{{ url('/projects.html') }}" class="navbar__mobile-link{{ request()->is('projects.html') || request()->is('project-detail.html') ? ' active' : '' }}">Projects</a>
      <a href="{{ url('/contact.html') }}" class="navbar__mobile-link{{ request()->is('contact.html') ? ' active' : '' }}">Contact</a>
    </nav>
    <div class="navbar__mobile-footer" id="navbar-mobile-footer">
      <a href="{{ url('/admin') }}" class="navbar__mobile-admin-btn" id="mobile-admin-login-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Admin Panel
      </a>
    </div>
  </div>
</nav>