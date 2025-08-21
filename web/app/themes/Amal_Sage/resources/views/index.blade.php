@extends('layouts.app')

@section('content')
  @if (is_home() && !is_paged())
    {{-- Show landing page content on the homepage --}}
    @include('partials.landing-page')
  @else
    {{-- Show blog posts on subsequent pages --}}
    @include('partials.page-header')

    @if (! have_posts())
      <x-alert type="warning">
        {!! __('Sorry, no results were found.', 'sage') !!}
      </x-alert>

      {!! get_search_form(false) !!}
    @endif

    @while(have_posts()) @php(the_post())
      @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
    @endwhile

    {!! get_the_posts_navigation() !!}
  @endif
@endsection

@section('sidebar')
  @if (!is_home() || is_paged())
    @include('sections.sidebar')
  @endif
@endsection
