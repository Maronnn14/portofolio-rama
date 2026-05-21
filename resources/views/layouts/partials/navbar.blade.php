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
    <div style="display:flex;align-items:center;">
      <button class="navbar__admin-lock" id="navbar-admin-lock" onclick="openLoginModal()" aria-label="Admin login"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>
      <div class="navbar__admin-badge" id="navbar-admin-badge" onclick="toggleAdminDropdown()">A<div class="admin-dropdown" id="admin-dropdown"><a href="{{ url('/admin.html') }}" class="admin-dropdown__item">🧩 Dashboard</a><a href="{{ url('/') }}" class="admin-dropdown__item">👁 View Site</a><div class="admin-dropdown__divider"></div><button class="admin-dropdown__item admin-dropdown__item--danger" onclick="adminLogout()">🚪 Logout</button></div></div>
      <button class="navbar__hamburger" aria-label="Toggle menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
  <div class="navbar__mobile-menu" role="dialog" aria-label="Mobile navigation">
    <a href="{{ url('/') }}" class="navbar__mobile-link">Home</a>
    <a href="{{ url('/about.html') }}" class="navbar__mobile-link">About</a>
    <a href="{{ url('/skills.html') }}" class="navbar__mobile-link">Skills</a>
    <a href="{{ url('/projects.html') }}" class="navbar__mobile-link">Projects</a>
    <a href="{{ url('/contact.html') }}" class="navbar__mobile-link">Contact</a>
    <button class="navbar__mobile-link" style="opacity:0.4;border:none;background:none;cursor:pointer;" onclick="openLoginModal()" id="navbar-mobile-admin">🔒 Admin</button>
  </div>
</nav>