<?php
/**
 * Frontend functionality for Amal Store
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store_Frontend {
    
    private $wpdb;
    private $items_table;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->items_table = $wpdb->prefix . 'amal_items';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Register shortcodes
        add_shortcode('amal_storefront', array($this, 'render_storefront'));
        add_shortcode('amal_item_detail', array($this, 'render_item_detail'));
        add_shortcode('amal_cart', array($this, 'render_cart'));
        add_shortcode('amal_checkout', array($this, 'render_checkout'));
        add_shortcode('amal_order_confirmation', array($this, 'render_order_confirmation'));
        
        // Handle AJAX requests
        add_action('wp_ajax_amal_add_to_cart', array($this, 'handle_add_to_cart'));
        add_action('wp_ajax_nopriv_amal_add_to_cart', array($this, 'handle_add_to_cart'));
        add_action('wp_ajax_amal_process_checkout', array($this, 'handle_checkout'));
        add_action('wp_ajax_nopriv_amal_process_checkout', array($this, 'handle_checkout'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script('amal-store-frontend', AMAL_STORE_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), AMAL_STORE_VERSION, true);
        wp_enqueue_style('amal-store-frontend', AMAL_STORE_PLUGIN_URL . 'assets/css/frontend.css', array(), AMAL_STORE_VERSION);
        
        // Localize script for AJAX
        wp_localize_script('amal-store-frontend', 'amal_store_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amal_store_nonce')
        ));
    }
    
    /**
     * Get active items with pagination and filtering
     */
    public function get_items($args = array()) {
        $defaults = array(
            'page' => 1,
            'per_page' => 12,
            'category' => '',
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args($args, $defaults);
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        // Build WHERE clause
        $where = "WHERE is_active = 1";
        $params = array();
        
        if (!empty($args['category'])) {
            $where .= " AND category = %s";
            $params[] = $args['category'];
        }
        
        if (!empty($args['search'])) {
            $where .= " AND (title LIKE %s OR description LIKE %s)";
            $search_term = '%' . $this->wpdb->esc_like($args['search']) . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        // Build ORDER BY clause
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        if (!$orderby) {
            $orderby = 'created_at DESC';
        }
        
        // Get items
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->items_table} {$where} ORDER BY {$orderby} LIMIT %d OFFSET %d",
            array_merge($params, array($args['per_page'], $offset))
        );
        
        $items = $this->wpdb->get_results($sql);
        
        // Get total count for pagination
        $count_sql = $this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->items_table} {$where}",
            $params
        );
        
        $total_items = $this->wpdb->get_var($count_sql);
        
        return array(
            'items' => $items,
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $args['per_page']),
            'current_page' => $args['page']
        );
    }
    
    /**
     * Get item by ID
     */
    public function get_item($item_id) {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->items_table} WHERE id = %d AND is_active = 1",
            $item_id
        );
        
        return $this->wpdb->get_row($sql);
    }
    
    /**
     * Get all categories
     */
    public function get_categories() {
        $sql = "SELECT DISTINCT category FROM {$this->items_table} WHERE is_active = 1 ORDER BY category";
        return $this->wpdb->get_col($sql);
    }
    
    /**
     * Render storefront shortcode
     */
    public function render_storefront($atts) {
        $atts = shortcode_atts(array(
            'per_page' => 12,
            'show_filters' => 'yes',
            'show_search' => 'yes'
        ), $atts);
        
        // Get current page and filters from URL
        $current_page = isset($_GET['store_page']) ? max(1, intval($_GET['store_page'])) : 1;
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        
        // Get items
        $result = $this->get_items(array(
            'page' => $current_page,
            'per_page' => intval($atts['per_page']),
            'category' => $category,
            'search' => $search
        ));
        
        // Get categories for filter
        $categories = $this->get_categories();
        
        ob_start();
        include AMAL_STORE_PLUGIN_DIR . 'templates/storefront.php';
        return ob_get_clean();
    }
    
    /**
     * Render item detail shortcode
     */
    public function render_item_detail($atts) {
        $atts = shortcode_atts(array(
            'item_id' => 0
        ), $atts);
        
        // Try to get item ID from URL if not provided in shortcode
        if (empty($atts['item_id'])) {
            $atts['item_id'] = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
        }
        
        if (empty($atts['item_id'])) {
            return '<p>Item not found.</p>';
        }
        
        $item = $this->get_item($atts['item_id']);
        
        if (!$item) {
            return '<p>Item not found or no longer available.</p>';
        }
        
        ob_start();
        include AMAL_STORE_PLUGIN_DIR . 'templates/item-detail.php';
        return ob_get_clean();
    }
    
    /**
     * Handle add to cart AJAX request
     */
    public function handle_add_to_cart() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_nonce')) {
            wp_die('Security check failed');
        }
        
        $item_id = intval($_POST['item_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity < 1) {
            $quantity = 1;
        }
        
        // Get item to check stock
        $item = $this->get_item($item_id);
        
        if (!$item) {
            wp_send_json_error('Item not found');
            return;
        }
        
        if ($item->stock_qty < $quantity) {
            wp_send_json_error('Not enough stock available');
            return;
        }
        
        // For now, we'll just store in session/cookie
        // In a full implementation, this would integrate with WooCommerce or custom cart
        $cart = isset($_SESSION['amal_cart']) ? $_SESSION['amal_cart'] : array();
        
        if (!session_id()) {
            session_start();
        }
        
        $cart_key = 'item_' . $item_id;
        
        if (isset($cart[$cart_key])) {
            $cart[$cart_key]['quantity'] += $quantity;
        } else {
            $cart[$cart_key] = array(
                'item_id' => $item_id,
                'quantity' => $quantity,
                'price' => $item->price,
                'title' => $item->title
            );
        }
        
        $_SESSION['amal_cart'] = $cart;
        
        wp_send_json_success(array(
            'message' => 'Item added to cart successfully',
            'cart_count' => array_sum(array_column($cart, 'quantity'))
        ));
    }
    
    /**
     * Render cart shortcode
     */
    public function render_cart($atts) {
        $atts = shortcode_atts(array(
            'show_checkout_button' => 'yes'
        ), $atts);
        
        if (!session_id()) {
            session_start();
        }
        
        $cart = isset($_SESSION['amal_cart']) ? $_SESSION['amal_cart'] : array();
        
        if (empty($cart)) {
            return '<div class="amal-cart-empty"><p>Your cart is empty.</p></div>';
        }
        
        // Calculate total
        $total = 0;
        foreach ($cart as $cart_item) {
            $total += $cart_item['price'] * $cart_item['quantity'];
        }
        
        ob_start();
        include AMAL_STORE_PLUGIN_DIR . 'templates/cart.php';
        return ob_get_clean();
    }
    
    /**
     * Render checkout shortcode
     */
    public function render_checkout($atts) {
        $atts = shortcode_atts(array(), $atts);
        
        if (!session_id()) {
            session_start();
        }
        
        $cart = isset($_SESSION['amal_cart']) ? $_SESSION['amal_cart'] : array();
        
        if (empty($cart)) {
            return '<div class="amal-checkout-empty"><p>Your cart is empty. <a href="' . get_permalink() . '">Continue shopping</a></p></div>';
        }
        
        // Calculate total
        $total = 0;
        foreach ($cart as $cart_item) {
            $total += $cart_item['price'] * $cart_item['quantity'];
        }
        
        ob_start();
        include AMAL_STORE_PLUGIN_DIR . 'templates/checkout.php';
        return ob_get_clean();
    }
    
    /**
     * Render order confirmation shortcode
     */
    public function render_order_confirmation($atts) {
        $atts = shortcode_atts(array(
            'order_id' => ''
        ), $atts);
        
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : intval($atts['order_id']);
        
        if (!$order_id) {
            return '<div class="amal-order-error"><p>Order not found.</p></div>';
        }
        
        $order = $this->get_order($order_id);
        
        if (!$order) {
            return '<div class="amal-order-error"><p>Order not found.</p></div>';
        }
        
        $order_items = $this->get_order_items($order_id);
        
        ob_start();
        include AMAL_STORE_PLUGIN_DIR . 'templates/order-confirmation.php';
        return ob_get_clean();
    }
    
    /**
     * Handle checkout process
     */
    public function handle_checkout() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!session_id()) {
            session_start();
        }
        
        $cart = isset($_SESSION['amal_cart']) ? $_SESSION['amal_cart'] : array();
        
        if (empty($cart)) {
            wp_send_json_error('Cart is empty');
            return;
        }
        
        // Check if user is logged in (required for orders)
        if (!is_user_logged_in()) {
            wp_send_json_error('Please log in to place an order');
            return;
        }
        
        $user_id = get_current_user_id();
        
        // Validate stock availability
        foreach ($cart as $cart_item) {
            $item = $this->get_item($cart_item['item_id']);
            if (!$item) {
                wp_send_json_error('Item ' . $cart_item['title'] . ' is no longer available');
                return;
            }
            
            if ($item->stock_qty < $cart_item['quantity']) {
                wp_send_json_error('Not enough stock for ' . $cart_item['title'] . '. Available: ' . $item->stock_qty);
                return;
            }
        }
        
        // Calculate total
        $total = 0;
        foreach ($cart as $cart_item) {
            $total += $cart_item['price'] * $cart_item['quantity'];
        }
        
        // Create order
        $order_id = $this->create_order($user_id, $total, $cart);
        
        if (!$order_id) {
            wp_send_json_error('Failed to create order');
            return;
        }
        
        // Clear cart
        unset($_SESSION['amal_cart']);
        
        wp_send_json_success(array(
            'message' => 'Order placed successfully',
            'order_id' => $order_id
        ));
    }
    
    /**
     * Create order in database
     */
    private function create_order($user_id, $total, $cart) {
        // Start transaction
        $this->wpdb->query('START TRANSACTION');
        
        try {
            // Insert order
            $orders_table = $this->wpdb->prefix . 'amal_orders';
            $result = $this->wpdb->insert(
                $orders_table,
                array(
                    'user_id' => $user_id,
                    'total_price' => $total,
                    'status' => 'pending'
                ),
                array('%d', '%f', '%s')
            );
            
            if (!$result) {
                throw new Exception('Failed to create order');
            }
            
            $order_id = $this->wpdb->insert_id;
            
            // Insert order items and reduce stock
            $order_items_table = $this->wpdb->prefix . 'amal_order_items';
            
            foreach ($cart as $cart_item) {
                // Insert order item
                $result = $this->wpdb->insert(
                    $order_items_table,
                    array(
                        'order_id' => $order_id,
                        'item_id' => $cart_item['item_id'],
                        'quantity' => $cart_item['quantity'],
                        'price' => $cart_item['price']
                    ),
                    array('%d', '%d', '%d', '%f')
                );
                
                if (!$result) {
                    throw new Exception('Failed to create order item');
                }
                
                // Reduce stock using raw SQL
                $result = $this->wpdb->query($this->wpdb->prepare(
                    "UPDATE {$this->items_table} SET stock_qty = stock_qty - %d WHERE id = %d",
                    $cart_item['quantity'],
                    $cart_item['item_id']
                ));
                
                if ($result === false) {
                    throw new Exception('Failed to update stock');
                }
            }
            
            // Commit transaction
            $this->wpdb->query('COMMIT');
            
            return $order_id;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->wpdb->query('ROLLBACK');
            error_log('Order creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order by ID
     */
    private function get_order($order_id) {
        $orders_table = $this->wpdb->prefix . 'amal_orders';
        
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$orders_table} WHERE id = %d",
            $order_id
        ));
    }
    
    /**
     * Get order items
     */
    private function get_order_items($order_id) {
        $order_items_table = $this->wpdb->prefix . 'amal_order_items';
        
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT oi.*, i.title, i.image_url 
             FROM {$order_items_table} oi 
             JOIN {$this->items_table} i ON oi.item_id = i.id 
             WHERE oi.order_id = %d",
            $order_id
        ));
    }
}