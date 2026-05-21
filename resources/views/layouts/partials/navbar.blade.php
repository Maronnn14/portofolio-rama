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
      <button class="navbar__admin-lock" id="navbar-admin-lock" onclick="openLoginModal()" aria-label="Admin login"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>
      <div class="navbar__admin-badge" id="navbar-admin-badge" onclick="toggleAdminDropdown(event)">A<div class="admin-dropdown" id="admin-dropdown"><a href="{{ url('/admin.html') }}" class="admin-dropdown__item">🧩 Dashboard</a><a href="{{ url('/') }}" class="admin-dropdown__item">👁 View Site</a><div class="admin-dropdown__divider"></div><button class="admin-dropdown__item admin-dropdown__item--danger" onclick="adminLogout()">🚪 Logout</button></div></div>
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
      <button class="navbar__mobile-admin-btn" id="mobile-admin-login-btn" onclick="openLoginModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Admin Login
      </button>
      
      <div class="navbar__mobile-admin-controls" id="mobile-admin-controls" style="display:none;">
        <div class="navbar__mobile-admin-info">
          <div class="navbar__mobile-admin-avatar">A</div>
          <div class="navbar__mobile-admin-meta">
            <span class="navbar__mobile-admin-name" id="mobile-admin-name">Admin</span>
            <span class="navbar__mobile-admin-role">Administrator</span>
          </div>
        </div>
        <div class="navbar__mobile-admin-links">
          <a href="{{ url('/admin.html') }}" class="navbar__mobile-admin-link"><span>🧩</span> Dashboard</a>
          <a href="{{ url('/') }}" class="navbar__mobile-admin-link"><span>👁</span> View Site</a>
          <button class="navbar__mobile-admin-link navbar__mobile-admin-link--danger" onclick="adminLogout()"><span>🚪</span> Logout</button>
        </div>
      </div>
    </div>
  </div>
</nav>