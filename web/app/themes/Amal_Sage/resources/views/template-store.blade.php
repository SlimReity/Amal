{{--
  Template Name: Store
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    
    <div class="container">
      <div class="store-content">
        @php(the_content())
        
        @if(function_exists('amal_store_init'))
          {!! do_shortcode('[amal_storefront per_page="12" show_filters="yes" show_search="yes"]') !!}
        @else
          <div class="alert alert-warning">
            <p>Amal Store plugin is not active. Please activate the plugin to view the store.</p>
          </div>
        @endif
      </div>
    </div>
  @endwhile
@endsection