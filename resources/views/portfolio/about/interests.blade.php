@php
  use App\Support\Icon;

  $intro = $personal['interests_intro'] ?? null;
@endphp

@extends('layouts.portfolio')

@section('title', 'Interests & Hobbies')
@section('description', 'Interests, hobbies, and the activities that keep me curious outside of work.')

@section('content')
  <header class="page-header">
    <span class="section-label justify-center">Beyond Code</span>
    <h1 class="page-header__title reveal">Interests &amp; <span class="text-accent">Hobbies</span></h1>
    <p class="page-header__subtitle reveal stagger-1">What I do when I'm not building software</p>
  </header>

  @include('layouts.partials.about-nav')

  <section class="section--compact">
    <div class="container">
      @if($intro)
        <p class="interests-intro reveal">{{ $intro }}</p>
      @endif

      <div class="interests-grid interests-grid--large">
        @forelse($interests as $interest)
          <article class="interest-card interest-card--large reveal stagger-{{ min($loop->iteration, 6) }}">
            <div class="interest-card__icon-wrapper">
              @if($interest->icon && \Illuminate\Support\Str::startsWith($interest->icon, ['http://', 'https://', 'data:', '/']))
                <img class="interest-card__icon" src="{{ $interest->icon }}" alt="" loading="lazy" />
              @elseif($interest->icon)
                <span class="interest-card__icon interest-card__icon--emoji" aria-hidden="true">{{ $interest->icon }}</span>
              @else
                <span class="interest-card__icon" aria-hidden="true">{{ Icon::initials($interest->name) }}</span>
              @endif
            </div>
            <h2 class="interest-card__name">{{ $interest->name }}</h2>
            <p class="interest-card__desc">{{ $interest->description }}</p>
          </article>
        @empty
          <div class="empty-state col-span-full"><p class="empty-state__text">No interests listed yet.</p></div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header section-header--center reveal">
        <span class="section-label justify-center">Let's Connect</span>
        <h2 class="section-heading">Share an <span class="text-accent">Interest?</span></h2>
        <p class="section-subheading">Always happy to talk about common hobbies, side projects, or new ideas.</p>
      </div>
      <div class="text-center"><a href="{{ route('contact') }}" class="btn btn--primary">Get in Touch <span class="btn-arrow">→</span></a></div>
    </div>
  </section>
@endsection
