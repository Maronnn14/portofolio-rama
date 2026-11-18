@php
  $contactName = \App\Models\PersonalInfo::displayName($personal);
  $contactEmail = $personal['email'] ?? $siteSettings['contact_email'] ?? null;
  $contactLocation = $personal['location'] ?? null;
  $contactGithub = $personal['github'] ?? null;
  $contactGithubUrl = $contactGithub ? "https://github.com/{$contactGithub}" : null;
  $availability = $siteSettings['availability'] ?? 'Open to opportunities';
  $socialIconMap = [
      'github' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>',
      'linkedin' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.42v1.56h.05a3.75 3.75 0 013.37-1.85c3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 110-4.13 2.07 2.07 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>',
      'instagram' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg>',
      'twitter' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L1 2h6.5l4.4 5.8L18.9 2z"/></svg>',
      'youtube' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 00.5 6.2 31 31 0 000 12a31 31 0 00.5 5.8 3 3 0 002.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 002.1-2.1A31 31 0 0024 12a31 31 0 00-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2-3.6z"/></svg>',
  ];
  $success = session('contact_success');
@endphp

@extends('layouts.portfolio')

@section('title', 'Contact')
@section('description', 'Get in touch for freelance work, collaboration, or a friendly conversation about software.')

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
          @if($contactEmail)
            <div class="contact-info__card contact-info__card--email">
              <div class="contact-info__icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </div>
              <div class="contact-info__body">
                <h2 class="contact-info__label">Email</h2>
                <div class="contact-info__email-row">
                  <a href="mailto:{{ $contactEmail }}" class="contact-info__value contact-info__value--email">{{ $contactEmail }}</a>
                  <button type="button" class="contact-info__copy" id="copy-email-btn" data-copy="{{ $contactEmail }}" aria-label="Copy email address {{ $contactEmail }} to clipboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span class="contact-info__copy-text">Copy</span>
                  </button>
                </div>
                <p class="contact-info__hint">Fastest way to reach me — I usually reply within 1–2 days.</p>
              </div>
            </div>
          @endif

          @if($contactLocation)
            <div class="contact-info__card">
              <div class="contact-info__icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <h2 class="contact-info__label">Location</h2>
                <span class="contact-info__value">{{ $contactLocation }}</span>
              </div>
            </div>
          @endif

          @if($availability)
            <div class="contact-info__card">
              <div class="contact-info__icon" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div>
                <h2 class="contact-info__label">Availability</h2>
                <span class="contact-info__value contact-info__value--accent">{{ $availability }}</span>
              </div>
            </div>
          @endif

          <div class="contact-socials">
            <h2 class="contact-socials__heading">Find me online</h2>
            <div class="contact-socials__grid" id="contact-socials-grid">
              @forelse($socials as $s)
                <a href="{{ $s->url }}" target="_blank" rel="noopener noreferrer" class="social-btn" aria-label="{{ $s->platform }}">
                  <span class="social-btn__icon">{!! $socialIconMap[$s->icon] ?? e(mb_substr($s->platform, 0, 1)) !!}</span>
                  <span class="social-btn__name">{{ $s->platform }}</span>
                </a>
              @empty
                <span class="tag opacity-60">No social links yet</span>
              @endforelse
            </div>
          </div>
        </div>

        <div class="contact-form-wrapper reveal stagger-2">
          @if($success)
            <div class="contact-form__success" id="contact-success-server" role="status">
              <div class="contact-form__success-icon" aria-hidden="true">✉️</div>
              <h2>Message Sent!</h2>
              <p>{{ $success }}</p>
              <a href="{{ route('contact') }}" class="btn btn--secondary">Send Another</a>
            </div>
          @endif

          <form class="contact-form" id="contact-form" method="POST" action="{{ route('contact.store') }}" @if($success) hidden @endif>
            @csrf
            <div class="contact-form__row">
              <div class="form-group">
                <label for="contact-name" class="form-label">Full Name *</label>
                <input type="text" id="contact-name" name="name" value="{{ old('name') }}" class="form-input @error('name') form-input--error @enderror" placeholder="Your name" required maxlength="100" autocomplete="name" />
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
              </div>
              <div class="form-group">
                <label for="contact-email-input" class="form-label">Email *</label>
                <input type="email" id="contact-email-input" name="email" value="{{ old('email') }}" class="form-input @error('email') form-input--error @enderror" placeholder="you@example.com" required autocomplete="email" />
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="form-group">
              <label for="contact-subject" class="form-label">Subject *</label>
              <input type="text" id="contact-subject" name="subject" value="{{ old('subject') }}" class="form-input @error('subject') form-input--error @enderror" placeholder="What is this about?" required maxlength="150" />
              @error('subject')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
              <label for="contact-message" class="form-label">Message *</label>
              <textarea id="contact-message" name="message" class="form-textarea @error('message') form-input--error @enderror" placeholder="Tell me about your project or idea..." required maxlength="2000" rows="6">{{ old('message') }}</textarea>
              <p class="form-hint"><span id="contact-char-count">{{ strlen(old('message', '')) }}</span> / 2000</p>
              @error('message')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <p class="form-error contact-form__error" id="contact-error" role="alert" hidden></p>
            <button type="submit" class="btn btn--primary btn--lg contact-form__submit" id="contact-submit">
              <span class="contact-form__submit-text">Send Message</span>
              <span class="contact-form__submit-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </span>
            </button>
          </form>

          <div class="contact-form__success" id="contact-success" style="display: none;" role="status" @if($success) hidden @endif>
            <div class="contact-form__success-icon" aria-hidden="true">✉️</div>
            <h2>Message Sent!</h2>
            <p>Thanks for reaching out. I'll get back to you as soon as possible.</p>
            <button type="button" class="btn btn--secondary" onclick="resetContactForm()">Send Another</button>
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
          <a href="{{ $contactGithubUrl }}" target="_blank" rel="noopener noreferrer" class="github-profile__card">
            <div class="github-profile__avatar" aria-hidden="true">
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
