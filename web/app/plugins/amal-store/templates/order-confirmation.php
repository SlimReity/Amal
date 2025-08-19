<?php
/**
 * Template for order confirmation display
 * Variables available: $order, $order_items, $atts
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-order-confirmation">
    <div class="amal-order-success">
        <h2>✅ Order Confirmed!</h2>
        <p>Thank you for your order. Your order has been placed successfully.</p>
    </div>
    
    <div class="amal-order-details">
        <h3>Order Details</h3>
        
        <div class="amal-order-info">
            <p><strong>Order ID:</strong> #<?php echo $order->id; ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order->created_at)); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order->status); ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($order->total_price, 2); ?></p>
        </div>
        
        <div class="amal-order-items">
            <h4>Items Ordered:</h4>
            
            <?php foreach ($order_items as $item): ?>
                <div class="amal-order-item">
                    <div class="amal-order-item-details">
                        <?php if ($item->image_url): ?>
                            <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" class="amal-order-item-image">
                        <?php endif; ?>
                        
                        <div class="amal-order-item-info">
                            <h5><?php echo esc_html($item->title); ?></h5>
                            <div class="amal-order-item-meta">
                                <span class="quantity">Quantity: <?php echo $item->quantity; ?></span>
                                <span class="price">Price: $<?php echo number_format($item->price, 2); ?></span>
                                <span class="subtotal">Subtotal: $<?php echo number_format($item->price * $item->quantity, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="amal-order-actions">
        <a href="<?php echo get_permalink(); ?>" class="amal-continue-shopping">Continue Shopping</a>
    </div>
</div>