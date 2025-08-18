<?php
/**
 * Admin functionality for Amal Store
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store_Admin {
    
    private $wpdb;
    private $database;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->database = new Amal_Store_Database();
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Add admin menu
        add_action('wp_loaded', array($this, 'setup_admin_pages'));
        
        // Handle AJAX requests
        add_action('wp_ajax_amal_store_save_item', array($this, 'ajax_save_item'));
        add_action('wp_ajax_amal_store_delete_item', array($this, 'ajax_delete_item'));
        add_action('wp_ajax_amal_store_toggle_item_status', array($this, 'ajax_toggle_item_status'));
        add_action('wp_ajax_amal_store_update_order_status', array($this, 'ajax_update_order_status'));
        
        // Enqueue admin assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    public function setup_admin_pages() {
        // Check if we're in a custom admin context (not WordPress admin)
        if (is_admin()) {
            return;
        }
        
        // Handle admin inventory page requests
        $this->handle_admin_requests();
    }
    
    private function handle_admin_requests() {
        // Check if we're handling a custom admin route
        $request_uri = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($request_uri);
        $path = $parsed_url['path'];
        
        // Check for admin management routes
        if (strpos($path, '/admin/inventory') !== false || strpos($path, '/admin/orders') !== false) {
            $this->route_admin_request();
        }
    }
    
    private function route_admin_request() {
        // Include auth functions
        if (file_exists(AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php')) {
            require_once AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php';
        }
        
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Require admin access
        if (!function_exists('amal_require_admin')) {
            wp_die('Admin authentication system not available');
        }
        
        try {
            amal_require_admin();
        } catch (Exception $e) {
            wp_die('Access denied. Admin privileges required.');
        }
        
        $request_uri = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($request_uri);
        $path = $parsed_url['path'];
        
        if (strpos($path, '/admin/inventory/add') !== false) {
            $this->show_add_item_page();
        } elseif (strpos($path, '/admin/inventory/edit') !== false) {
            $this->show_edit_item_page();
        } elseif (strpos($path, '/admin/inventory') !== false) {
            $this->show_inventory_list_page();
        } elseif (strpos($path, '/admin/orders/view') !== false) {
            $this->show_order_detail_page();
        } elseif (strpos($path, '/admin/orders') !== false) {
            $this->show_orders_list_page();
        }
    }
    
    public function show_inventory_list_page() {
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $items_per_page = 20;
        $offset = ($page - 1) * $items_per_page;
        
        $items = $this->get_all_items($items_per_page, $offset, $search);
        $total_items = $this->get_items_count($search);
        
        include AMAL_STORE_PLUGIN_DIR . 'admin/pages/inventory-list.php';
        exit;
    }
    
    public function show_add_item_page() {
        $item = null;
        $is_edit = false;
        include AMAL_STORE_PLUGIN_DIR . 'admin/pages/item-form.php';
        exit;
    }
    
    public function show_edit_item_page() {
        $item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $item = $this->get_item($item_id);
        $is_edit = true;
        
        if (!$item) {
            wp_redirect(home_url('/admin/inventory/?error=item_not_found'));
            exit;
        }
        
        include AMAL_STORE_PLUGIN_DIR . 'admin/pages/item-form.php';
        exit;
    }
    
    public function get_all_items($limit = 20, $offset = 0, $search = '') {
        $table_name = $this->database->get_table_names()['items'];
        
        $where_clause = '';
        $params = array();
        
        if (!empty($search)) {
            $where_clause = 'WHERE title LIKE %s OR category LIKE %s OR description LIKE %s';
            $search_term = '%' . $this->wpdb->esc_like($search) . '%';
            $params = array($search_term, $search_term, $search_term);
        }
        
        $query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->wpdb->get_results(
            $this->wpdb->prepare($query, $params)
        );
    }
    
    public function get_item($id) {
        $table_name = $this->database->get_table_names()['items'];
        
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $id
            )
        );
    }
    
    public function get_items_count($search = '') {
        $table_name = $this->database->get_table_names()['items'];
        
        $where_clause = '';
        $params = array();
        
        if (!empty($search)) {
            $where_clause = 'WHERE title LIKE %s OR category LIKE %s OR description LIKE %s';
            $search_term = '%' . $this->wpdb->esc_like($search) . '%';
            $params = array($search_term, $search_term, $search_term);
        }
        
        $query = "SELECT COUNT(*) FROM $table_name $where_clause";
        
        if (!empty($params)) {
            return $this->wpdb->get_var($this->wpdb->prepare($query, $params));
        } else {
            return $this->wpdb->get_var($query);
        }
    }
    
    public function ajax_save_item() {
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Include auth functions and require admin access
        if (file_exists(AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php')) {
            require_once AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php';
        }
        
        if (!function_exists('amal_is_admin') || !amal_is_admin()) {
            wp_send_json_error(array('message' => 'Access denied. Admin privileges required.'));
        }
        
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $title = sanitize_text_field($_POST['title']);
        $category = sanitize_text_field($_POST['category']);
        $description = sanitize_textarea_field($_POST['description']);
        $price = floatval($_POST['price']);
        $stock_qty = intval($_POST['stock_qty']);
        $image_url = esc_url_raw($_POST['image_url']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate required fields
        $errors = array();
        if (empty($title)) $errors[] = 'Title is required';
        if (empty($category)) $errors[] = 'Category is required';
        if ($price < 0) $errors[] = 'Price must be a positive number';
        if ($stock_qty < 0) $errors[] = 'Stock quantity cannot be negative';
        
        if (!empty($errors)) {
            wp_send_json_error(array('message' => implode(', ', $errors)));
        }
        
        $data = array(
            'title' => $title,
            'category' => $category,
            'description' => $description,
            'price' => $price,
            'stock_qty' => $stock_qty,
            'image_url' => $image_url,
            'is_active' => $is_active,
            'updated_at' => current_time('mysql')
        );
        
        $table_name = $this->database->get_table_names()['items'];
        
        if ($item_id > 0) {
            // Update existing item
            $result = $this->wpdb->update(
                $table_name,
                $data,
                array('id' => $item_id),
                array('%s', '%s', '%s', '%f', '%d', '%s', '%d', '%s'),
                array('%d')
            );
        } else {
            // Create new item
            $data['created_at'] = current_time('mysql');
            $result = $this->wpdb->insert(
                $table_name,
                $data,
                array('%s', '%s', '%s', '%f', '%d', '%s', '%d', '%s', '%s')
            );
            $item_id = $this->wpdb->insert_id;
        }
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => 'Item saved successfully',
                'item_id' => $item_id
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to save item'));
        }
    }
    
    public function ajax_delete_item() {
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Include auth functions and require admin access
        if (file_exists(AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php')) {
            require_once AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php';
        }
        
        if (!function_exists('amal_is_admin') || !amal_is_admin()) {
            wp_send_json_error(array('message' => 'Access denied. Admin privileges required.'));
        }
        
        $item_id = intval($_POST['item_id']);
        
        if ($item_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid item ID'));
        }
        
        $table_name = $this->database->get_table_names()['items'];
        
        $result = $this->wpdb->delete(
            $table_name,
            array('id' => $item_id),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array('message' => 'Item deleted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete item'));
        }
    }
    
    public function ajax_toggle_item_status() {
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Include auth functions and require admin access
        if (file_exists(AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php')) {
            require_once AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php';
        }
        
        if (!function_exists('amal_is_admin') || !amal_is_admin()) {
            wp_send_json_error(array('message' => 'Access denied. Admin privileges required.'));
        }
        
        $item_id = intval($_POST['item_id']);
        $is_active = intval($_POST['is_active']);
        
        if ($item_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid item ID'));
        }
        
        $table_name = $this->database->get_table_names()['items'];
        
        $result = $this->wpdb->update(
            $table_name,
            array('is_active' => $is_active, 'updated_at' => current_time('mysql')),
            array('id' => $item_id),
            array('%d', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => 'Item status updated successfully',
                'is_active' => $is_active
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to update item status'));
        }
    }
    
    public function enqueue_admin_assets() {
        // Only enqueue on admin inventory or orders pages
        $request_uri = $_SERVER['REQUEST_URI'];
        if (strpos($request_uri, '/admin/inventory') !== false || strpos($request_uri, '/admin/orders') !== false) {
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'amal-store-admin',
                AMAL_STORE_PLUGIN_URL . 'admin/assets/admin.js',
                array('jquery'),
                AMAL_STORE_VERSION,
                true
            );
            
            wp_enqueue_style(
                'amal-store-admin',
                AMAL_STORE_PLUGIN_URL . 'admin/assets/admin.css',
                array(),
                AMAL_STORE_VERSION
            );
            
            // Localize script with AJAX URL and nonce
            wp_localize_script('amal-store-admin', 'amalStoreAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amal_store_admin_nonce'),
                'messages' => array(
                    'deleteConfirm' => 'Are you sure you want to delete this item?',
                    'saveSuccess' => 'Item saved successfully',
                    'deleteSuccess' => 'Item deleted successfully',
                    'statusUpdateSuccess' => 'Order status updated successfully',
                    'error' => 'An error occurred. Please try again.'
                )
            ));
        }
    }
    
    // Order Management Methods
    
    public function show_orders_list_page() {
        $status_filter = $_GET['status'] ?? '';
        $date_filter = $_GET['date'] ?? '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $orders_per_page = 20;
        $offset = ($page - 1) * $orders_per_page;
        
        $orders = $this->get_all_orders($orders_per_page, $offset, $status_filter, $date_filter);
        $total_orders = $this->get_orders_count($status_filter, $date_filter);
        
        include AMAL_STORE_PLUGIN_DIR . 'admin/pages/orders-list.php';
        exit;
    }
    
    public function show_order_detail_page() {
        $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $order = $this->get_order_with_details($order_id);
        
        if (!$order) {
            wp_redirect(home_url('/admin/orders/?error=order_not_found'));
            exit;
        }
        
        include AMAL_STORE_PLUGIN_DIR . 'admin/pages/order-detail.php';
        exit;
    }
    
    public function get_all_orders($limit = 20, $offset = 0, $status_filter = '', $date_filter = '') {
        $orders_table = $this->wpdb->prefix . 'amal_orders';
        $users_table = $this->wpdb->prefix . 'amal_users';
        
        $where_conditions = array();
        $params = array();
        
        if (!empty($status_filter)) {
            $where_conditions[] = "o.status = %s";
            $params[] = $status_filter;
        }
        
        if (!empty($date_filter)) {
            $where_conditions[] = "DATE(o.created_at) = %s";
            $params[] = $date_filter;
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        $query = "
            SELECT o.*, u.username, u.email, u.first_name, u.last_name
            FROM $orders_table o
            LEFT JOIN $users_table u ON o.user_id = u.id
            $where_clause
            ORDER BY o.created_at DESC
            LIMIT %d OFFSET %d
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        if (!empty($params)) {
            $query = $this->wpdb->prepare($query, $params);
        }
        
        return $this->wpdb->get_results($query);
    }
    
    public function get_orders_count($status_filter = '', $date_filter = '') {
        $orders_table = $this->wpdb->prefix . 'amal_orders';
        
        $where_conditions = array();
        $params = array();
        
        if (!empty($status_filter)) {
            $where_conditions[] = "status = %s";
            $params[] = $status_filter;
        }
        
        if (!empty($date_filter)) {
            $where_conditions[] = "DATE(created_at) = %s";
            $params[] = $date_filter;
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        $query = "SELECT COUNT(*) FROM $orders_table $where_clause";
        
        if (!empty($params)) {
            $query = $this->wpdb->prepare($query, $params);
        }
        
        return intval($this->wpdb->get_var($query));
    }
    
    public function get_order_with_details($order_id) {
        $orders_table = $this->wpdb->prefix . 'amal_orders';
        $users_table = $this->wpdb->prefix . 'amal_users';
        $order_items_table = $this->wpdb->prefix . 'amal_order_items';
        $items_table = $this->wpdb->prefix . 'amal_items';
        
        // Get order with user info
        $order_query = $this->wpdb->prepare("
            SELECT o.*, u.username, u.email, u.first_name, u.last_name
            FROM $orders_table o
            LEFT JOIN $users_table u ON o.user_id = u.id
            WHERE o.id = %d
        ", $order_id);
        
        $order = $this->wpdb->get_row($order_query);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $items_query = $this->wpdb->prepare("
            SELECT oi.*, i.title, i.category, i.image_url
            FROM $order_items_table oi
            LEFT JOIN $items_table i ON oi.item_id = i.id
            WHERE oi.order_id = %d
        ", $order_id);
        
        $order->items = $this->wpdb->get_results($items_query);
        
        return $order;
    }
    
    public function ajax_update_order_status() {
        // Start session if needed
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_store_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Include auth functions and require admin access
        if (file_exists(AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php')) {
            require_once AMAL_STORE_PLUGIN_DIR . '../amal-auth/includes/helper-functions.php';
        }
        
        if (!function_exists('amal_is_admin') || !amal_is_admin()) {
            wp_send_json_error(array('message' => 'Access denied. Admin privileges required.'));
        }
        
        $order_id = intval($_POST['order_id']);
        $new_status = sanitize_text_field($_POST['status']);
        
        if ($order_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid order ID'));
        }
        
        $valid_statuses = array('pending', 'processing', 'shipped', 'delivered', 'cancelled');
        if (!in_array($new_status, $valid_statuses)) {
            wp_send_json_error(array('message' => 'Invalid status'));
        }
        
        $orders_table = $this->wpdb->prefix . 'amal_orders';
        
        $result = $this->wpdb->update(
            $orders_table,
            array('status' => $new_status, 'updated_at' => current_time('mysql')),
            array('id' => $order_id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => 'Order status updated successfully',
                'status' => $new_status
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to update order status'));
        }
    }
}