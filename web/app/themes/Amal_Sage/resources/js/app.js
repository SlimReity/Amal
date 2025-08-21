import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

/**
 * Mobile menu toggle functionality
 */
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');

  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', function() {
      const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
      
      // Toggle menu visibility
      mobileMenu.classList.toggle('hidden');
      
      // Update aria-expanded attribute
      mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
      
      // Update button icon
      const icon = mobileMenuToggle.querySelector('svg path');
      if (icon) {
        if (isExpanded) {
          // Show hamburger icon
          icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
        } else {
          // Show close icon
          icon.setAttribute('d', 'M6 18L18 6M6 6l12 12');
        }
      }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!mobileMenuToggle.contains(event.target) && !mobileMenu.contains(event.target)) {
        mobileMenu.classList.add('hidden');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        
        // Reset to hamburger icon
        const icon = mobileMenuToggle.querySelector('svg path');
        if (icon) {
          icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
        }
      }
    });

    // Close mobile menu on escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        mobileMenuToggle.focus(); // Return focus to toggle button
        
        // Reset to hamburger icon
        const icon = mobileMenuToggle.querySelector('svg path');
        if (icon) {
          icon.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
        }
      }
    });
  }

  /**
   * Smooth scrolling for anchor links
   */
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  
  anchorLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      
      // Skip if it's just a hash
      if (href === '#') return;
      
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        
        // Calculate offset for fixed header if exists
        const headerHeight = document.querySelector('.banner')?.offsetHeight || 0;
        const targetPosition = target.offsetTop - headerHeight - 20;
        
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });

  /**
   * Scroll animations for sections
   */
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('section-fade-in');
      }
    });
  }, observerOptions);

  // Observe service cards, step indicators, and feature cards
  const animatedElements = document.querySelectorAll('.service-card, .step-indicator, .feature-card');
  animatedElements.forEach(element => {
    observer.observe(element);
  });

  /**
   * Parallax effect for hero section
   */
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.hero-gradient');
    
    if (heroSection) {
      const rate = scrolled * -0.5;
      heroSection.style.transform = `translateY(${rate}px)`;
    }
  });

  /**
   * Add smooth hover effects to buttons
   */
  const buttons = document.querySelectorAll('.btn-primary, .btn-secondary');
  buttons.forEach(button => {
    button.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px) scale(1.05)';
    });
    
    button.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });

  /**
   * Dynamic gradient animation
   */
  const gradientElements = document.querySelectorAll('.hero-gradient, .cta-section');
  
  gradientElements.forEach(element => {
    let angle = 135;
    
    setInterval(() => {
      angle += 0.5;
      if (angle > 360) angle = 0;
      
      element.style.background = `linear-gradient(${angle}deg, #667eea 0%, #764ba2 100%)`;
    }, 100);
  });

  /**
   * Counter animation for trust indicators (if visible)
   */
  const trustIndicators = document.querySelectorAll('.trust-counter');
  const animateCounter = (element, target) => {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
      current += increment;
      element.textContent = Math.round(current);
      
      if (current >= target) {
        element.textContent = target;
        clearInterval(timer);
      }
    }, 20);
  };

  trustIndicators.forEach(counter => {
    const target = parseInt(counter.dataset.target);
    if (target) {
      observer.observe(counter);
      counter.addEventListener('section-fade-in', () => {
        animateCounter(counter, target);
      });
    }
  });
});
