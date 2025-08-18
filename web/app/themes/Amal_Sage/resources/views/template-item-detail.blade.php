{{--
  Template Name: Item Detail
--}}

@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    
    <div class="container">
      <div class="item-detail-content">
        @if(function_exists('amal_store_init'))
          {!! do_shortcode('[amal_item_detail]') !!}
        @else
          <div class="alert alert-warning">
            <p>Amal Store plugin is not active. Please activate the plugin to view item details.</p>
          </div>
        @endif
      </div>
    </div>
  @endwhile
@endsection