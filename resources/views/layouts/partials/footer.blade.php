@php
  use App\Models\PersonalInfo;
  use App\Support\Navigation;

  $fullName = PersonalInfo::displayName($personal);
  $tagline = $personal['tagline'] ?? '';
  $email = $personal['email'] ?? $siteSettings['contact_email'] ?? '';
  $location = $personal['location'] ?? '';
  $footerLinks = collect(Navigation::items())
      ->flatMap(fn ($item) => collect([$item])->merge($item['children']))
      ->values();
  $iconMap = [
    'github' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>',
    'linkedin' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.42v1.56h.05a3.75 3.75 0 013.37-1.85c3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 110-4.13 2.07 2.07 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>',
    'instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg>',
    'twitter' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-6.4L6.2 22H3l7.3-8.3L1 2h6.5l4.4 5.8L18.9 2z"/></svg>',
    'youtube' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 00.5 6.2 31 31 0 000 12a31 31 0 00.5 5.8 3 3 0 002.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 002.1-2.1A31 31 0 0024 12a31 31 0 00-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z"/></svg>',
  ];
@endphp
<footer class="footer">
  <div class="container">
    <div class="footer__inner">
      <div class="footer__brand">
        <div class="footer__brand-name">{{ PersonalInfo::initials($personal) }}<span>.</span></div>
        <p class="footer__brand-tagline">{{ $tagline }}</p>
        <div class="footer__socials mt-6">
          @foreach($socials as $s)
            <a href="{{ $s->url }}" class="footer__social-icon" target="_blank" rel="noopener noreferrer" aria-label="{{ $s->platform }}">{!! $iconMap[$s->icon] ?? e(mb_substr($s->platform, 0, 1)) !!}</a>
          @endforeach
        </div>
      </div>
      <div class="footer__nav">
        <h4 class="footer__heading">Navigation</h4>
        @foreach($footerLinks as $link)
          <a href="{{ $link['url'] }}" class="footer__link">{{ $link['label'] }}</a>
        @endforeach
      </div>
      <div class="footer__contact">
        <h4 class="footer__heading">Get in Touch</h4>
        @if($email)
          <a href="mailto:{{ $email }}" class="footer__link">{{ $email }}</a>
        @endif
        @if($location)<p class="footer__link">{{ $location }}</p>@endif
        @if(!empty($siteSettings['availability']))<p class="footer__link">{{ $siteSettings['availability'] }}</p>@endif
      </div>
    </div>
    <div class="footer__bottom">
      <p class="footer__copyright">{{ $siteSettings['copyright_text'] ?? '© '.date('Y').' '.$fullName.'. All rights reserved.' }}</p>
      <div class="footer__bottom-links"><span class="footer__bottom-link">{{ $siteSettings['footer_text'] ?? 'Built with passion & code' }}</span></div>
    </div>
  </div>
</footer>
