<?php
/**
 * Amal Authentication Helper Functions
 * 
 * This file contains utility functions for session management,
 * user authentication, and access control.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Amal Authentication Helper Class
 */
class AmalAuthHelper
{
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public static function is_logged_in()
    {
        return isset($_SESSION['amal_user_id']) && !empty($_SESSION['amal_user_id']);
    }
    
    /**
     * Get current logged-in user data
     * 
     * @return object|null
     */
    public static function get_current_user()
    {
        if (!self::is_logged_in()) {
            return null;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'amal_users';
        
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND is_active = 1",
                $_SESSION['amal_user_id']
            )
        );
        
        return $user;
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public static function get_current_user_id()
    {
        return self::is_logged_in() ? (int) $_SESSION['amal_user_id'] : null;
    }
    
    /**
     * Get current user email
     * 
     * @return string|null
     */
    public static function get_current_user_email()
    {
        return self::is_logged_in() ? $_SESSION['amal_user_email'] : null;
    }
    
    /**
     * Get current user type
     * 
     * @return string|null
     */
    public static function get_current_user_type()
    {
        return self::is_logged_in() ? $_SESSION['amal_user_type'] : null;
    }
    
    /**
     * Check if current user is a pet owner
     * 
     * @return bool
     */
    public static function is_pet_owner()
    {
        return self::is_logged_in() && $_SESSION['amal_user_type'] === 'pet_owner';
    }
    
    /**
     * Check if current user is a service provider
     * 
     * @return bool
     */
    public static function is_service_provider()
    {
        return self::is_logged_in() && $_SESSION['amal_user_type'] === 'service_provider';
    }
    
    /**
     * Require user to be logged in
     * Redirects to login page if not logged in
     * 
     * @param string $redirect_url URL to redirect to after login
     */
    public static function require_login($redirect_url = '')
    {
        if (!self::is_logged_in()) {
            if (empty($redirect_url)) {
                $redirect_url = home_url('/login/');
            }
            
            wp_redirect($redirect_url);
            exit;
        }
    }
    
    /**
     * Require user to be a specific type
     * 
     * @param string $user_type Required user type ('pet_owner' or 'service_provider')
     * @param string $redirect_url URL to redirect to if not authorized
     */
    public static function require_user_type($user_type, $redirect_url = '')
    {
        self::require_login();
        
        if (self::get_current_user_type() !== $user_type) {
            if (empty($redirect_url)) {
                $redirect_url = home_url('/unauthorized/');
            }
            
            wp_redirect($redirect_url);
            exit;
        }
    }
    
    /**
     * Logout current user
     */
    public static function logout()
    {
        unset($_SESSION['amal_user_id']);
        unset($_SESSION['amal_user_email']);
        unset($_SESSION['amal_user_type']);
        
        // Destroy session if no other session data exists
        if (empty($_SESSION)) {
            session_destroy();
        }
    }
    
    /**
     * Generate a secure random token
     * 
     * @param int $length Token length
     * @return string
     */
    public static function generate_token($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Validate password strength
     * 
     * @param string $password
     * @return array Array with 'valid' boolean and 'message' string
     */
    public static function validate_password_strength($password)
    {
        $result = ['valid' => true, 'message' => 'Password is strong'];
        
        if (strlen($password) < 8) {
            $result['valid'] = false;
            $result['message'] = 'Password must be at least 8 characters long';
            return $result;
        }
        
        if (!preg_match('/[a-zA-Z]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one letter';
            return $result;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one number';
            return $result;
        }
        
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $result['valid'] = false;
            $result['message'] = 'Password must contain at least one symbol';
            return $result;
        }
        
        return $result;
    }
    
    /**
     * Sanitize user input for database storage
     * 
     * @param mixed $input
     * @return mixed
     */
    public static function sanitize_input($input)
    {
        if (is_string($input)) {
            return sanitize_text_field($input);
        } elseif (is_email($input)) {
            return sanitize_email($input);
        } elseif (is_array($input)) {
            return array_map([self::class, 'sanitize_input'], $input);
        }
        
        return $input;
    }
    
    /**
     * Get user by email
     * 
     * @param string $email
     * @return object|null
     */
    public static function get_user_by_email($email)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amal_users';
        
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE email = %s AND is_active = 1",
                $email
            )
        );
        
        return $user;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $user_id
     * @return object|null
     */
    public static function get_user_by_id($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amal_users';
        
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND is_active = 1",
                $user_id
            )
        );
        
        return $user;
    }
    
    /**
     * Update user data
     * 
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public static function update_user($user_id, $data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amal_users';
        
        // Sanitize data
        $data = self::sanitize_input($data);
        
        // Add updated timestamp
        $data['updated_at'] = current_time('mysql');
        
        $result = $wpdb->update(
            $table_name,
            $data,
            ['id' => $user_id],
            null,
            ['%d']
        );
        
        return $result !== false;
    }
    
    /**
     * Deactivate user account
     * 
     * @param int $user_id
     * @return bool
     */
    public static function deactivate_user($user_id)
    {
        return self::update_user($user_id, ['is_active' => 0]);
    }
    
    /**
     * Activate user account
     * 
     * @param int $user_id
     * @return bool
     */
    public static function activate_user($user_id)
    {
        return self::update_user($user_id, ['is_active' => 1]);
    }
    
    /**
     * Get user count by type
     * 
     * @param string $user_type
     * @return int
     */
    public static function get_user_count_by_type($user_type = '')
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'amal_users';
        
        if (empty($user_type)) {
            $count = $wpdb->get_var(
                "SELECT COUNT(*) FROM $table_name WHERE is_active = 1"
            );
        } else {
            $count = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name WHERE user_type = %s AND is_active = 1",
                    $user_type
                )
            );
        }
        
        return (int) $count;
    }
}

/**
 * Convenience functions for easier access
 */

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function amal_is_logged_in()
{
    return AmalAuthHelper::is_logged_in();
}

/**
 * Get current user
 * 
 * @return object|null
 */
function amal_current_user()
{
    return AmalAuthHelper::get_current_user();
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function amal_current_user_id()
{
    return AmalAuthHelper::get_current_user_id();
}

/**
 * Check if current user is pet owner
 * 
 * @return bool
 */
function amal_is_pet_owner()
{
    return AmalAuthHelper::is_pet_owner();
}

/**
 * Check if current user is service provider
 * 
 * @return bool
 */
function amal_is_service_provider()
{
    return AmalAuthHelper::is_service_provider();
}

/**
 * Require login
 * 
 * @param string $redirect_url
 */
function amal_require_login($redirect_url = '')
{
    AmalAuthHelper::require_login($redirect_url);
}

/**
 * Logout current user
 */
function amal_logout()
{
    AmalAuthHelper::logout();
}