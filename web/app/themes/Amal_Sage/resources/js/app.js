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
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
});
