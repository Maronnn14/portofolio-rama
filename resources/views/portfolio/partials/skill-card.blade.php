@php
  use App\Support\Icon;

  $icon = Icon::skill($skill->icon, $skill->name);
  $stagger = $stagger ?? 1;
  $categoryLabel = $categoryLabel ?? $skill->category;
@endphp

<article class="skill-card reveal stagger-{{ min($stagger, 8) }}"
  data-category="{{ $skill->category }}"
  data-skill-id="{{ $skill->id }}">
  <button type="button" class="skill-card__button" onclick="openSkillModal('{{ $skill->id }}')" aria-label="View details for {{ $skill->name }}">
    <span class="skill-card__header">
      <span class="skill-card__icon">
        @if($icon)
          <img src="{{ $icon }}" alt="" loading="lazy" />
        @else
          <span aria-hidden="true">{{ Icon::initials($skill->name) }}</span>
        @endif
      </span>
      <span>
        <span class="skill-card__name">{{ $skill->name }}</span>
        <span class="skill-card__category">{{ $categoryLabel }}</span>
      </span>
    </span>
    <span class="skill-card__teaser">{{ $skill->teaser }}</span>
    <span class="skill-card__meter" role="img" aria-label="Proficiency {{ (int) $skill->proficiency }} percent">
      <span style="width: {{ max(0, min(100, (int) $skill->proficiency)) }}%"></span>
    </span>
    <span class="skill-card__arrow" aria-hidden="true">→</span>
  </button>
</article>
