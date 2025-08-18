<?php
/**
 * Template for cart display
 * Variables available: $cart, $total, $atts
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-cart">
    <h2>Shopping Cart</h2>
    
    <div class="amal-cart-items">
        <?php foreach ($cart as $cart_key => $cart_item): ?>
            <div class="amal-cart-item">
                <div class="amal-cart-item-details">
                    <h4><?php echo esc_html($cart_item['title']); ?></h4>
                    <div class="amal-cart-item-meta">
                        <span class="price">$<?php echo number_format($cart_item['price'], 2); ?></span>
                        <span class="quantity">Qty: <?php echo $cart_item['quantity']; ?></span>
                        <span class="subtotal">Subtotal: $<?php echo number_format($cart_item['price'] * $cart_item['quantity'], 2); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="amal-cart-summary">
        <div class="amal-cart-total">
            <strong>Total: $<?php echo number_format($total, 2); ?></strong>
        </div>
        
        <?php if ($atts['show_checkout_button'] === 'yes'): ?>
            <div class="amal-cart-actions">
                <a href="<?php echo esc_url(add_query_arg('page', 'checkout', get_permalink())); ?>" class="amal-checkout-btn">
                    Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>