@extends('layouts.portfolio')

@section('title', "Contact — Rama Adin")
@section('description', "Get in touch with Rama Adin — full stack developer available for freelance, collaboration, and new opportunities.")

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/contact.css') }}" />
@endpush

@section('content')
<!-- Page Header -->
  <header class="page-header">
    <span class="section-label" style="justify-content: center;">Get in Touch</span>
    <h1 class="page-header__title reveal">Let's <span class="text-accent">Connect</span></h1>
    <p class="page-header__subtitle reveal stagger-1">Have a project in mind? I'd love to hear from you.</p>
  </header>

  <!-- Contact Content -->
  <section class="section--compact contact-section">
    <div class="container">
      <div class="contact-grid">

        <!-- Left: Info + Socials -->
        <div class="contact-info reveal">
          <div class="contact-info__card">
            <div class="contact-info__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <h3 class="contact-info__label">Email</h3>
              <a href="mailto:rama@example.com" class="contact-info__value" id="contact-email">rama@example.com</a>
            </div>
          </div>

          <div class="contact-info__card">
            <div class="contact-info__icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
              <h3 class="contact-info__label">Location</h3>
              <span class="contact-info__value" id="contact-location">Indonesia</span>
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

          <!-- Social Icons -->
          <div class="contact-socials">
            <h3 class="contact-socials__heading">Find me online</h3>
            <div class="contact-socials__grid" id="contact-socials-grid">
              <!-- Rendered by JS -->
            </div>
          </div>
        </div>

        <!-- Right: Contact Form -->
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

          <!-- Success State -->
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

  <!-- GitHub Section -->
  <section class="section github-section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label" style="justify-content: center;">Open Source</span>
        <h2 class="section-heading">GitHub <span class="text-accent">Activity</span></h2>
      </div>
      <div class="github-profile reveal stagger-1" id="github-profile">
        <!-- Rendered by JS -->
      </div>
    </div>
  </section>
<!-- Toast -->
  <div class="toast" id="toast"></div>
@endsection

@push('scripts')
  <script src="{{ asset('js/contact.js') }}"></script>
@endpush
