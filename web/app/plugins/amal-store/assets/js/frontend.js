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
            
            // Cart management events
            $(document).on('click', '.amal-remove-item', this.handleRemoveItem);
            $(document).on('click', '.amal-qty-increase', this.handleQuantityIncrease);
            $(document).on('click', '.amal-qty-decrease', this.handleQuantityDecrease);
            $(document).on('change', '.amal-cart-quantity-input', this.handleQuantityChange);
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

        handleRemoveItem: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            // Disable button and show loading state
            $button.prop('disabled', true);
            var originalText = $button.text();
            $button.text('Removing...');
            
            // Make AJAX request
            $.ajax({
                url: amal_store_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'amal_remove_from_cart',
                    item_id: itemId,
                    nonce: amal_store_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AmalStore.showFeedback('success', response.data.message);
                        AmalStore.updateCartCount(response.data.cart_count);
                        AmalStore.updateCartDisplay(response.data);
                        
                        // Remove item from DOM
                        $button.closest('.amal-cart-item').fadeOut(300, function() {
                            $(this).remove();
                            AmalStore.checkEmptyCart();
                        });
                    } else {
                        AmalStore.showFeedback('error', response.data || 'Failed to remove item from cart.');
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

        handleQuantityIncrease: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $quantityInput = $button.siblings('.amal-cart-quantity-input');
            var currentQuantity = parseInt($quantityInput.val()) || 1;
            var maxQuantity = parseInt($quantityInput.attr('max')) || 999;
            
            if (currentQuantity < maxQuantity) {
                $quantityInput.val(currentQuantity + 1).trigger('change');
            }
        },

        handleQuantityDecrease: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $quantityInput = $button.siblings('.amal-cart-quantity-input');
            var currentQuantity = parseInt($quantityInput.val()) || 1;
            var minQuantity = parseInt($quantityInput.attr('min')) || 1;
            
            if (currentQuantity > minQuantity) {
                $quantityInput.val(currentQuantity - 1).trigger('change');
            }
        },

        handleQuantityChange: function(e) {
            var $input = $(this);
            var itemId = $input.data('item-id');
            var newQuantity = parseInt($input.val()) || 1;
            var minQuantity = parseInt($input.attr('min')) || 1;
            var maxQuantity = parseInt($input.attr('max')) || 999;
            
            // Validate quantity
            if (newQuantity < minQuantity) {
                newQuantity = minQuantity;
                $input.val(newQuantity);
            } else if (newQuantity > maxQuantity) {
                newQuantity = maxQuantity;
                $input.val(newQuantity);
            }
            
            // Update cart via AJAX
            AmalStore.updateCartItem(itemId, newQuantity);
        },

        updateCartItem: function(itemId, quantity) {
            // Make AJAX request to update cart
            $.ajax({
                url: amal_store_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'amal_update_cart',
                    item_id: itemId,
                    quantity: quantity,
                    nonce: amal_store_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        AmalStore.updateCartCount(response.data.cart_count);
                        AmalStore.updateCartDisplay(response.data);
                        
                        if (quantity === 0) {
                            // Remove item from DOM if quantity is 0
                            $('[data-item-id="' + itemId + '"]').closest('.amal-cart-item').fadeOut(300, function() {
                                $(this).remove();
                                AmalStore.checkEmptyCart();
                            });
                        }
                    } else {
                        AmalStore.showFeedback('error', response.data || 'Failed to update cart.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    AmalStore.showFeedback('error', 'An error occurred. Please try again.');
                }
            });
        },

        updateCartDisplay: function(data) {
            // Update cart totals if elements exist
            if (data.cart_total !== undefined) {
                $('.amal-cart-subtotal .amount').text('$' + parseFloat(data.cart_total).toFixed(2));
                $('.amal-cart-total .amount').text('$' + parseFloat(data.cart_total).toFixed(2));
            }
            
            // Update individual item totals
            $('.amal-cart-item').each(function() {
                var $item = $(this);
                var itemId = $item.data('item-id');
                var $quantityInput = $item.find('.amal-cart-quantity-input');
                var quantity = parseInt($quantityInput.val()) || 1;
                var priceText = $item.find('.amal-cart-item-price').text();
                var price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
                
                if (!isNaN(price)) {
                    var itemTotal = price * quantity;
                    $item.find('.amal-item-total').text('$' + itemTotal.toFixed(2));
                }
            });
        },

        checkEmptyCart: function() {
            // Check if cart is empty and show empty message
            if ($('.amal-cart-item').length === 0) {
                $('.amal-cart-items').hide();
                $('.amal-cart-summary').hide();
                $('.amal-cart-actions').hide();
                
                if ($('.amal-cart-empty').length === 0) {
                    $('.amal-cart-container').append(
                        '<div class="amal-cart-empty">' +
                        '<p>Your cart is empty.</p>' +
                        '<a href="#" class="amal-continue-shopping">Continue Shopping</a>' +
                        '</div>'
                    );
                }
            }
        },

        updateCartCount: function(count) {
            // Update cart count in header/navigation if element exists
            var $cartCount = $('.amal-cart-count, .cart-count');
            if ($cartCount.length) {
                $cartCount.text(count);
            }
            
            // Update cart header count if on cart page
            var $cartHeader = $('.amal-cart-header .amal-cart-count');
            if ($cartHeader.length) {
                $cartHeader.text(count + ' item' + (count != 1 ? 's' : ''));
            }
        }
    };

    // Expose AmalStore object globally for potential external use
    window.AmalStore = AmalStore;

})(jQuery);