/**
 * Frontend JavaScript for Amal Store
 */

(function($) {
    'use strict';

    // Initialize store functionality when document is ready
    $(document).ready(function() {
        AmalStore.init();
    });

    var AmalStore = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Handle add to cart button clicks
            $(document).on('click', '.amal-add-to-cart, .amal-add-to-cart-btn', this.handleAddToCart);
            
            // Handle quantity input validation
            $(document).on('change', '.amal-quantity-input', this.validateQuantity);
            
            // Handle filter form auto-submit on category change
            $(document).on('change', '.amal-category-select', this.autoSubmitFilters);
        },

        handleAddToCart: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var itemTitle = $button.data('item-title');
            var quantity = 1;
            
            // If this is from the detail page, get quantity from input
            var $quantityInput = $button.closest('.amal-cart-form').find('.amal-quantity-input');
            if ($quantityInput.length) {
                quantity = parseInt($quantityInput.val()) || 1;
            }
            
            // Validate inputs
            if (!itemId) {
                AmalStore.showFeedback('error', 'Invalid item selected.');
                return;
            }
            
            if (quantity < 1) {
                AmalStore.showFeedback('error', 'Please enter a valid quantity.');
                return;
            }
            
            // Disable button and show loading state
            $button.prop('disabled', true);
            var originalText = $button.text();
            $button.text('Adding...');
            
            // Make AJAX request
            $.ajax({
                url: amal_store_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'amal_add_to_cart',
                    item_id: itemId,
                    quantity: quantity,
                    nonce: amal_store_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AmalStore.showFeedback('success', response.data.message);
                        AmalStore.updateCartCount(response.data.cart_count);
                    } else {
                        AmalStore.showFeedback('error', response.data || 'Failed to add item to cart.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    AmalStore.showFeedback('error', 'An error occurred. Please try again.');
                },
                complete: function() {
                    // Re-enable button and restore text
                    $button.prop('disabled', false);
                    $button.text(originalText);
                }
            });
        },

        validateQuantity: function() {
            var $input = $(this);
            var value = parseInt($input.val());
            var min = parseInt($input.attr('min')) || 1;
            var max = parseInt($input.attr('max')) || 999;
            
            if (isNaN(value) || value < min) {
                $input.val(min);
            } else if (value > max) {
                $input.val(max);
            }
        },

        autoSubmitFilters: function() {
            var $form = $(this).closest('.amal-filter-form');
            if ($form.length) {
                $form.submit();
            }
        },

        showFeedback: function(type, message) {
            // Show feedback on item detail page
            var $feedback = $('.amal-cart-feedback');
            if ($feedback.length) {
                $feedback
                    .removeClass('success error')
                    .addClass(type)
                    .html('<p>' + message + '</p>')
                    .show();
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $feedback.fadeOut();
                }, 5000);
            } else {
                // Fallback: show alert for storefront page
                alert(message);
            }
        },

        updateCartCount: function(count) {
            // Update cart count in header/navigation if element exists
            var $cartCount = $('.amal-cart-count, .cart-count');
            if ($cartCount.length) {
                $cartCount.text(count);
            }
        }
    };

    // Expose AmalStore object globally for potential external use
    window.AmalStore = AmalStore;

})(jQuery);