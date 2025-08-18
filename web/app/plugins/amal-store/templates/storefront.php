<?php
/**
 * Template for storefront display
 * Variables available: $result, $categories, $atts, $current_page, $category, $search
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-storefront">
    <?php if ($atts['show_search'] === 'yes' || $atts['show_filters'] === 'yes'): ?>
    <div class="amal-store-filters">
        <form method="get" class="amal-filter-form">
            <?php if ($atts['show_search'] === 'yes'): ?>
            <div class="amal-search-box">
                <input type="text" name="search" value="<?php echo esc_attr($search); ?>" placeholder="Search products..." class="amal-search-input">
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_filters'] === 'yes' && !empty($categories)): ?>
            <div class="amal-category-filter">
                <select name="category" class="amal-category-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo esc_attr($cat); ?>" <?php selected($category, $cat); ?>>
                            <?php echo esc_html($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <button type="submit" class="amal-filter-button">Filter</button>
            
            <?php if (!empty($category) || !empty($search)): ?>
                <a href="<?php echo esc_url(remove_query_arg(array('category', 'search', 'store_page'))); ?>" class="amal-clear-filters">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <?php if (!empty($result['items'])): ?>
        <div class="amal-items-grid">
            <?php foreach ($result['items'] as $item): ?>
                <div class="amal-item-card">
                    <?php if (!empty($item->image_url)): ?>
                        <div class="amal-item-image">
                            <img src="<?php echo esc_url($item->image_url); ?>" alt="<?php echo esc_attr($item->title); ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                    
                    <div class="amal-item-content">
                        <h3 class="amal-item-title">
                            <a href="<?php echo esc_url(add_query_arg('item_id', $item->id, get_permalink())); ?>">
                                <?php echo esc_html($item->title); ?>
                            </a>
                        </h3>
                        
                        <div class="amal-item-category">
                            <?php echo esc_html($item->category); ?>
                        </div>
                        
                        <div class="amal-item-price">
                            $<?php echo number_format($item->price, 2); ?>
                        </div>
                        
                        <?php if (!empty($item->description)): ?>
                            <div class="amal-item-excerpt">
                                <?php echo esc_html(wp_trim_words($item->description, 15)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="amal-item-stock">
                            <?php if ($item->stock_qty > 0): ?>
                                <span class="in-stock"><?php echo $item->stock_qty; ?> in stock</span>
                            <?php else: ?>
                                <span class="out-of-stock">Out of stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="amal-item-actions">
                            <a href="<?php echo esc_url(add_query_arg('item_id', $item->id, get_permalink())); ?>" class="amal-view-details">
                                View Details
                            </a>
                            
                            <?php if ($item->stock_qty > 0): ?>
                                <button type="button" class="amal-add-to-cart" data-item-id="<?php echo $item->id; ?>" data-item-title="<?php echo esc_attr($item->title); ?>">
                                    Add to Cart
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($result['total_pages'] > 1): ?>
            <div class="amal-pagination">
                <?php
                $base_url = remove_query_arg('store_page');
                
                // Previous page
                if ($result['current_page'] > 1):
                    $prev_url = add_query_arg('store_page', $result['current_page'] - 1, $base_url);
                ?>
                    <a href="<?php echo esc_url($prev_url); ?>" class="amal-page-link prev">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                // Page numbers
                $start_page = max(1, $result['current_page'] - 2);
                $end_page = min($result['total_pages'], $result['current_page'] + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                    if ($i == $result['current_page']):
                ?>
                        <span class="amal-page-link current"><?php echo $i; ?></span>
                <?php else:
                        $page_url = add_query_arg('store_page', $i, $base_url);
                ?>
                        <a href="<?php echo esc_url($page_url); ?>" class="amal-page-link"><?php echo $i; ?></a>
                <?php
                    endif;
                endfor;
                ?>

                <?php
                // Next page
                if ($result['current_page'] < $result['total_pages']):
                    $next_url = add_query_arg('store_page', $result['current_page'] + 1, $base_url);
                ?>
                    <a href="<?php echo esc_url($next_url); ?>" class="amal-page-link next">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="amal-no-items">
            <p>No items found.</p>
            <?php if (!empty($category) || !empty($search)): ?>
                <p><a href="<?php echo esc_url(remove_query_arg(array('category', 'search', 'store_page'))); ?>">View all products</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>