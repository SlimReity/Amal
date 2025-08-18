<?php
/**
 * Plugin Name: Amal Authentication System
 * Plugin URI: https://github.com/SlimReity/Amal
 * Description: Secure user login and registration system for the Amal pet services platform
 * Version: 1.0.0
 * Author: Amal Team
 * Text Domain: amal-auth
 * Requires at least: 5.0
 * Requires PHP: 8.1
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AMAL_AUTH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMAL_AUTH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AMAL_AUTH_VERSION', '1.0.0');

// Include helper functions
require_once AMAL_AUTH_PLUGIN_PATH . 'includes/helper-functions.php';

/**
 * Main Amal Auth Plugin Class
 */
class AmalAuthPlugin
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_amal_register', [$this, 'handle_registration']);
        add_action('wp_ajax_nopriv_amal_register', [$this, 'handle_registration']);
        add_action('wp_ajax_amal_login', [$this, 'handle_login']);
        add_action('wp_ajax_nopriv_amal_login', [$this, 'handle_login']);
        add_action('wp_ajax_amal_logout', [$this, 'handle_logout']);
        
        // Profile management AJAX handlers
        add_action('wp_ajax_amal_update_profile', [$this, 'handle_update_profile']);
        add_action('wp_ajax_amal_add_pet', [$this, 'handle_add_pet']);
        add_action('wp_ajax_amal_update_pet', [$this, 'handle_update_pet']);
        add_action('wp_ajax_amal_delete_pet', [$this, 'handle_delete_pet']);
        add_action('wp_ajax_amal_add_service', [$this, 'handle_add_service']);
        add_action('wp_ajax_amal_update_service', [$this, 'handle_update_service']);
        add_action('wp_ajax_amal_delete_service', [$this, 'handle_delete_service']);
        add_action('wp_ajax_amal_upload_image', [$this, 'handle_image_upload']);
        
        // Add shortcodes
        add_shortcode('amal_register_form', [$this, 'register_form_shortcode']);
        add_shortcode('amal_login_form', [$this, 'login_form_shortcode']);
        add_shortcode('amal_user_info', [$this, 'user_info_shortcode']);
        add_shortcode('amal_profile_management', [$this, 'profile_management_shortcode']);
        
        // Create database table on plugin activation
        register_activation_hook(__FILE__, [$this, 'create_database_table']);
    }
    
    /**
     * Initialize plugin
     */
    public function init()
    {
        // Start session if not already started
        if (!session_id()) {
            session_start();
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('amal-auth-js', AMAL_AUTH_PLUGIN_URL . 'assets/amal-auth.js', ['jquery'], AMAL_AUTH_VERSION, true);
        wp_enqueue_style('amal-auth-css', AMAL_AUTH_PLUGIN_URL . 'assets/amal-auth.css', [], AMAL_AUTH_VERSION);
        
        // Localize script for AJAX
        wp_localize_script('amal-auth-js', 'amal_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amal_auth_nonce')
        ]);
    }
    
    /**
     * Create database tables for users and profile management
     */
    public function create_database_table()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create users table with additional profile fields
        $users_table = $wpdb->prefix . 'amal_users';
        $users_sql = "CREATE TABLE $users_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            password_hash varchar(255) NOT NULL,
            first_name varchar(50) DEFAULT '',
            last_name varchar(50) DEFAULT '',
            phone varchar(20) DEFAULT '',
            address text DEFAULT '',
            profile_picture varchar(255) DEFAULT '',
            notification_email tinyint(1) DEFAULT 1,
            notification_push tinyint(1) DEFAULT 1,
            notification_sms tinyint(1) DEFAULT 0,
            subscription_type enum('free', 'premium') DEFAULT 'free',
            user_type enum('pet_owner', 'service_provider') DEFAULT 'pet_owner',
            registration_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_login datetime DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            email_verified tinyint(1) DEFAULT 0,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        // Create pets table
        $pets_table = $wpdb->prefix . 'amal_pets';
        $pets_sql = "CREATE TABLE $pets_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            owner_id mediumint(9) NOT NULL,
            name varchar(100) NOT NULL,
            type varchar(50) NOT NULL,
            breed varchar(100) DEFAULT '',
            age int DEFAULT NULL,
            weight decimal(5,2) DEFAULT NULL,
            health_notes text DEFAULT '',
            photo_url varchar(255) DEFAULT '',
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY owner_id (owner_id)
        ) $charset_collate;";
        
        // Create services table
        $services_table = $wpdb->prefix . 'amal_services';
        $services_sql = "CREATE TABLE $services_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            provider_id mediumint(9) NOT NULL,
            title varchar(200) NOT NULL,
            category varchar(100) NOT NULL,
            description text DEFAULT '',
            price decimal(10,2) NOT NULL,
            availability text DEFAULT '',
            location varchar(255) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY provider_id (provider_id),
            KEY category (category),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        // Create bookings table
        $bookings_table = $wpdb->prefix . 'amal_bookings';
        $bookings_sql = "CREATE TABLE $bookings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            service_id mediumint(9) NOT NULL,
            pet_id mediumint(9) DEFAULT NULL,
            booking_date datetime NOT NULL,
            status enum('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            notes text DEFAULT '',
            total_amount decimal(10,2) NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY service_id (service_id),
            KEY pet_id (pet_id),
            KEY booking_date (booking_date),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($users_sql);
        dbDelta($pets_sql);
        dbDelta($services_sql);
        dbDelta($bookings_sql);
        
        // Generate SQL file for manual execution
        $this->generate_sql_file();
    }
    
    /**
     * Generate SQL file with CREATE TABLE statement
     */
    private function generate_sql_file()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'amal_users';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql_content = "-- Amal Authentication System Database Schema\n";
        $sql_content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
        $sql_content .= "CREATE TABLE IF NOT EXISTS $table_name (\n";
        $sql_content .= "    id mediumint(9) NOT NULL AUTO_INCREMENT,\n";
        $sql_content .= "    email varchar(100) NOT NULL,\n";
        $sql_content .= "    password_hash varchar(255) NOT NULL,\n";
        $sql_content .= "    first_name varchar(50) DEFAULT '',\n";
        $sql_content .= "    last_name varchar(50) DEFAULT '',\n";
        $sql_content .= "    user_type enum('pet_owner', 'service_provider') DEFAULT 'pet_owner',\n";
        $sql_content .= "    registration_date datetime DEFAULT CURRENT_TIMESTAMP,\n";
        $sql_content .= "    last_login datetime DEFAULT NULL,\n";
        $sql_content .= "    is_active tinyint(1) DEFAULT 1,\n";
        $sql_content .= "    email_verified tinyint(1) DEFAULT 0,\n";
        $sql_content .= "    created_at timestamp DEFAULT CURRENT_TIMESTAMP,\n";
        $sql_content .= "    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
        $sql_content .= "    PRIMARY KEY (id),\n";
        $sql_content .= "    UNIQUE KEY email (email)\n";
        $sql_content .= ") $charset_collate;\n\n";
        $sql_content .= "-- Sample INSERT statements will be added here when users register\n";
        
        file_put_contents(AMAL_AUTH_PLUGIN_PATH . 'users.sql', $sql_content);
    }
    
    /**
     * Validate email format
     */
    private function validate_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     */
    private function validate_password($password)
    {
        // Minimum 8 characters, at least one letter, one number, and one symbol
        if (strlen($password) < 8) {
            return false;
        }
        
        if (!preg_match('/[a-zA-Z]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if email already exists
     */
    private function email_exists($email)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'amal_users';
        
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE email = %s",
                $email
            )
        );
        
        return $result > 0;
    }
    
    /**
     * Handle user registration
     */
    public function handle_registration()
    {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        
        // Sanitize input data
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $user_type = sanitize_text_field($_POST['user_type'] ?? 'pet_owner');
        
        // Validate email
        if (!$this->validate_email($email)) {
            wp_send_json_error(['message' => 'Invalid email format']);
            return;
        }
        
        // Check for duplicate email
        if ($this->email_exists($email)) {
            wp_send_json_error(['message' => 'Email already exists']);
            return;
        }
        
        // Validate password
        if (!$this->validate_password($password)) {
            wp_send_json_error(['message' => 'Password must be at least 8 characters with letters, numbers, and symbols']);
            return;
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $table_name = $wpdb->prefix . 'amal_users';
        
        $result = $wpdb->insert(
            $table_name,
            [
                'email' => $email,
                'password_hash' => $password_hash,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'user_type' => $user_type,
                'registration_date' => current_time('mysql')
            ],
            [
                '%s', '%s', '%s', '%s', '%s', '%s'
            ]
        );
        
        if ($result === false) {
            wp_send_json_error(['message' => 'Registration failed']);
            return;
        }
        
        // Add INSERT statement to SQL file
        $this->add_user_to_sql_file($email, $password_hash, $first_name, $last_name, $user_type);
        
        wp_send_json_success(['message' => 'Registration successful']);
    }
    
    /**
     * Add user INSERT statement to SQL file
     */
    private function add_user_to_sql_file($email, $password_hash, $first_name, $last_name, $user_type)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'amal_users';
        $sql_file = AMAL_AUTH_PLUGIN_PATH . 'users.sql';
        
        $insert_statement = sprintf(
            "INSERT INTO %s (email, password_hash, first_name, last_name, user_type, registration_date) VALUES ('%s', '%s', '%s', '%s', '%s', '%s');\n",
            $table_name,
            esc_sql($email),
            esc_sql($password_hash),
            esc_sql($first_name),
            esc_sql($last_name),
            esc_sql($user_type),
            current_time('mysql')
        );
        
        file_put_contents($sql_file, $insert_statement, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Handle user login
     */
    public function handle_login()
    {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        global $wpdb;
        
        // Sanitize input data
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        
        // Validate email
        if (!$this->validate_email($email)) {
            wp_send_json_error(['message' => 'Invalid email format']);
            return;
        }
        
        // Get user from database
        $table_name = $wpdb->prefix . 'amal_users';
        
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE email = %s AND is_active = 1",
                $email
            )
        );
        
        if (!$user) {
            wp_send_json_error(['message' => 'Invalid email or password']);
            return;
        }
        
        // Verify password
        if (!password_verify($password, $user->password_hash)) {
            wp_send_json_error(['message' => 'Invalid email or password']);
            return;
        }
        
        // Update last login
        $wpdb->update(
            $table_name,
            ['last_login' => current_time('mysql')],
            ['id' => $user->id],
            ['%s'],
            ['%d']
        );
        
        // Set session data
        $_SESSION['amal_user_id'] = $user->id;
        $_SESSION['amal_user_email'] = $user->email;
        $_SESSION['amal_user_type'] = $user->user_type;
        
        wp_send_json_success([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type
            ]
        ]);
    }
    
    /**
     * Handle user logout
     */
    public function handle_logout()
    {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        // Clear session data
        unset($_SESSION['amal_user_id']);
        unset($_SESSION['amal_user_email']);
        unset($_SESSION['amal_user_type']);
        
        wp_send_json_success(['message' => 'Logout successful']);
    }
    
    /**
     * Registration form shortcode
     */
    public function register_form_shortcode($atts)
    {
        $atts = shortcode_atts([
            'redirect' => '',
            'class' => 'amal-register-form'
        ], $atts);
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="amal-register-form" method="post">
                <div class="form-group">
                    <label for="register-email">Email *</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="register-password">Password *</label>
                    <input type="password" id="register-password" name="password" required>
                    <small>Must be at least 8 characters with letters, numbers, and symbols</small>
                </div>
                
                <div class="form-group">
                    <label for="register-first-name">First Name</label>
                    <input type="text" id="register-first-name" name="first_name">
                </div>
                
                <div class="form-group">
                    <label for="register-last-name">Last Name</label>
                    <input type="text" id="register-last-name" name="last_name">
                </div>
                
                <div class="form-group">
                    <label for="register-user-type">I am a:</label>
                    <select id="register-user-type" name="user_type">
                        <option value="pet_owner">Pet Owner</option>
                        <option value="service_provider">Service Provider</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                
                <div id="register-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Login form shortcode
     */
    public function login_form_shortcode($atts)
    {
        $atts = shortcode_atts([
            'redirect' => '',
            'class' => 'amal-login-form'
        ], $atts);
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <form id="amal-login-form" method="post">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
                
                <div id="login-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * User info shortcode
     */
    public function user_info_shortcode($atts)
    {
        if (!AmalAuthHelper::is_logged_in()) {
            return '<p>Please log in to view your information.</p>';
        }
        
        $user = AmalAuthHelper::get_current_user();
        
        ob_start();
        ?>
        <div class="amal-user-info">
            <h3>Welcome, <?php echo esc_html($user->first_name ?: $user->email); ?>!</h3>
            <p><strong>Email:</strong> <?php echo esc_html($user->email); ?></p>
            <p><strong>Name:</strong> <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></p>
            <p><strong>User Type:</strong> <?php echo esc_html(ucwords(str_replace('_', ' ', $user->user_type))); ?></p>
            <p><strong>Member Since:</strong> <?php echo esc_html(date('F j, Y', strtotime($user->registration_date))); ?></p>
            
            <button id="amal-logout-btn">Logout</button>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // === PROFILE MANAGEMENT AJAX HANDLERS ===
    
    /**
     * Handle profile update
     */
    public function handle_update_profile()
    {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to update your profile']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $profile_data = [
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'address' => sanitize_textarea_field($_POST['address'] ?? ''),
            'notification_email' => (int)($_POST['notification_email'] ?? 1),
            'notification_push' => (int)($_POST['notification_push'] ?? 1),
            'notification_sms' => (int)($_POST['notification_sms'] ?? 0),
            'subscription_type' => sanitize_text_field($_POST['subscription_type'] ?? 'free'),
        ];
        
        $result = AmalAuthHelper::update_user_profile($user_id, $profile_data);
        
        if ($result) {
            wp_send_json_success(['message' => 'Profile updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update profile']);
        }
    }
    
    /**
     * Handle add pet
     */
    public function handle_add_pet()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to add a pet']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $pet_data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'type' => sanitize_text_field($_POST['type'] ?? ''),
            'breed' => sanitize_text_field($_POST['breed'] ?? ''),
            'age' => (int)($_POST['age'] ?? 0),
            'weight' => (float)($_POST['weight'] ?? 0),
            'health_notes' => sanitize_textarea_field($_POST['health_notes'] ?? ''),
            'photo_url' => sanitize_url($_POST['photo_url'] ?? ''),
        ];
        
        $pet_id = AmalAuthHelper::add_pet($user_id, $pet_data);
        
        if ($pet_id) {
            wp_send_json_success(['message' => 'Pet added successfully', 'pet_id' => $pet_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to add pet']);
        }
    }
    
    /**
     * Handle update pet
     */
    public function handle_update_pet()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to update pet']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $pet_id = (int)($_POST['pet_id'] ?? 0);
        $pet_data = [
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'type' => sanitize_text_field($_POST['type'] ?? ''),
            'breed' => sanitize_text_field($_POST['breed'] ?? ''),
            'age' => (int)($_POST['age'] ?? 0),
            'weight' => (float)($_POST['weight'] ?? 0),
            'health_notes' => sanitize_textarea_field($_POST['health_notes'] ?? ''),
            'photo_url' => sanitize_url($_POST['photo_url'] ?? ''),
        ];
        
        $result = AmalAuthHelper::update_pet($pet_id, $user_id, $pet_data);
        
        if ($result) {
            wp_send_json_success(['message' => 'Pet updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update pet']);
        }
    }
    
    /**
     * Handle delete pet
     */
    public function handle_delete_pet()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to delete pet']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $pet_id = (int)($_POST['pet_id'] ?? 0);
        
        $result = AmalAuthHelper::delete_pet($pet_id, $user_id);
        
        if ($result) {
            wp_send_json_success(['message' => 'Pet deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete pet']);
        }
    }
    
    /**
     * Handle add service
     */
    public function handle_add_service()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in() || !AmalAuthHelper::is_service_provider()) {
            wp_send_json_error(['message' => 'You must be a service provider to add services']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $service_data = [
            'title' => sanitize_text_field($_POST['title'] ?? ''),
            'category' => sanitize_text_field($_POST['category'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'availability' => sanitize_textarea_field($_POST['availability'] ?? ''),
            'location' => sanitize_text_field($_POST['location'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 1),
        ];
        
        $service_id = AmalAuthHelper::add_service($user_id, $service_data);
        
        if ($service_id) {
            wp_send_json_success(['message' => 'Service added successfully', 'service_id' => $service_id]);
        } else {
            wp_send_json_error(['message' => 'Failed to add service']);
        }
    }
    
    /**
     * Handle update service
     */
    public function handle_update_service()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in() || !AmalAuthHelper::is_service_provider()) {
            wp_send_json_error(['message' => 'You must be a service provider to update services']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $service_id = (int)($_POST['service_id'] ?? 0);
        $service_data = [
            'title' => sanitize_text_field($_POST['title'] ?? ''),
            'category' => sanitize_text_field($_POST['category'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'availability' => sanitize_textarea_field($_POST['availability'] ?? ''),
            'location' => sanitize_text_field($_POST['location'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 1),
        ];
        
        $result = AmalAuthHelper::update_service($service_id, $user_id, $service_data);
        
        if ($result) {
            wp_send_json_success(['message' => 'Service updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update service']);
        }
    }
    
    /**
     * Handle delete service
     */
    public function handle_delete_service()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in() || !AmalAuthHelper::is_service_provider()) {
            wp_send_json_error(['message' => 'You must be a service provider to delete services']);
            return;
        }
        
        $user_id = AmalAuthHelper::get_current_user_id();
        $service_id = (int)($_POST['service_id'] ?? 0);
        
        $result = AmalAuthHelper::delete_service($service_id, $user_id);
        
        if ($result) {
            wp_send_json_success(['message' => 'Service deleted successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete service']);
        }
    }
    
    /**
     * Handle image upload
     */
    public function handle_image_upload()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'amal_auth_nonce')) {
            wp_die('Security check failed');
        }
        
        if (!AmalAuthHelper::is_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to upload images']);
            return;
        }
        
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => 'No file uploaded or upload error']);
            return;
        }
        
        $upload_dir = wp_upload_dir();
        $amal_dir = $upload_dir['basedir'] . '/amal-images/';
        
        // Create directory if it doesn't exist
        if (!file_exists($amal_dir)) {
            wp_mkdir_p($amal_dir);
        }
        
        $file = $_FILES['image'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            wp_send_json_error(['message' => 'Invalid file type. Only JPG, PNG, and GIF allowed']);
            return;
        }
        
        $filename = uniqid() . '.' . $file_ext;
        $filepath = $amal_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $file_url = $upload_dir['baseurl'] . '/amal-images/' . $filename;
            wp_send_json_success(['message' => 'Image uploaded successfully', 'url' => $file_url]);
        } else {
            wp_send_json_error(['message' => 'Failed to upload image']);
        }
    }
    
    /**
     * Profile management shortcode
     */
    public function profile_management_shortcode($atts)
    {
        if (!AmalAuthHelper::is_logged_in()) {
            return '<p>Please log in to manage your profile. <a href="' . home_url('/login/') . '">Login here</a></p>';
        }
        
        $user = AmalAuthHelper::get_current_user();
        $pets = AmalAuthHelper::get_user_pets($user->id);
        $services = AmalAuthHelper::is_service_provider() ? AmalAuthHelper::get_user_services($user->id) : [];
        $bookings = AmalAuthHelper::get_user_bookings($user->id);
        
        ob_start();
        include AMAL_AUTH_PLUGIN_PATH . 'templates/profile-management.php';
        return ob_get_clean();
    }
}

// Initialize the plugin
new AmalAuthPlugin();