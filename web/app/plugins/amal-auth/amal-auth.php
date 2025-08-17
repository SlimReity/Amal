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
        
        // Add shortcodes
        add_shortcode('amal_register_form', [$this, 'register_form_shortcode']);
        add_shortcode('amal_login_form', [$this, 'login_form_shortcode']);
        add_shortcode('amal_user_info', [$this, 'user_info_shortcode']);
        
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
     * Create database table for users
     */
    public function create_database_table()
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'amal_users';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            password_hash varchar(255) NOT NULL,
            first_name varchar(50) DEFAULT '',
            last_name varchar(50) DEFAULT '',
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
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
}

// Initialize the plugin
new AmalAuthPlugin();