<?php
/**
 * Template for item detail display
 * Variables available: $item
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-item-detail">
    <div class="amal-item-gallery">
        <?php if (!empty($item->image_url)): ?>
            <div class="amal-item-main-image">
                <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>">
            </div>
        <?php else: ?>
            <div class="amal-item-no-image">
                <div class="placeholder">No Image Available</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="amal-item-info">
        <h1 class="amal-item-title"><?php echo esc_html($item->title); ?></h1>
        
        <div class="amal-item-meta">
            <div class="amal-item-category">
                <strong>Category:</strong> <?php echo esc_html($item->category); ?>
            </div>
            
            <div class="amal-item-price">
                <span class="price-label">Price:</span>
                <span class="price-amount">$<?php echo number_format($item->price, 2); ?></span>
            </div>
            
            <div class="amal-item-stock">
                <strong>Stock:</strong>
                <?php if ($item->stock_qty > 0): ?>
                    <span class="in-stock"><?php echo $item->stock_qty; ?> available</span>
                <?php else: ?>
                    <span class="out-of-stock">Out of stock</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($item->description)): ?>
            <div class="amal-item-description">
                <h3>Description</h3>
                <p><?php echo nl2br(esc_html($item->description)); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($item->stock_qty > 0): ?>
            <div class="amal-add-to-cart-form">
                <form class="amal-cart-form">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $item->stock_qty; ?>" class="amal-quantity-input">
                    </div>
                    
                    <button type="button" class="amal-add-to-cart-btn" data-item-id="<?php echo $item->id; ?>" data-item-title="<?php echo esc_attr($item->title); ?>">
                        Add to Cart
                    </button>
                </form>
                
                <div class="amal-cart-feedback" style="display: none;"></div>
            </div>
        <?php else: ?>
            <div class="amal-out-of-stock-notice">
                <p><strong>This item is currently out of stock.</strong></p>
            </div>
        <?php endif; ?>

        <div class="amal-item-details">
            <h3>Product Details</h3>
            <table class="amal-product-details-table">
                <tr>
                    <td><strong>Product ID:</strong></td>
                    <td><?php echo $item->id; ?></td>
                </tr>
                <tr>
                    <td><strong>Category:</strong></td>
                    <td><?php echo esc_html($item->category); ?></td>
                </tr>
                <tr>
                    <td><strong>Price:</strong></td>
                    <td>$<?php echo number_format($item->price, 2); ?></td>
                </tr>
                <tr>
                    <td><strong>Availability:</strong></td>
                    <td>
                        <?php if ($item->stock_qty > 0): ?>
                            <span class="in-stock">In Stock (<?php echo $item->stock_qty; ?> available)</span>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Added:</strong></td>
                    <td><?php echo date('F j, Y', strtotime($item->created_at)); ?></td>
                </tr>
            </table>
        </div>

        <div class="amal-item-actions">
            <a href="javascript:history.back()" class="amal-back-button">← Back to Store</a>
        </div>
    </div>
</div>