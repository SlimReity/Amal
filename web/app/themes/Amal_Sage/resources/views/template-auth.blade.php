{{--
  Template Name: Authentication
  Description: User login and registration page with enhanced styling
--}}

@extends('layouts.app')

@section('content')
  <div class="auth-page min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    {{-- Hero Section --}}
    <div class="hero-section bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
          Join the Amal Community
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto opacity-90">
          Connect with trusted pet care providers or offer your services to loving pet owners
        </p>
      </div>
    </div>

    {{-- Authentication Section --}}
    <div class="auth-container py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
          {{-- Tab Navigation --}}
          <div class="auth-tabs bg-white rounded-t-lg shadow-lg overflow-hidden">
            <div class="tab-buttons flex">
              <button class="tab-btn flex-1 py-4 px-6 font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors border-r border-gray-200 active" 
                      data-tab="login">
                Sign In
              </button>
              <button class="tab-btn flex-1 py-4 px-6 font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors" 
                      data-tab="register">
                Create Account
              </button>
            </div>
          </div>

          {{-- Tab Content --}}
          <div class="auth-content bg-white rounded-b-lg shadow-lg">
            {{-- Login Tab --}}
            <div class="tab-content active p-8" id="login">
              <div class="mb-6 text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h3>
                <p class="text-gray-600">Sign in to access your account</p>
              </div>
              {!! do_shortcode('[amal_login_form class="amal-login-form-enhanced"]') !!}
              
              <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                  Don't have an account? 
                  <button class="text-blue-600 hover:text-blue-800 font-medium tab-switch" data-target="register">
                    Sign up here
                  </button>
                </p>
              </div>
            </div>

            {{-- Register Tab --}}
            <div class="tab-content p-8" id="register">
              <div class="mb-6 text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Create Your Account</h3>
                <p class="text-gray-600">Join our community of pet lovers</p>
              </div>
              {!! do_shortcode('[amal_register_form class="amal-register-form-enhanced"]') !!}
              
              <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                  Already have an account? 
                  <button class="text-blue-600 hover:text-blue-800 font-medium tab-switch" data-target="login">
                    Sign in here
                  </button>
                </p>
              </div>
            </div>
          </div>
        </div>

        {{-- Features Section --}}
        <div class="features-section mt-16">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Join Amal?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
              Whether you're a pet owner or service provider, Amal connects you with the right people
            </p>
          </div>
          
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Trusted Providers</h3>
              <p class="text-gray-600 text-sm">All service providers are vetted and reviewed by our community</p>
            </div>
            
            <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center">
              <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Fair Pricing</h3>
              <p class="text-gray-600 text-sm">Transparent pricing with no hidden fees or surprises</p>
            </div>
            
            <div class="feature-card bg-white p-6 rounded-lg shadow-md text-center">
              <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Peace of Mind</h3>
              <p class="text-gray-600 text-sm">Your pets are in safe hands with our caring community</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Enhanced Styling --}}
  <style>
    /* Tab functionality */
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    .tab-btn.active {
      background: white !important;
      color: #007cba !important;
      border-bottom: 3px solid #007cba;
    }
    
    /* Enhanced form styling to match homepage */
    .amal-login-form-enhanced,
    .amal-register-form-enhanced {
      max-width: none !important;
      margin: 0 !important;
      padding: 0 !important;
      background: transparent !important;
      border-radius: 0 !important;
      box-shadow: none !important;
    }
    
    .amal-login-form-enhanced .form-group,
    .amal-register-form-enhanced .form-group {
      margin-bottom: 24px;
    }
    
    .amal-login-form-enhanced .form-group label,
    .amal-register-form-enhanced .form-group label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      font-size: 14px;
    }
    
    .amal-login-form-enhanced .form-group input,
    .amal-login-form-enhanced .form-group select,
    .amal-register-form-enhanced .form-group input,
    .amal-register-form-enhanced .form-group select {
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.2s ease;
      background: #f9fafb;
    }
    
    .amal-login-form-enhanced .form-group input:focus,
    .amal-login-form-enhanced .form-group select:focus,
    .amal-register-form-enhanced .form-group input:focus,
    .amal-register-form-enhanced .form-group select:focus {
      outline: none;
      border-color: #2563eb;
      background: white;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .amal-login-form-enhanced .form-group button,
    .amal-register-form-enhanced .form-group button {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.2s ease;
      transform: translateY(0);
    }
    
    .amal-login-form-enhanced .form-group button:hover,
    .amal-register-form-enhanced .form-group button:hover {
      background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    /* Message styling */
    #login-message,
    #register-message {
      margin-top: 16px;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      text-align: center;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .auth-page .hero-section {
        padding: 8rem 0;
      }
      
      .auth-container {
        padding: 2rem 0;
      }
      
      .tab-content {
        padding: 1.5rem !important;
      }
      
      .features-section {
        margin-top: 3rem !important;
      }
      
      .features-section .grid {
        grid-template-columns: 1fr !important;
        gap: 1.5rem;
      }
    }
  </style>

  {{-- Tab switching JavaScript --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabButtons = document.querySelectorAll('.tab-btn');
      const tabContents = document.querySelectorAll('.tab-content');
      const tabSwitches = document.querySelectorAll('.tab-switch');

      function switchTab(targetTab) {
        // Remove active class from all tabs and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        // Add active class to target elements
        const targetButton = document.querySelector(`[data-tab="${targetTab}"]`);
        const targetContent = document.getElementById(targetTab);
        
        if (targetButton) targetButton.classList.add('active');
        if (targetContent) targetContent.classList.add('active');
      }

      // Tab button clicks
      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          const targetTab = this.getAttribute('data-tab');
          switchTab(targetTab);
        });
      });

      // Tab switch links
      tabSwitches.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const targetTab = this.getAttribute('data-target');
          switchTab(targetTab);
        });
      });
    });
  </script>
@endsection