{{-- Landing Page Content --}}

<!-- Hero Section -->
<section class="hero-gradient text-white py-24 md:py-32 relative">
  <div class="floating-shapes"></div>
  <div class="container mx-auto px-6 text-center relative z-10">
    <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold mb-8 text-shadow leading-tight">
      Welcome to <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Amal</span>
    </h1>
    <p class="text-xl md:text-2xl lg:text-3xl mb-12 max-w-4xl mx-auto leading-relaxed text-shadow">
      The comprehensive service platform connecting pet owners with trusted service providers for all your pet care needs.
    </p>
    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
      <a href="#services" class="btn-primary text-gray-900 hover:text-gray-700">
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
          </svg>
          Explore Services
        </span>
      </a>
      <a href="#how-it-works" class="btn-secondary hover:bg-white hover:text-purple-600">
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
          </svg>
          How It Works
        </span>
      </a>
    </div>
  </div>
  
  <!-- Scroll indicator -->
  <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
    <div class="w-6 h-10 border-2 border-white rounded-full flex justify-center">
      <div class="w-1 h-3 bg-white rounded-full mt-2 animate-bounce"></div>
    </div>
  </div>
</section>

<!-- Services Section -->
<section id="services" class="py-24 bg-gradient-to-br from-gray-50 to-blue-50 relative">
  <div class="container mx-auto px-6">
    <div class="text-center mb-20 section-fade-in">
      <h2 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
        Pet Services You Can <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Trust</span>
      </h2>
      <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
        Find experienced and vetted service providers for all your pet care needs
      </p>
    </div>
    
    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-8">
      <div class="service-card group">
        <div class="service-icon" style="--icon-color-1: #3b82f6; --icon-color-2: #1d4ed8;">
          <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-900">Pet Sitting</h3>
        <p class="text-gray-600 text-lg leading-relaxed">Professional in-home pet care while you're away, ensuring your pets stay comfortable in familiar surroundings.</p>
      </div>
      
      <div class="service-card group">
        <div class="service-icon" style="--icon-color-1: #10b981; --icon-color-2: #047857;">
          <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-900">Dog Walking</h3>
        <p class="text-gray-600 text-lg leading-relaxed">Regular exercise and outdoor time for your furry friends with experienced and reliable walkers.</p>
      </div>
      
      <div class="service-card group">
        <div class="service-icon" style="--icon-color-1: #8b5cf6; --icon-color-2: #6d28d9;">
          <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-900">Grooming</h3>
        <p class="text-gray-600 text-lg leading-relaxed">Professional grooming to keep your pets looking their best with expert care and attention.</p>
      </div>
      
      <div class="service-card group">
        <div class="service-icon" style="--icon-color-1: #f59e0b; --icon-color-2: #d97706;">
          <svg class="w-8 h-8 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold mb-4 text-gray-900">Training</h3>
        <p class="text-gray-600 text-lg leading-relaxed">Expert training services for pets of all ages and breeds with certified professional trainers.</p>
      </div>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-24 bg-white relative overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 opacity-50"></div>
  <div class="container mx-auto px-6 relative z-10">
    <div class="text-center mb-20 section-fade-in">
      <h2 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
        How <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Amal</span> Works
      </h2>
      <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
        Simple steps to connect with the perfect pet care provider
      </p>
    </div>
    
    <div class="grid lg:grid-cols-3 gap-16 relative">
      <!-- Connection lines for desktop -->
      <div class="hidden lg:block absolute top-1/2 left-1/3 w-1/3 h-0.5 bg-gradient-to-r from-purple-400 to-blue-400 transform -translate-y-1/2"></div>
      <div class="hidden lg:block absolute top-1/2 right-1/3 w-1/3 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 transform -translate-y-1/2"></div>
      
      <div class="text-center relative">
        <div class="step-indicator">
          <span class="relative z-10">1</span>
        </div>
        <h3 class="text-3xl font-bold mb-6 text-gray-900">Browse Services</h3>
        <p class="text-gray-600 text-lg leading-relaxed max-w-sm mx-auto">
          Explore our wide range of pet care services and find providers in your area with detailed profiles and authentic reviews.
        </p>
      </div>
      
      <div class="text-center relative">
        <div class="step-indicator">
          <span class="relative z-10">2</span>
        </div>
        <h3 class="text-3xl font-bold mb-6 text-gray-900">Book & Connect</h3>
        <p class="text-gray-600 text-lg leading-relaxed max-w-sm mx-auto">
          Schedule services, communicate with providers, and coordinate all the details through our secure and intuitive platform.
        </p>
      </div>
      
      <div class="text-center relative">
        <div class="step-indicator">
          <span class="relative z-10">3</span>
        </div>
        <h3 class="text-3xl font-bold mb-6 text-gray-900">Enjoy Peace of Mind</h3>
        <p class="text-gray-600 text-lg leading-relaxed max-w-sm mx-auto">
          Relax knowing your pets are in caring hands. Rate your experience and build lasting relationships with trusted providers.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-24 bg-gradient-to-br from-gray-900 to-blue-900 text-white relative overflow-hidden">
  <div class="absolute inset-0 bg-black opacity-50"></div>
  <div class="absolute inset-0">
    <div class="w-full h-full bg-gradient-to-br from-purple-900/20 to-blue-900/20"></div>
  </div>
  
  <div class="container mx-auto px-6 relative z-10">
    <div class="text-center mb-20 section-fade-in">
      <h2 class="text-5xl md:text-6xl font-bold mb-6">
        Why Choose <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Amal?</span>
      </h2>
      <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
        We provide everything you need for seamless pet care coordination
      </p>
    </div>
    
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Secure Platform</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">Safe and secure booking with verified service providers and protected payments for complete peace of mind.</p>
      </div>
      
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Easy Communication</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">Built-in messaging system to coordinate details and stay connected with your pet's caregiver in real-time.</p>
      </div>
      
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Trusted Reviews</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">Read authentic reviews and ratings from other pet owners to make informed decisions about your pet's care.</p>
      </div>
      
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Mobile Friendly</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">Access all features on any device with our responsive design and mobile optimization for on-the-go management.</p>
      </div>
      
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Flexible Booking</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">Schedule one-time services or set up recurring appointments that fit your lifestyle and your pet's needs.</p>
      </div>
      
      <div class="feature-card bg-gray-800/50 backdrop-blur-sm border-gray-700">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-white">Quality Assurance</h3>
        </div>
        <p class="text-gray-300 text-lg leading-relaxed">All service providers are vetted and background-checked for your peace of mind and your pet's safety.</p>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-24 relative">
  <div class="floating-shapes"></div>
  <div class="container mx-auto px-6 text-center relative z-10">
    <h2 class="text-5xl md:text-6xl font-bold mb-8 text-shadow">
      Ready to Get <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">Started?</span>
    </h2>
    <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto leading-relaxed text-shadow">
      Join thousands of pet owners who trust Amal for their pet care needs. Your furry friends deserve the best!
    </p>
    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
      <a href="#register" class="btn-primary text-gray-900 hover:text-gray-700 group">
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          Find Pet Care
        </span>
      </a>
      <a href="#provider" class="btn-secondary group">
        <span class="flex items-center gap-2">
          <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Become a Provider
        </span>
      </a>
    </div>
    
    <!-- Trust indicators -->
    <div class="mt-16 flex flex-wrap justify-center items-center gap-8 opacity-80">
      <div class="flex items-center text-white/80">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <span class="text-lg font-semibold">4.9/5 Rating</span>
      </div>
      <div class="flex items-center text-white/80">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
          <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2v4c0 1.11-.89 2-2 2s-2-.89-2-2V4zM4 18c0 1.11.89 2 2 2h12c1.11 0 2-.89 2-2v-4H4v4zM2 12V6c0-1.11.89-2 2-2h12c1.11 0 2 .89 2 2v6H2z"/>
        </svg>
        <span class="text-lg font-semibold">10,000+ Happy Pets</span>
      </div>
      <div class="flex items-center text-white/80">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="text-lg font-semibold">Trusted & Verified</span>
      </div>
    </div>
  </div>
</section>