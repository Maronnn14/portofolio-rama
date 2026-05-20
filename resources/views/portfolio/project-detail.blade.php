@extends('layouts.portfolio')

@section('title', "Project Detail — Rama Adin")
@section('description', "Detailed view of a project by Rama Adin — description, tech stack, gallery, and links.")

@section('content')
<!-- Project Detail Content -->
  <main class="project-detail" id="project-detail">
    <!-- Rendered by JS -->
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/lightbox.js') }}"></script>
  <script src="{{ asset('js/project-detail.js') }}"></script>
@endpush
