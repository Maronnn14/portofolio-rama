@php
  // Tombol utama hero. CV diambil dari Profile → CV / Resume (PersonalInfo::cv_file).
  $heroCvFile = $personal['cv_file'] ?? null;
  $heroCvLabel = $personal['cv_label'] ?? 'CV';
  $heroCvFileName = \Illuminate\Support\Str::slug($homeName ?? 'cv').'-cv.pdf';
@endphp

<div class="hero__cta">
  <a href="{{ $homeSettings['hero_cta_primary_url'] ?? route('projects.index') }}" class="btn btn--primary btn--lg">
    {{ $homeSettings['hero_cta_primary_label'] ?? 'View Projects' }}
    <span class="btn-arrow">→</span>
  </a>
  <a href="{{ $homeSettings['hero_cta_secondary_url'] ?? route('contact') }}" class="btn btn--secondary btn--lg">
    {{ $homeSettings['hero_cta_secondary_label'] ?? 'Contact Me' }}
  </a>

  @if($heroCvFile)
    <a href="{{ $heroCvFile }}"
       class="btn btn--ghost-outline btn--lg hero__cta-cv"
       download="{{ $heroCvFileName }}"
       title="Download {{ $heroCvLabel }} (PDF)">
      <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M4 20h16"/>
      </svg>
      Download CV
    </a>
  @else
    <a href="{{ route('about.cv') }}" class="btn btn--ghost-outline btn--lg hero__cta-cv">
      <svg class="btn-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M4 20h16"/>
      </svg>
      Download CV
    </a>
  @endif
</div>
