@php
  $contactFullName = $personal['fullName'] ?? $personal['name'] ?? 'Rama Adin';
  $contactEmail = $personal['email'] ?? '';
  $contactLocation = $personal['location'] ?? '';
  $contactGithub = $personal['github'] ?? 'Maronnn14';
  $contactGithubUrl = $contactGithub ? "https://github.com/$contactGithub" : '';
  $socialIconMap = ['github' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>', 'linkedin' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>', 'instagram' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.71...', 'twitter' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>', 'youtube' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'];
@endphp

@extends('layouts.portfolio')

@section('title', "Contact — $contactFullName")
@section('description', "Get in touch with $contactFullName — full stack developer available for freelance, collaboration, and new opportunities.")

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Get in Touch</span>
    <h1 class="page-header__title reveal">Let's <span class="text-accent">Connect</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Have a project in mind? I'd love to hear from you.</p>
  </header>

  <section class="section--compact contact-section">
    <div class="container">
      <div class="contact-grid">

        <div class="contact-info reveal">
          <div class="contact-info__card">
            <div class="contact-info__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <h3 class="contact-info__label">Email</h3>
              <a href="mailto:{{ $contactEmail }}" class="contact-info__value" id="contact-email">{{ $contactEmail ?: 'rama@example.com' }}</a>
            </div>
          </div>

          <div class="contact-info__card">
            <div class="contact-info__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
              <h3 class="contact-info__label">Location</h3>
              <span class="contact-info__value" id="contact-location">{{ $contactLocation ?: 'Indonesia' }}</span>
            </div>
          </div>

          <div class="contact-info__card">
            <div class="contact-info__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
              <h3 class="contact-info__label">Availability</h3>
              <span class="contact-info__value contact-info__value--accent">Open to Opportunities</span>
            </div>
          </div>

          <div class="contact-socials">
            <h3 class="contact-socials__heading">Find me online</h3>
            <div class="contact-socials__grid" id="contact-socials-grid">
              @forelse($socials as $s)
                <a href="{{ $s->url }}" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="{{ $s->platform }}">
                  <span class="social-btn__icon">{!! $socialIconMap[$s->icon] ?? $s->platform[0] !!}</span>
                  <span class="social-btn__name">{{ $s->platform }}</span>
                </a>
              @empty
                <span class="tag opacity-60">No social links yet</span>
              @endforelse
            </div>
          </div>
        </div>

        <div class="contact-form-wrapper reveal stagger-2">
          <form class="contact-form" id="contact-form">
            <div class="contact-form__row">
              <div class="form-group">
                <label for="contact-name" class="form-label">Full Name *</label>
                <input type="text" id="contact-name" class="form-input" placeholder="Your name" required maxlength="100" />
              </div>
              <div class="form-group">
                <label for="contact-email-input" class="form-label">Email *</label>
                <input type="email" id="contact-email-input" class="form-input" placeholder="you@example.com" required />
              </div>
            </div>
            <div class="form-group">
              <label for="contact-subject" class="form-label">Subject *</label>
              <input type="text" id="contact-subject" class="form-input" placeholder="What is this about?" required maxlength="150" />
            </div>
            <div class="form-group">
              <label for="contact-message" class="form-label">Message *</label>
              <textarea id="contact-message" class="form-textarea" placeholder="Tell me about your project or idea..." required maxlength="2000" rows="6"></textarea>
              <p class="form-hint"><span id="contact-char-count">0</span> / 2000</p>
            </div>
            <button type="submit" class="btn btn--primary btn--lg contact-form__submit" id="contact-submit">
              <span class="contact-form__submit-text">Send Message</span>
              <span class="contact-form__submit-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </span>
            </button>
          </form>

          <div class="contact-form__success" id="contact-success" style="display: none;">
            <div class="contact-form__success-icon">✉️</div>
            <h3>Message Sent!</h3>
            <p>Thanks for reaching out. I'll get back to you as soon as possible.</p>
            <button class="btn btn--secondary" onclick="resetContactForm()">Send Another</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section github-section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Open Source</span>
        <h2 class="section-heading">GitHub <span class="text-accent">Activity</span></h2>
      </div>
      <div class="github-profile reveal stagger-1" id="github-profile">
        @if($contactGithub)
          <a href="{{ $contactGithubUrl }}" target="_blank" class="github-profile__card">
            <div class="github-profile__avatar">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
            </div>
            <div class="github-profile__info">
              <h3>@ {{ $contactGithub }}</h3>
              <p>View my open source work</p>
            </div>
          </a>
        @else
          <span class="tag opacity-60">GitHub not configured</span>
        @endif
      </div>
    </div>
  </section>

  <div class="toast" id="toast"></div>
@endsection

@push('scripts')
  <script src="{{ asset('js/contact.js') }}"></script>
@endpush
