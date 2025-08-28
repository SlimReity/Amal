{{--
  Template Name: Social Media
  Description: Social media feed page with post creation and interactions
--}}

@extends('layouts.app')

@section('content')
  <div class="social-page min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    {{-- Hero Section --}}
    <div class="hero-section bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
          Amal Community
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto opacity-90">
          Share your pet stories, connect with fellow pet lovers, and discover the latest in pet care
        </p>
      </div>
    </div>

    {{-- Main Content --}}
    <div class="social-container py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          
          @if(function_exists('amal_is_logged_in') && amal_is_logged_in())
            {{-- Post Creation Section --}}
            <div class="create-post-section bg-white rounded-lg shadow-lg p-6 mb-8">
              <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Share Something</h3>
                <p class="text-gray-600">What's on your mind today?</p>
              </div>
              
              <form id="amal-create-post-form" class="amal-create-post">
                <div class="mb-4">
                  <textarea 
                    id="amal-post-content" 
                    name="content" 
                    rows="4" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 resize-none"
                    placeholder="Share your thoughts, pet stories, or ask the community for advice..."
                    required
                  ></textarea>
                </div>
                
                <div class="flex justify-between items-center">
                  <div class="flex items-center text-sm text-gray-500">
                    <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 text-sm font-semibold">
                      @php
                        $current_user = function_exists('amal_current_user') ? amal_current_user() : null;
                        if ($current_user) {
                          echo esc_html(strtoupper(substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1)));
                        }
                      @endphp
                    </div>
                    <span>
                      Posting as: 
                      @if($current_user)
                        {{ esc_html($current_user->first_name . ' ' . $current_user->last_name) }}
                      @endif
                    </span>
                  </div>
                  
                  <button 
                    type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                  >
                    Share Post
                  </button>
                </div>
              </form>
            </div>
          @else
            {{-- Login Prompt --}}
            <div class="auth-prompt bg-white rounded-lg shadow-lg p-8 mb-8 text-center">
              <div class="mb-6">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Join the Conversation</h3>
                <p class="text-gray-600 mb-6">
                  Connect with fellow pet lovers, share your stories, and get advice from our amazing community.
                </p>
              </div>
              
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a 
                  href="{{ home_url('/auth/') }}" 
                  class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105"
                >
                  Sign In
                </a>
                <a 
                  href="{{ home_url('/auth/') }}" 
                  class="border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition-colors"
                >
                  Create Account
                </a>
              </div>
            </div>
          @endif

          {{-- Posts Feed Section --}}
          <div class="posts-feed-section">
            <div class="mb-6">
              <h3 class="text-2xl font-bold text-gray-900">Community Feed</h3>
              <p class="text-gray-600">Recent posts from our pet-loving community</p>
            </div>
            
            {{-- Posts will be loaded here --}}
            <div id="amal-social-feed" class="amal-social-feed">
              {{-- Loading spinner --}}
              <div class="loading-spinner text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-gray-600 mt-2">Loading posts...</p>
              </div>
            </div>
            
            {{-- Load More Button --}}
            <div class="text-center mt-8">
              <button 
                id="load-more-posts" 
                class="bg-white border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition-colors hidden"
              >
                Load More Posts
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Enhanced Styling for Social Elements --}}
  <style>
    /* Post Creation Form Enhancements */
    .amal-create-post textarea:focus {
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Social Feed Styling */
    .amal-social-feed .amal-post-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 24px;
      padding: 24px;
      transition: all 0.3s ease;
      border: 1px solid #f1f5f9;
    }
    
    .amal-social-feed .amal-post-card:hover {
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }
    
    /* User Avatar Styling */
    .amal-user-avatar {
      background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
      color: white;
      font-weight: 600;
    }
    
    /* Post Actions */
    .amal-post-actions .reaction-btn {
      transition: all 0.2s ease;
      border-radius: 8px;
      padding: 8px 16px;
    }
    
    .amal-post-actions .reaction-btn:hover {
      background-color: #f3f4f6;
      transform: scale(1.05);
    }
    
    .amal-post-actions .reaction-btn.active {
      background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
      color: white;
    }
    
    /* Login Prompt Enhancements */
    .amal-login-prompt {
      background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
      border: 2px solid #bfdbfe;
    }
    
    /* Message Styling */
    .amal-message {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-weight: 500;
    }
    
    .amal-message.success {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #6ee7b7;
    }
    
    .amal-message.error {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fca5a5;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .social-page .hero-section {
        padding: 8rem 0;
      }
      
      .social-container {
        padding: 2rem 0;
      }
      
      .amal-social-feed .amal-post-card {
        padding: 16px;
        margin-left: -12px;
        margin-right: -12px;
        border-radius: 0;
      }
      
      .create-post-section {
        margin-left: -12px;
        margin-right: -12px;
        border-radius: 0;
      }
    }
  </style>

  {{-- JavaScript for Social Media Functionality --}}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize social media functionality
      initializeSocialFeed();
      
      @if(function_exists('amal_is_logged_in') && amal_is_logged_in())
        initializePostCreation();
      @endif
    });
    
    function initializeSocialFeed() {
      // Load initial posts
      loadPosts();
      
      // Load more posts button
      const loadMoreBtn = document.getElementById('load-more-posts');
      if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
          const currentPosts = document.querySelectorAll('.amal-post-card').length;
          loadPosts(20, currentPosts);
        });
      }
    }
    
    @if(function_exists('amal_is_logged_in') && amal_is_logged_in())
    function initializePostCreation() {
      const form = document.getElementById('amal-create-post-form');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const content = document.getElementById('amal-post-content').value.trim();
          if (!content) {
            showMessage('Please enter some content for your post.', 'error');
            return;
          }
          
          // Submit post via AJAX
          createPost(content);
        });
      }
    }
    
    function createPost(content) {
      const submitBtn = document.querySelector('#amal-create-post-form button[type="submit"]');
      const originalText = submitBtn.textContent;
      
      submitBtn.textContent = 'Posting...';
      submitBtn.disabled = true;
      
      // Use existing AJAX functionality from social plugin
      if (typeof amal_social_ajax !== 'undefined') {
        jQuery.post(amal_social_ajax.ajaxurl, {
          action: 'amal_create_post',
          content: content,
          nonce: amal_social_ajax.nonce
        })
        .done(function(response) {
          if (response.success) {
            showMessage('Post created successfully!', 'success');
            document.getElementById('amal-post-content').value = '';
            loadPosts(); // Reload posts to show new post
          } else {
            showMessage(response.data?.message || 'Failed to create post', 'error');
          }
        })
        .fail(function() {
          showMessage('Network error. Please try again.', 'error');
        })
        .always(function() {
          submitBtn.textContent = originalText;
          submitBtn.disabled = false;
        });
      }
    }
    @endif
    
    function loadPosts(limit = 20, offset = 0) {
      const feedContainer = document.getElementById('amal-social-feed');
      const loadingSpinner = feedContainer.querySelector('.loading-spinner');
      
      if (offset === 0) {
        // Show loading for initial load
        if (loadingSpinner) loadingSpinner.style.display = 'block';
      }
      
      // Use existing AJAX functionality from social plugin
      if (typeof amal_social_ajax !== 'undefined') {
        jQuery.post(amal_social_ajax.ajaxurl, {
          action: 'amal_load_posts',
          limit: limit,
          offset: offset,
          nonce: amal_social_ajax.nonce
        })
        .done(function(response) {
          if (response.success) {
            if (offset === 0) {
              // Replace content for initial load
              feedContainer.innerHTML = response.data.html || '<div class="text-center py-12 text-gray-500">No posts yet. Be the first to share something!</div>';
            } else {
              // Append for load more
              feedContainer.innerHTML += response.data.html;
            }
            
            // Show/hide load more button
            const loadMoreBtn = document.getElementById('load-more-posts');
            if (loadMoreBtn) {
              if (response.data.html && response.data.html.trim()) {
                loadMoreBtn.classList.remove('hidden');
              } else {
                loadMoreBtn.classList.add('hidden');
              }
            }
            
            // Initialize reactions for new posts
            initializeReactions();
          } else {
            if (offset === 0) {
              feedContainer.innerHTML = '<div class="text-center py-12 text-red-500">Error loading posts. Please refresh the page.</div>';
            }
          }
        })
        .fail(function() {
          if (offset === 0) {
            feedContainer.innerHTML = '<div class="text-center py-12 text-red-500">Network error. Please refresh the page.</div>';
          }
        })
        .always(function() {
          if (loadingSpinner) loadingSpinner.style.display = 'none';
        });
      } else {
        // Fallback if AJAX not available
        feedContainer.innerHTML = '<div class="text-center py-12 text-gray-500">Social media functionality not available. Please ensure the social plugin is activated.</div>';
      }
    }
    
    function initializeReactions() {
      // Initialize reaction buttons (delegate to existing social.js functionality)
      if (typeof window.initializeReactionButtons === 'function') {
        window.initializeReactionButtons();
      }
    }
    
    function showMessage(message, type = 'info') {
      // Remove existing messages
      const existingMessages = document.querySelectorAll('.amal-message');
      existingMessages.forEach(msg => msg.remove());
      
      // Create new message
      const messageDiv = document.createElement('div');
      messageDiv.className = `amal-message ${type}`;
      messageDiv.textContent = message;
      
      // Insert message at top of form
      const form = document.getElementById('amal-create-post-form');
      if (form) {
        form.parentNode.insertBefore(messageDiv, form);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
          messageDiv.remove();
        }, 5000);
      }
    }
  </script>
@endsection