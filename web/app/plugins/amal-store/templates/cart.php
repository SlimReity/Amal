<?php
/**
 * Shopping Cart Template
 * Displays cart contents with add/remove/update functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

// Calculate cart totals
$cart_count = array_sum(array_column($cart, 'quantity'));
$cart_subtotal = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart));
?>

<div class="amal-cart-container">
    <div class="amal-cart-header">
        <h2>Shopping Cart</h2>
        <span class="amal-cart-count"><?php echo $cart_count; ?> item<?php echo $cart_count != 1 ? 's' : ''; ?></span>
    </div>

    <div class="amal-cart-feedback" style="display: none;"></div>

    <?php if (empty($cart)): ?>
        <div class="amal-cart-empty">
            <p>Your cart is empty.</p>
            <a href="#" class="amal-continue-shopping">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="amal-cart-items">
            <?php foreach ($cart as $cart_key => $item): ?>
                <div class="amal-cart-item" data-item-id="<?php echo $item['item_id']; ?>">
                    <div class="amal-cart-item-info">
                        <h4 class="amal-cart-item-title"><?php echo esc_html($item['title']); ?></h4>
                        <span class="amal-cart-item-price">$<?php echo number_format($item['price'], 2); ?> each</span>
                    </div>
                    
                    <div class="amal-cart-item-quantity">
                        <label for="quantity-<?php echo $item['item_id']; ?>">Quantity:</label>
                        <div class="amal-quantity-controls">
                            <button type="button" class="amal-qty-decrease" data-item-id="<?php echo $item['item_id']; ?>">-</button>
                            <input type="number" 
                                   id="quantity-<?php echo $item['item_id']; ?>" 
                                   class="amal-cart-quantity-input" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1" 
                                   max="999"
                                   data-item-id="<?php echo $item['item_id']; ?>">
                            <button type="button" class="amal-qty-increase" data-item-id="<?php echo $item['item_id']; ?>">+</button>
                        </div>
                    </div>
                    
                    <div class="amal-cart-item-total">
                        <span class="amal-item-total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    
                    <div class="amal-cart-item-actions">
                        <button type="button" class="amal-remove-item" data-item-id="<?php echo $item['item_id']; ?>">
                            Remove
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="amal-cart-summary">
            <div class="amal-cart-subtotal">
                <span class="label">Subtotal:</span>
                <span class="amount">$<?php echo number_format($cart_subtotal, 2); ?></span>
            </div>
            
            <div class="amal-cart-total">
                <span class="label">Total:</span>
                <span class="amount">$<?php echo number_format($cart_subtotal, 2); ?></span>
            </div>
        </div>

        <?php if ($atts['show_checkout'] === 'yes'): ?>
            <div class="amal-cart-actions">
                <a href="#" class="amal-continue-shopping">Continue Shopping</a>
                <a href="<?php echo esc_url(add_query_arg('page', 'checkout', get_permalink())); ?>" class="amal-checkout-btn">
                    Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>