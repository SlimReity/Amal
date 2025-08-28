{{--
  Template Name: Home Landing Page
--}}

@extends('layouts.app')

@section('content')
  <!-- Hero Section -->
  <section class="hero-section bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-4 text-center">
      <h1 class="text-5xl md:text-6xl font-bold mb-6">
        Welcome to Amal
      </h1>
      <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
        The comprehensive service platform connecting pet owners with trusted service providers for all your pet care needs.
      </p>
      <div class="flex flex-col md:flex-row gap-4 justify-center">
        @if(function_exists('amal_is_logged_in') && amal_is_logged_in())
          {{-- Logged in user actions --}}
          <a href="{{ home_url('/profile/') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            My Profile
          </a>
          <a href="{{ home_url('/social/') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
            Community
          </a>
          <a href="#services" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
            Explore Services
          </a>
        @else
          {{-- Guest user actions --}}
          <a href="{{ home_url('/auth/') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            Get Started
          </a>
          <a href="{{ home_url('/social/') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
            Community
          </a>
          <a href="#how-it-works" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
            How It Works
          </a>
        @endif
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Pet Services You Can Trust</h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Find experienced and vetted service providers for all your pet care needs
        </p>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition-shadow">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 001.5-1.5V7a1.5 1.5 0 00-1.5-1.5H9m0 0V4a2 2 0 012-2h2a2 2 0 012 2v1.5M9 10v6a2 2 0 002 2h2a2 2 0 002-2V10M9 10H7a2 2 0 00-2 2v6a2 2 0 002 2h2"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-3">Pet Sitting</h3>
          <p class="text-gray-600">Professional in-home pet care while you're away</p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition-shadow">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-3">Dog Walking</h3>
          <p class="text-gray-600">Regular exercise and outdoor time for your furry friends</p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition-shadow">
          <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-3">Grooming</h3>
          <p class="text-gray-600">Professional grooming to keep your pets looking their best</p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-lg text-center hover:shadow-xl transition-shadow">
          <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-3">Training</h3>
          <p class="text-gray-600">Expert training services for pets of all ages and breeds</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section id="how-it-works" class="py-20 bg-white">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">How Amal Works</h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Simple steps to connect with the perfect pet care provider
        </p>
      </div>
      
      <div class="grid md:grid-cols-3 gap-12">
        <div class="text-center">
          <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
            1
          </div>
          <h3 class="text-2xl font-semibold mb-4">Browse Services</h3>
          <p class="text-gray-600">
            Explore our wide range of pet care services and find providers in your area with detailed profiles and reviews.
          </p>
        </div>
        
        <div class="text-center">
          <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
            2
          </div>
          <h3 class="text-2xl font-semibold mb-4">Book & Connect</h3>
          <p class="text-gray-600">
            Schedule services, communicate with providers, and coordinate all the details through our secure platform.
          </p>
        </div>
        
        <div class="text-center">
          <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
            3
          </div>
          <h3 class="text-2xl font-semibold mb-4">Enjoy Peace of Mind</h3>
          <p class="text-gray-600">
            Relax knowing your pets are in caring hands. Rate your experience and build lasting relationships with trusted providers.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose Amal?</h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          We provide everything you need for seamless pet care coordination
        </p>
      </div>
      
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-blue-600">🔒 Secure Platform</h3>
          <p class="text-gray-600">Safe and secure booking with verified service providers and protected payments.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-green-600">💬 Easy Communication</h3>
          <p class="text-gray-600">Built-in messaging system to coordinate details and stay connected with your pet's caregiver.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-purple-600">⭐ Trusted Reviews</h3>
          <p class="text-gray-600">Read authentic reviews and ratings from other pet owners to make informed decisions.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-orange-600">📱 Mobile Friendly</h3>
          <p class="text-gray-600">Access all features on any device with our responsive design and mobile optimization.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-red-600">🎯 Flexible Booking</h3>
          <p class="text-gray-600">Schedule one-time services or set up recurring appointments that fit your lifestyle.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h3 class="text-xl font-semibold mb-3 text-indigo-600">🏆 Quality Assurance</h3>
          <p class="text-gray-600">All service providers are vetted and background-checked for your peace of mind.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="py-20 bg-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-4xl font-bold mb-6">Ready to Get Started?</h2>
      <p class="text-xl mb-8 max-w-2xl mx-auto">
        Join thousands of pet owners who trust Amal for their pet care needs. Your furry friends deserve the best!
      </p>
      <div class="flex flex-col md:flex-row gap-4 justify-center">
        <a href="#register" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
          Find Pet Care
        </a>
        <a href="#provider" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
          Become a Provider
        </a>
      </div>
    </div>
  </section>
@endsection