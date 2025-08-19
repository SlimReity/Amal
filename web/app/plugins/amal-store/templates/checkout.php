<?php
/**
 * Template for checkout display
 * Variables available: $cart, $total, $atts
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-checkout">
    <h2>Checkout</h2>
    
    <!-- Order Review -->
    <div class="amal-checkout-review">
        <h3>Order Review</h3>
        
        <div class="amal-checkout-items">
            <?php foreach ($cart as $cart_key => $cart_item): ?>
                <div class="amal-checkout-item">
                    <span class="item-title"><?php echo esc_html($cart_item['title']); ?></span>
                    <span class="item-quantity">x<?php echo $cart_item['quantity']; ?></span>
                    <span class="item-price">$<?php echo number_format($cart_item['price'] * $cart_item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="amal-checkout-total">
            <strong>Total: $<?php echo number_format($total, 2); ?></strong>
        </div>
    </div>
    
    <!-- Checkout Form -->
    <div class="amal-checkout-form">
        <h3>Place Your Order</h3>
        
        <?php if (!is_user_logged_in()): ?>
            <div class="amal-checkout-login-notice">
                <p>Please <a href="<?php echo wp_login_url(get_permalink()); ?>">log in</a> to place your order.</p>
            </div>
        <?php else: ?>
            <form id="amal-checkout-form" method="post">
                <div class="amal-checkout-customer-info">
                    <h4>Customer Information</h4>
                    <p><strong>Logged in as:</strong> <?php echo esc_html(wp_get_current_user()->display_name); ?></p>
                </div>
                
                <div class="amal-checkout-actions">
                    <button type="submit" class="amal-place-order-btn">Place Order</button>
                </div>
                
                <?php wp_nonce_field('amal_store_nonce', 'nonce'); ?>
            </form>
            
            <div class="amal-checkout-feedback" style="display: none;"></div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#amal-checkout-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $button = $form.find('.amal-place-order-btn');
        var $feedback = $('.amal-checkout-feedback');
        var originalText = $button.text();
        
        // Disable button and show loading
        $button.prop('disabled', true).text('Processing...');
        $feedback.hide();
        
        $.ajax({
            url: amal_store_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'amal_process_checkout',
                nonce: $form.find('[name="nonce"]').val()
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to order confirmation
                    window.location.href = '<?php echo get_permalink(); ?>?page=order-confirmation&order_id=' + response.data.order_id;
                } else {
                    $feedback
                        .removeClass('success')
                        .addClass('error')
                        .html('<p>' + (response.data || 'Checkout failed. Please try again.') + '</p>')
                        .show();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $feedback
                    .removeClass('success')
                    .addClass('error')
                    .html('<p>An error occurred. Please try again.</p>')
                    .show();
            },
            complete: function() {
                // Re-enable button
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>