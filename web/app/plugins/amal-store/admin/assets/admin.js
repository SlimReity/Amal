/**
 * Admin JavaScript for Amal Store Inventory Management
 */

jQuery(document).ready(function($) {
    'use strict';

    // Global admin object
    window.AmalStoreAdmin = {
        init: function() {
            this.bindEvents();
            this.initTooltips();
        },

        bindEvents: function() {
            // Confirm before leaving page with unsaved changes
            this.trackFormChanges();
            
            // Auto-save draft functionality (optional)
            this.initAutoSave();
            
            // Keyboard shortcuts
            this.initKeyboardShortcuts();
        },

        trackFormChanges: function() {
            let formChanged = false;
            const form = $('#item-form');
            
            if (form.length) {
                // Track changes
                form.on('change input', function() {
                    formChanged = true;
                });
                
                // Reset on successful save
                $(document).on('amal_item_saved', function() {
                    formChanged = false;
                });
                
                // Warn before leaving
                $(window).on('beforeunload', function() {
                    if (formChanged) {
                        return 'You have unsaved changes. Are you sure you want to leave?';
                    }
                });
            }
        },

        initAutoSave: function() {
            // Optional: Auto-save form data to localStorage
            const form = $('#item-form');
            if (form.length) {
                // Save to localStorage on change (debounced)
                let saveTimeout;
                form.on('change input', function() {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(function() {
                        AmalStoreAdmin.saveFormDraft();
                    }, 2000);
                });
                
                // Load draft on page load
                this.loadFormDraft();
            }
        },

        saveFormDraft: function() {
            const form = $('#item-form');
            if (form.length) {
                const formData = form.serializeArray();
                const draftData = {};
                
                $.each(formData, function(i, field) {
                    draftData[field.name] = field.value;
                });
                
                localStorage.setItem('amal_store_item_draft', JSON.stringify(draftData));
            }
        },

        loadFormDraft: function() {
            const draftData = localStorage.getItem('amal_store_item_draft');
            if (draftData) {
                try {
                    const data = JSON.parse(draftData);
                    const form = $('#item-form');
                    
                    // Only load draft for new items (not edit mode)
                    if (!data.item_id) {
                        $.each(data, function(name, value) {
                            const field = form.find('[name="' + name + '"]');
                            if (field.length) {
                                if (field.is(':checkbox')) {
                                    field.prop('checked', value === 'on');
                                } else {
                                    field.val(value);
                                }
                            }
                        });
                        
                        // Show draft notification
                        this.showDraftNotification();
                    }
                } catch (e) {
                    // Invalid draft data, remove it
                    localStorage.removeItem('amal_store_item_draft');
                }
            }
        },

        showDraftNotification: function() {
            const notification = $('<div class="alert alert-info draft-notification">')
                .html('📝 Draft data restored. <button type="button" class="btn btn-sm btn-secondary clear-draft">Clear Draft</button>')
                .prependTo('.amal-admin-main');
            
            notification.find('.clear-draft').on('click', function() {
                localStorage.removeItem('amal_store_item_draft');
                notification.fadeOut();
            });
        },

        initKeyboardShortcuts: function() {
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + S to save form
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    const form = $('#item-form');
                    if (form.length && form.is(':visible')) {
                        form.trigger('submit');
                    }
                }
                
                // Escape to cancel/go back
                if (e.key === 'Escape') {
                    const cancelBtn = $('.btn-secondary[href*="inventory"]');
                    if (cancelBtn.length) {
                        window.location.href = cancelBtn.attr('href');
                    }
                }
            });
        },

        initTooltips: function() {
            // Simple tooltip implementation
            $('[title]').each(function() {
                const $this = $(this);
                const title = $this.attr('title');
                
                $this.removeAttr('title').on('mouseenter', function() {
                    const tooltip = $('<div class="tooltip">')
                        .text(title)
                        .appendTo('body');
                    
                    const offset = $this.offset();
                    tooltip.css({
                        top: offset.top - tooltip.outerHeight() - 5,
                        left: offset.left + ($this.outerWidth() / 2) - (tooltip.outerWidth() / 2)
                    });
                }).on('mouseleave', function() {
                    $('.tooltip').remove();
                });
            });
        },

        // Utility functions
        showLoadingState: function(element, text) {
            text = text || 'Loading...';
            const $el = $(element);
            $el.data('original-text', $el.text())
               .text(text)
               .prop('disabled', true);
        },

        hideLoadingState: function(element) {
            const $el = $(element);
            const originalText = $el.data('original-text');
            if (originalText) {
                $el.text(originalText)
                   .prop('disabled', false)
                   .removeData('original-text');
            }
        },

        formatPrice: function(price) {
            return '$' + parseFloat(price).toFixed(2);
        },

        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = function() {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };

    // Initialize admin functionality
    AmalStoreAdmin.init();

    // Export for global use
    window.AmalStoreAdmin = AmalStoreAdmin;
});

// Additional utility functions (vanilla JS)
function amal_logout() {
    if (confirm('Are you sure you want to logout?')) {
        // Implement logout functionality
        // This would typically redirect to a logout endpoint
        window.location.href = '/logout/';
    }
}

// Form validation helpers
function validateRequired(value, fieldName) {
    if (!value || value.trim() === '') {
        throw new Error(fieldName + ' is required');
    }
}

function validateNumeric(value, fieldName, min = null, max = null) {
    const num = parseFloat(value);
    if (isNaN(num)) {
        throw new Error(fieldName + ' must be a valid number');
    }
    if (min !== null && num < min) {
        throw new Error(fieldName + ' must be at least ' + min);
    }
    if (max !== null && num > max) {
        throw new Error(fieldName + ' must be at most ' + max);
    }
    return num;
}

function validateUrl(value, fieldName) {
    if (value && !value.match(/^https?:\/\/.+\..+/)) {
        throw new Error(fieldName + ' must be a valid URL');
    }
}

// Image handling utilities
function previewImage(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function validateImageFile(file) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!allowedTypes.includes(file.type)) {
        throw new Error('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
    }
    
    if (file.size > maxSize) {
        throw new Error('Image file must be smaller than 5MB');
    }
}