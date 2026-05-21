@extends('layouts.admin')

@section('title', 'Admin Dashboard — Rama Adin')

@section('content')
<!-- Admin Sidebar -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__header">
      <a href="index.html" class="admin-sidebar__logo">R<span>.</span></a>
      <span class="admin-sidebar__title">Admin</span>
      <button class="admin-sidebar__collapse" id="sidebar-collapse" aria-label="Toggle sidebar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
    </div>
    <nav class="admin-sidebar__nav" id="admin-nav">
      <a href="#overview" class="admin-nav__item active" data-section="overview">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Overview</span>
      </a>
      <a href="#profile" class="admin-nav__item" data-section="profile">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Profile</span>
      </a>
      <a href="#experience" class="admin-nav__item" data-section="experience">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>Experience</span>
      </a>
      <a href="#skills" class="admin-nav__item" data-section="skills">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        <span>Skills</span>
      </a>
      <a href="#projects" class="admin-nav__item" data-section="projects">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        <span>Projects</span>
      </a>
      <a href="#socials" class="admin-nav__item" data-section="socials">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <span>Socials</span>
      </a>
      <a href="#messages" class="admin-nav__item" data-section="messages">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>Messages</span>
        <span class="admin-nav__badge" id="nav-msg-badge" style="display:none;">0</span>
      </a>
      <a href="#gallery" class="admin-nav__item" data-section="gallery">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <span>Gallery</span>
      </a>
      <a href="#appearance" class="admin-nav__item" data-section="appearance">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Appearance</span>
      </a>
      <a href="#settings" class="admin-nav__item" data-section="settings">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>Settings</span>
      </a>
    </nav>
    <div class="admin-sidebar__footer">
      <button class="admin-nav__item admin-nav__item--danger" onclick="AdminAuth.logout().then(() => window.location.href='index.html');">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Logout</span>
      </button>
    </div>
  </aside>

  <!-- Admin Main Content -->
  <div class="admin-main" id="admin-main">
    <!-- Top Bar -->
    <header class="admin-topbar" id="admin-topbar">
      <div class="admin-topbar__left">
        <button class="admin-topbar__menu-btn" id="mobile-sidebar-toggle" aria-label="Toggle sidebar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 class="admin-topbar__title" id="admin-page-title">Overview</h1>
      </div>
      <div class="admin-topbar__right">
        <a href="index.html" class="admin-topbar__link" target="_blank">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          View Site
        </a>
        <div class="admin-topbar__user">
          <div class="admin-topbar__avatar" id="admin-avatar">A</div>
          <span class="admin-topbar__username" id="admin-username">Admin</span>
        </div>
      </div>
    </header>

    <!-- Content Area -->
    <main class="admin-content" id="admin-content">
      <!-- Rendered by router -->
    </main>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast"></div>

  <!-- Scripts -->
@endsection

@push('scripts')
<script>
    // Route guard: redirect if not authenticated
    document.addEventListener('DOMContentLoaded', async () => {
      const authed = await AdminAuth.init();
      if (!authed) {
        window.location.href = 'index.html';
        return;
      }
      AdminRouter.init();

      // Set username
      const usernameEl = document.getElementById('admin-username');
      const avatarEl = document.getElementById('admin-avatar');
      const user = AdminAuth.getUser();
      if (usernameEl) usernameEl.textContent = user.name || 'Admin';
      if (avatarEl) avatarEl.textContent = (user.name || 'A').charAt(0).toUpperCase();

      // Sidebar collapse
      const sidebar = document.getElementById('admin-sidebar');
      const collapseBtn = document.getElementById('sidebar-collapse');
      const mobileToggle = document.getElementById('mobile-sidebar-toggle');

      collapseBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
      });

      mobileToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.toggle('mobile-open');
      });

      // Close mobile sidebar on click outside
      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
          if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
            sidebar.classList.remove('mobile-open');
          }
        }
      });
    });
  </script>
@endpush
