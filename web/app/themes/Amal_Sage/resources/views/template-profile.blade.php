{{--
  Template Name: Profile Management
  Description: User profile management page with pets, services, and bookings
--}}

@extends('layouts.app')

@section('content')
  <div class="profile-page">
    <div class="container">
      {{-- Page Header --}}
      <div class="page-header">
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Manage your account, pets, and services</p>
      </div>

      {{-- Check if user is logged in --}}
      @if(function_exists('amal_is_logged_in') && amal_is_logged_in())
        {{-- Profile Management Interface --}}
        <div class="profile-management-wrapper">
          {!! do_shortcode('[amal_profile_management]') !!}
        </div>
      @else
        {{-- Login/Register Section --}}
        <div class="auth-section">
          <div class="auth-tabs">
            <div class="tab-buttons">
              <button class="tab-btn active" data-tab="login">Login</button>
              <button class="tab-btn" data-tab="register">Register</button>
            </div>
            
            <div class="tab-content active" id="login">
              <h3>Login to Your Account</h3>
              {!! do_shortcode('[amal_login_form]') !!}
            </div>
            
            <div class="tab-content" id="register">
              <h3>Create New Account</h3>
              {!! do_shortcode('[amal_register_form]') !!}
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Additional styling for the page --}}
  <style>
    .profile-page {
      min-height: 70vh;
      padding: 2rem 0;
    }
    
    .page-header {
      text-align: center;
      margin-bottom: 3rem;
      padding: 2rem 0;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 8px;
    }
    
    .page-title {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    
    .page-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      margin: 0;
    }
    
    .auth-section {
      max-width: 500px;
      margin: 0 auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    
    .tab-buttons {
      display: flex;
      background: #f8f9fa;
    }
    
    .tab-buttons .tab-btn {
      flex: 1;
      padding: 1rem;
      border: none;
      background: none;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .tab-buttons .tab-btn.active {
      background: white;
      color: #007cba;
    }
    
    .auth-section .tab-content {
      display: none;
      padding: 2rem;
    }
    
    .auth-section .tab-content.active {
      display: block;
    }
    
    .auth-section h3 {
      margin-top: 0;
      margin-bottom: 1.5rem;
      text-align: center;
      color: #333;
    }
    
    @media (max-width: 768px) {
      .page-title {
        font-size: 2rem;
      }
      
      .page-header {
        margin-bottom: 2rem;
        padding: 1.5rem;
      }
      
      .auth-section .tab-content {
        padding: 1.5rem;
      }
    }
  </style>

  {{-- JavaScript for auth tabs --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabButtons = document.querySelectorAll('.auth-section .tab-btn');
      const tabContents = document.querySelectorAll('.auth-section .tab-content');
      
      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          const targetTab = this.getAttribute('data-tab');
          
          // Remove active class from all buttons and contents
          tabButtons.forEach(btn => btn.classList.remove('active'));
          tabContents.forEach(content => content.classList.remove('active'));
          
          // Add active class to clicked button and corresponding content
          this.classList.add('active');
          document.getElementById(targetTab).classList.add('active');
        });
      });
    });
  </script>
@endsection