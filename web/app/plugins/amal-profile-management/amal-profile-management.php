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

        // Shortcodes
        add_shortcode('amal_profile_management', array($this, 'shortcode_profile_management'));

        // Filters for extensibility
        add_filter('amal_get_user_pets', array($this, 'get_demo_pets'), 10, 2);
        add_filter('amal_get_user_services', array($this, 'get_demo_services'), 10, 2);
        add_filter('amal_get_user_bookings', array($this, 'get_demo_bookings'), 10, 2);
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
                    'price' => '$25/hour',
                    'location' => 'Downtown Area',
                    'status' => 'Active',
                ),
                array(
                    'id' => 2,
                    'name' => 'Pet Sitting Service',
                    'category' => 'Pet Sitting',
                    'price' => '$40/day',
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
                    'amount' => '$25.00',
                    'status' => 'Confirmed',
                ),
                array(
                    'id' => 2,
                    'service_name' => 'Grooming Service',
                    'date' => 'August 15, 2024 at 10:00 AM',
                    'pet_name' => 'Whiskers',
                    'amount' => '$45.00',
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