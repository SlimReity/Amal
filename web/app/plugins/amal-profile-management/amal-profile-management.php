<?php
/**
 * Plugin Name: Amal Profile Management
 * Plugin URI: https://github.com/SlimReity/Amal
 * Description: Complete user profile, pet, and service management system for the Amal pet services platform. Provides a comprehensive interface for users to manage their profiles, pets, services, and bookings.
 * Version: 1.0.0
 * Author: Amal Development Team
 * Author URI: https://github.com/SlimReity
 * Text Domain: amal-profile-management
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Network: false
 * 
 * @package AmalProfileManagement
 * @version 1.0.0
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('AMAL_PROFILE_MANAGEMENT_VERSION')) {
    define('AMAL_PROFILE_MANAGEMENT_VERSION', '1.0.0');
}

if (!defined('AMAL_PROFILE_MANAGEMENT_PLUGIN_URL')) {
    define('AMAL_PROFILE_MANAGEMENT_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('AMAL_PROFILE_MANAGEMENT_PLUGIN_PATH')) {
    define('AMAL_PROFILE_MANAGEMENT_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

/**
 * Main Amal Profile Management Class
 * 
 * @since 1.0.0
 */
class AmalProfileManagement
{
    /**
     * Instance of this class
     * 
     * @since 1.0.0
     * @var AmalProfileManagement
     */
    private static $instance = null;

    /**
     * Plugin version
     * 
     * @since 1.0.0
     * @var string
     */
    public $version = AMAL_PROFILE_MANAGEMENT_VERSION;

    /**
     * Get instance of this class
     * 
     * @since 1.0.0
     * @return AmalProfileManagement
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    private function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     * 
     * @since 1.0.0
     */
    private function init_hooks()
    {
        // Plugin activation and deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // WordPress init
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_amal_update_profile', array($this, 'ajax_update_profile'));
        add_action('wp_ajax_amal_update_pet', array($this, 'ajax_update_pet'));
        add_action('wp_ajax_amal_update_service', array($this, 'ajax_update_service'));
        add_action('wp_ajax_amal_delete_pet', array($this, 'ajax_delete_pet'));
        add_action('wp_ajax_amal_delete_service', array($this, 'ajax_delete_service'));
        add_action('wp_ajax_amal_get_pets', array($this, 'ajax_get_pets'));
        add_action('wp_ajax_amal_get_services', array($this, 'ajax_get_services'));
        add_action('wp_ajax_amal_get_orders', array($this, 'ajax_get_orders'));
        add_action('wp_ajax_amal_get_order_details', array($this, 'ajax_get_order_details'));

        // Shortcodes
        add_shortcode('amal_profile_management', array($this, 'shortcode_profile_management'));

        // Filters for extensibility
        add_filter('amal_get_user_pets', array($this, 'get_demo_pets'), 10, 2);
        add_filter('amal_get_user_services', array($this, 'get_demo_services'), 10, 2);
        add_filter('amal_get_user_bookings', array($this, 'get_demo_bookings'), 10, 2);
        add_filter('amal_get_user_orders', array($this, 'get_user_orders'), 10, 2);
    }

    /**
     * Plugin activation
     * 
     * @since 1.0.0
     */
    public function activate()
    {
        // Create database tables if needed
        $this->create_tables();

        // Set default options
        add_option('amal_profile_management_version', $this->version);

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     * 
     * @since 1.0.0
     */
    public function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin
     * 
     * @since 1.0.0
     */
    public function init()
    {
        // Load text domain for translations
        load_plugin_textdomain('amal-profile-management', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Check for database updates
        $this->maybe_update_db();
    }

    /**
     * Enqueue scripts and styles
     * 
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        // Only load on pages that contain the shortcode or specific pages
        if ($this->should_load_assets()) {
            // Enqueue CSS
            wp_enqueue_style(
                'amal-profile-management',
                AMAL_PROFILE_MANAGEMENT_PLUGIN_URL . 'assets/profile-management.css',
                array(),
                $this->version,
                'all'
            );

            // Enqueue JavaScript
            wp_enqueue_script(
                'amal-profile-management',
                AMAL_PROFILE_MANAGEMENT_PLUGIN_URL . 'assets/profile-management.js',
                array('jquery'),
                $this->version,
                true
            );

            // Localize script for AJAX
            wp_localize_script('amal-profile-management', 'amalProfile', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('amal_profile_nonce'),
                'messages' => array(
                    'profileUpdated' => __('Profile updated successfully!', 'amal-profile-management'),
                    'petUpdated' => __('Pet updated successfully!', 'amal-profile-management'),
                    'serviceUpdated' => __('Service updated successfully!', 'amal-profile-management'),
                    'petDeleted' => __('Pet deleted successfully!', 'amal-profile-management'),
                    'serviceDeleted' => __('Service deleted successfully!', 'amal-profile-management'),
                    'confirmDelete' => __('Are you sure you want to delete this item?', 'amal-profile-management'),
                    'error' => __('An error occurred. Please try again.', 'amal-profile-management'),
                ),
                'editUrls' => array(
                    'pet' => admin_url('admin.php?page=amal-edit-pet'),
                    'service' => admin_url('admin.php?page=amal-edit-service'),
                ),
                'addUrls' => array(
                    'pet' => admin_url('admin.php?page=amal-add-pet'),
                    'service' => admin_url('admin.php?page=amal-add-service'),
                ),
            ));
        }
    }

    /**
     * Check if assets should be loaded
     * 
     * @since 1.0.0
     * @return bool
     */
    private function should_load_assets()
    {
        global $post;

        // Always load in admin
        if (is_admin()) {
            return true;
        }

        // Load if shortcode is present in post content
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'amal_profile_management')) {
            return true;
        }

        // Load on specific pages (can be filtered)
        $load_pages = apply_filters('amal_profile_management_load_pages', array('profile', 'dashboard', 'account'));
        
        if (is_page($load_pages)) {
            return true;
        }

        return false;
    }

    /**
     * Profile management shortcode
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function shortcode_profile_management($atts = array())
    {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return '<div class="amal-profile-error">' . 
                   __('You must be logged in to view your profile.', 'amal-profile-management') . 
                   ' <a href="' . esc_url(wp_login_url(get_permalink())) . '">' . 
                   __('Login here', 'amal-profile-management') . '</a></div>';
        }

        // Parse attributes
        $atts = shortcode_atts(array(
            'show_header' => 'true',
            'show_features' => 'true',
            'show_implementation' => 'true',
            'default_tab' => 'profile',
        ), $atts, 'amal_profile_management');

        // Start output buffering
        ob_start();

        // Include template
        $template_path = AMAL_PROFILE_MANAGEMENT_PLUGIN_PATH . 'templates/profile-management.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo '<div class="amal-profile-error">' . 
                 __('Template file not found.', 'amal-profile-management') . '</div>';
        }

        return ob_get_clean();
    }

    /**
     * AJAX handler for profile update
     * 
     * @since 1.0.0
     */
    public function ajax_update_profile()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        $user_id = get_current_user_id();

        // Sanitize and update user data
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $address = sanitize_textarea_field($_POST['address']);

        // Update user meta
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'address', $address);

        // Update email if changed
        if ($email !== get_userdata($user_id)->user_email) {
            wp_update_user(array(
                'ID' => $user_id,
                'user_email' => $email,
            ));
        }

        wp_send_json_success(array('message' => __('Profile updated successfully!', 'amal-profile-management')));
    }

    /**
     * AJAX handler for pet update
     * 
     * @since 1.0.0
     */
    public function ajax_update_pet()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        // This would integrate with your pet management system
        // For now, return success for demo purposes
        wp_send_json_success(array('message' => __('Pet updated successfully!', 'amal-profile-management')));
    }

    /**
     * AJAX handler for service update
     * 
     * @since 1.0.0
     */
    public function ajax_update_service()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        // This would integrate with your service management system
        // For now, return success for demo purposes
        wp_send_json_success(array('message' => __('Service updated successfully!', 'amal-profile-management')));
    }

    /**
     * AJAX handler for pet deletion
     * 
     * @since 1.0.0
     */
    public function ajax_delete_pet()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        // This would integrate with your pet management system
        // For now, return success for demo purposes
        wp_send_json_success(array('message' => __('Pet deleted successfully!', 'amal-profile-management')));
    }

    /**
     * AJAX handler for service deletion
     * 
     * @since 1.0.0
     */
    public function ajax_delete_service()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        // This would integrate with your service management system
        // For now, return success for demo purposes
        wp_send_json_success(array('message' => __('Service deleted successfully!', 'amal-profile-management')));
    }

    /**
     * AJAX handler for getting pets
     * 
     * @since 1.0.0
     */
    public function ajax_get_pets()
    {
        // Verify nonce
        if (!wp_verify_nonce($_GET['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        $user_id = get_current_user_id();
        $pets = apply_filters('amal_get_user_pets', array(), $user_id);

        ob_start();
        if (!empty($pets)) {
            foreach ($pets as $pet) {
                echo '<div class="amal-pet-card" data-id="' . esc_attr($pet['id']) . '">';
                echo '<h4 class="amal-card-title">' . esc_html($pet['name']) . '</h4>';
                echo '<p class="amal-card-info"><strong>' . __('Type:', 'amal-profile-management') . '</strong> ' . esc_html($pet['type']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Breed:', 'amal-profile-management') . '</strong> ' . esc_html($pet['breed']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Age:', 'amal-profile-management') . '</strong> ' . esc_html($pet['age']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Weight:', 'amal-profile-management') . '</strong> ' . esc_html($pet['weight']) . '</p>';
                echo '<button class="amal-btn amal-btn-edit" data-type="pet" data-id="' . esc_attr($pet['id']) . '">' . __('Edit', 'amal-profile-management') . '</button>';
                echo '<button class="amal-btn amal-btn-danger amal-btn-delete" data-type="pet" data-id="' . esc_attr($pet['id']) . '">' . __('Delete', 'amal-profile-management') . '</button>';
                echo '</div>';
            }
        }
        
        echo ob_get_clean();
        wp_die();
    }

    /**
     * AJAX handler for getting services
     * 
     * @since 1.0.0
     */
    public function ajax_get_services()
    {
        // Verify nonce
        if (!wp_verify_nonce($_GET['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        $user_id = get_current_user_id();
        $services = apply_filters('amal_get_user_services', array(), $user_id);

        ob_start();
        if (!empty($services)) {
            foreach ($services as $service) {
                echo '<div class="amal-service-card" data-id="' . esc_attr($service['id']) . '">';
                echo '<h4 class="amal-card-title">' . esc_html($service['name']) . '</h4>';
                echo '<p class="amal-card-info"><strong>' . __('Category:', 'amal-profile-management') . '</strong> ' . esc_html($service['category']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Price:', 'amal-profile-management') . '</strong> ' . esc_html($service['price']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Location:', 'amal-profile-management') . '</strong> ' . esc_html($service['location']) . '</p>';
                echo '<p class="amal-card-info"><strong>' . __('Status:', 'amal-profile-management') . '</strong> ' . esc_html($service['status']) . '</p>';
                echo '<button class="amal-btn amal-btn-edit" data-type="service" data-id="' . esc_attr($service['id']) . '">' . __('Edit', 'amal-profile-management') . '</button>';
                echo '<button class="amal-btn amal-btn-danger amal-btn-delete" data-type="service" data-id="' . esc_attr($service['id']) . '">' . __('Delete', 'amal-profile-management') . '</button>';
                echo '</div>';
            }
        }
        
        echo ob_get_clean();
        wp_die();
    }

    /**
     * Get demo pets data
     * 
     * @since 1.0.0
     * @param array $pets
     * @param int $user_id
     * @return array
     */
    public function get_demo_pets($pets, $user_id)
    {
        // Return demo data if no real pets exist
        if (empty($pets)) {
            return array(
                array(
                    'id' => 1,
                    'name' => 'Buddy',
                    'type' => 'Dog',
                    'breed' => 'Golden Retriever',
                    'age' => '3 years',
                    'weight' => '65 lbs',
                ),
                array(
                    'id' => 2,
                    'name' => 'Whiskers',
                    'type' => 'Cat',
                    'breed' => 'Persian',
                    'age' => '2 years',
                    'weight' => '8 lbs',
                ),
            );
        }
        return $pets;
    }

    /**
     * Get demo services data
     * 
     * @since 1.0.0
     * @param array $services
     * @param int $user_id
     * @return array
     */
    public function get_demo_services($services, $user_id)
    {
        // Return demo data if no real services exist
        if (empty($services)) {
            return array(
                array(
                    'id' => 1,
                    'name' => 'Professional Dog Walking',
                    'category' => 'Dog Walking',
                    'price' => '25.00 CHF/hour',
                    'location' => 'Downtown Area',
                    'status' => 'Active',
                ),
                array(
                    'id' => 2,
                    'name' => 'Pet Sitting Service',
                    'category' => 'Pet Sitting',
                    'price' => '40.00 CHF/day',
                    'location' => 'Your Home',
                    'status' => 'Active',
                ),
            );
        }
        return $services;
    }

    /**
     * Get demo bookings data
     * 
     * @since 1.0.0
     * @param array $bookings
     * @param int $user_id
     * @return array
     */
    public function get_demo_bookings($bookings, $user_id)
    {
        // Return demo data if no real bookings exist
        if (empty($bookings)) {
            return array(
                array(
                    'id' => 1,
                    'service_name' => 'Dog Walking Service',
                    'date' => 'August 20, 2024 at 3:00 PM',
                    'pet_name' => 'Buddy',
                    'amount' => '25.00 CHF',
                    'status' => 'Confirmed',
                ),
                array(
                    'id' => 2,
                    'service_name' => 'Grooming Service',
                    'date' => 'August 15, 2024 at 10:00 AM',
                    'pet_name' => 'Whiskers',
                    'amount' => '45.00 CHF',
                    'status' => 'Completed',
                ),
            );
        }
        return $bookings;
    }

    /**
     * Create database tables
     * 
     * @since 1.0.0
     */
    private function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Pets table
        $table_name = $wpdb->prefix . 'amal_pets';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            type varchar(50) NOT NULL,
            breed varchar(100) DEFAULT '',
            age varchar(20) DEFAULT '',
            weight varchar(20) DEFAULT '',
            notes text DEFAULT '',
            image_url varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        // Services table
        $table_name_services = $wpdb->prefix . 'amal_services';
        $sql_services = "CREATE TABLE $table_name_services (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(200) NOT NULL,
            category varchar(100) NOT NULL,
            price varchar(50) NOT NULL,
            location varchar(200) DEFAULT '',
            description text DEFAULT '',
            status enum('active', 'inactive') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($sql_services);
    }

    /**
     * Maybe update database
     * 
     * @since 1.0.0
     */
    private function maybe_update_db()
    {
        $installed_version = get_option('amal_profile_management_version');
        
        if ($installed_version !== $this->version) {
            $this->create_tables();
            update_option('amal_profile_management_version', $this->version);
        }
    }

    /**
     * Get user orders from store database
     * 
     * @since 1.0.0
     * @param array $orders
     * @param int $user_id
     * @return array
     */
    public function get_user_orders($orders, $user_id)
    {
        global $wpdb;
        
        // Get orders from amal-store plugin tables
        $orders_table = $wpdb->prefix . 'amal_orders';
        $order_items_table = $wpdb->prefix . 'amal_order_items';
        $items_table = $wpdb->prefix . 'amal_items';
        
        // Check if store plugin tables exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$orders_table'") != $orders_table) {
            // Return demo data if store plugin tables don't exist
            return $this->get_demo_orders();
        }
        
        $user_orders = $wpdb->get_results($wpdb->prepare("
            SELECT o.id, o.total_price, o.status, o.created_at, o.updated_at
            FROM $orders_table o
            WHERE o.user_id = %d
            ORDER BY o.created_at DESC
        ", $user_id), ARRAY_A);
        
        if (empty($user_orders)) {
            return $this->get_demo_orders();
        }
        
        return $user_orders;
    }

    /**
     * Get demo orders data
     * 
     * @since 1.0.0
     * @return array
     */
    private function get_demo_orders()
    {
        return array(
            array(
                'id' => 1,
                'total_price' => '89.99',
                'status' => 'delivered',
                'created_at' => '2024-08-15 10:30:00',
                'updated_at' => '2024-08-18 14:20:00',
            ),
            array(
                'id' => 2,
                'total_price' => '124.50',
                'status' => 'shipped',
                'created_at' => '2024-08-20 09:15:00',
                'updated_at' => '2024-08-21 16:45:00',
            ),
            array(
                'id' => 3,
                'total_price' => '45.99',
                'status' => 'processing',
                'created_at' => '2024-08-22 14:22:00',
                'updated_at' => '2024-08-22 14:22:00',
            ),
        );
    }

    /**
     * Get order details including line items
     * 
     * @since 1.0.0
     * @param int $order_id
     * @param int $user_id
     * @return array|false
     */
    public function get_order_details($order_id, $user_id)
    {
        global $wpdb;
        
        $orders_table = $wpdb->prefix . 'amal_orders';
        $order_items_table = $wpdb->prefix . 'amal_order_items';
        $items_table = $wpdb->prefix . 'amal_items';
        
        // Check if store plugin tables exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$orders_table'") != $orders_table) {
            // Return demo data if store plugin tables don't exist
            return $this->get_demo_order_details($order_id);
        }
        
        // Get order info
        $order = $wpdb->get_row($wpdb->prepare("
            SELECT o.id, o.total_price, o.status, o.created_at, o.updated_at
            FROM $orders_table o
            WHERE o.id = %d AND o.user_id = %d
        ", $order_id, $user_id), ARRAY_A);
        
        if (!$order) {
            return false;
        }
        
        // Get order items
        $order_items = $wpdb->get_results($wpdb->prepare("
            SELECT oi.quantity, oi.price, i.title, i.category, i.image_url
            FROM $order_items_table oi
            JOIN $items_table i ON oi.item_id = i.id
            WHERE oi.order_id = %d
        ", $order_id), ARRAY_A);
        
        $order['items'] = $order_items;
        
        return $order;
    }

    /**
     * Get demo order details
     * 
     * @since 1.0.0
     * @param int $order_id
     * @return array|false
     */
    private function get_demo_order_details($order_id)
    {
        $demo_orders = array(
            1 => array(
                'id' => 1,
                'total_price' => '89.99',
                'status' => 'delivered',
                'created_at' => '2024-08-15 10:30:00',
                'updated_at' => '2024-08-18 14:20:00',
                'items' => array(
                    array(
                        'title' => 'Premium Dog Food',
                        'category' => 'Food',
                        'quantity' => 2,
                        'price' => '45.00',
                        'image_url' => '',
                    ),
                ),
            ),
            2 => array(
                'id' => 2,
                'total_price' => '124.50',
                'status' => 'shipped',
                'created_at' => '2024-08-20 09:15:00',
                'updated_at' => '2024-08-21 16:45:00',
                'items' => array(
                    array(
                        'title' => 'Cat Litter Box',
                        'category' => 'Accessories',
                        'quantity' => 1,
                        'price' => '89.99',
                        'image_url' => '',
                    ),
                    array(
                        'title' => 'Dog Leash',
                        'category' => 'Accessories',
                        'quantity' => 1,
                        'price' => '24.50',
                        'image_url' => '',
                    ),
                ),
            ),
            3 => array(
                'id' => 3,
                'total_price' => '45.99',
                'status' => 'processing',
                'created_at' => '2024-08-22 14:22:00',
                'updated_at' => '2024-08-22 14:22:00',
                'items' => array(
                    array(
                        'title' => 'Premium Dog Food',
                        'category' => 'Food',
                        'quantity' => 1,
                        'price' => '45.99',
                        'image_url' => '',
                    ),
                ),
            ),
        );
        
        return isset($demo_orders[$order_id]) ? $demo_orders[$order_id] : false;
    }

    /**
     * AJAX handler for getting orders
     * 
     * @since 1.0.0
     */
    public function ajax_get_orders()
    {
        // Verify nonce
        if (!wp_verify_nonce($_GET['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        $user_id = get_current_user_id();
        $orders = apply_filters('amal_get_user_orders', array(), $user_id);

        ob_start();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $status_class = 'amal-status-' . strtolower($order['status']);
                $order_date = date('d.m.Y H:i', strtotime($order['created_at']));
                ?>
                <div class="amal-order-card" data-id="<?php echo esc_attr($order['id']); ?>">
                    <div class="amal-order-header">
                        <h4 class="amal-card-title"><?php echo sprintf(esc_html__('Order #%d', 'amal-profile-management'), $order['id']); ?></h4>
                        <span class="amal-order-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($order['status'])); ?></span>
                    </div>
                    <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php echo esc_html($order_date); ?></p>
                    <p class="amal-card-info"><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> <?php echo esc_html(number_format($order['total_price'], 2)); ?> CHF</p>
                    <button class="amal-btn amal-btn-view-order" data-order-id="<?php echo esc_attr($order['id']); ?>"><?php esc_html_e('View Details', 'amal-profile-management'); ?></button>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="amal-no-orders">
                <p><?php esc_html_e('No orders found.', 'amal-profile-management'); ?></p>
            </div>
            <?php
        }
        
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    }

    /**
     * AJAX handler for getting order details
     * 
     * @since 1.0.0
     */
    public function ajax_get_order_details()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_profile_nonce')) {
            wp_die('Security check failed');
        }

        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('You must be logged in.', 'amal-profile-management')));
        }

        $order_id = intval($_POST['order_id'] ?? 0);
        if (!$order_id) {
            wp_send_json_error(array('message' => __('Invalid order ID.', 'amal-profile-management')));
        }

        $user_id = get_current_user_id();
        $order = $this->get_order_details($order_id, $user_id);

        if (!$order) {
            wp_send_json_error(array('message' => __('Order not found.', 'amal-profile-management')));
        }

        ob_start();
        $status_class = 'amal-status-' . strtolower($order['status']);
        $order_date = date('d.m.Y H:i', strtotime($order['created_at']));
        ?>
        <div class="amal-order-details">
            <div class="amal-order-summary">
                <h4><?php echo sprintf(esc_html__('Order #%d Details', 'amal-profile-management'), $order['id']); ?></h4>
                <p><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php echo esc_html($order_date); ?></p>
                <p><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <span class="amal-order-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($order['status'])); ?></span></p>
                <p><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> <?php echo esc_html(number_format($order['total_price'], 2)); ?> CHF</p>
            </div>
            
            <div class="amal-order-items">
                <h5><?php esc_html_e('Order Items', 'amal-profile-management'); ?></h5>
                <?php if (!empty($order['items'])) : ?>
                    <div class="amal-items-list">
                        <?php foreach ($order['items'] as $item) : ?>
                            <div class="amal-item-row">
                                <div class="amal-item-info">
                                    <h6><?php echo esc_html($item['title']); ?></h6>
                                    <p class="amal-item-category"><?php echo esc_html($item['category']); ?></p>
                                </div>
                                <div class="amal-item-details">
                                    <p><strong><?php esc_html_e('Quantity:', 'amal-profile-management'); ?></strong> <?php echo esc_html($item['quantity']); ?></p>
                                    <p><strong><?php esc_html_e('Price:', 'amal-profile-management'); ?></strong> <?php echo esc_html(number_format($item['price'], 2)); ?> CHF</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p><?php esc_html_e('No items found for this order.', 'amal-profile-management'); ?></p>
                <?php endif; ?>
            </div>
            
            <button class="amal-btn amal-btn-back-to-orders"><?php esc_html_e('Back to Orders', 'amal-profile-management'); ?></button>
        </div>
        <?php
        
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    }
}

// Initialize the plugin
function amal_profile_management_init()
{
    return AmalProfileManagement::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'amal_profile_management_init');

// Helper functions for external use
if (!function_exists('amal_profile_management_shortcode')) {
    /**
     * Helper function to render the profile management interface
     * 
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string
     */
    function amal_profile_management_shortcode($atts = array())
    {
        $plugin = AmalProfileManagement::get_instance();
        return $plugin->shortcode_profile_management($atts);
    }
}