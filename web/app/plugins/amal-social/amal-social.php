<?php
/**
 * Plugin Name: Amal Social Media
 * Plugin URI: https://github.com/SlimReity/Amal
 * Description: Social media functionality for the Amal pet services platform
 * Version: 1.0.0
 * Author: Amal Team
 * License: MIT
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Amal Social Media Plugin Class
 */
class AmalSocialPlugin
{
    private $table_posts;
    private $table_reactions;
    
    public function __construct()
    {
        global $wpdb;
        
        $this->table_posts = $wpdb->prefix . 'amal_social_posts';
        $this->table_reactions = $wpdb->prefix . 'amal_social_reactions';
        
        // Initialize plugin
        add_action('plugins_loaded', [$this, 'init']);
        
        // Create database tables on activation
        register_activation_hook(__FILE__, [$this, 'create_tables']);
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // AJAX handlers
        add_action('wp_ajax_amal_create_post', [$this, 'handle_create_post']);
        add_action('wp_ajax_amal_react_to_post', [$this, 'handle_reaction']);
        add_action('wp_ajax_amal_load_posts', [$this, 'handle_load_posts']);
        
        // Start session for authentication
        if (!session_id()) {
            session_start();
        }
    }
    
    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Load required files
        $this->load_dependencies();
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies()
    {
        // Load helper functions if auth plugin is available
        if (file_exists(plugin_dir_path(__FILE__) . '../amal-auth/includes/helper-functions.php')) {
            require_once plugin_dir_path(__FILE__) . '../amal-auth/includes/helper-functions.php';
        }
    }
    
    /**
     * Create database tables
     */
    public function create_tables()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Social posts table
        $sql_posts = "CREATE TABLE {$this->table_posts} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            content text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_active tinyint(1) DEFAULT 1,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Reactions table
        $sql_reactions = "CREATE TABLE {$this->table_reactions} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id mediumint(9) NOT NULL,
            user_id mediumint(9) NOT NULL,
            reaction_type enum('like', 'dislike') NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_user_post_reaction (post_id, user_id),
            KEY post_id (post_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_posts);
        dbDelta($sql_reactions);
        
        // Create SQL file for manual execution
        $this->generate_sql_file();
    }
    
    /**
     * Generate SQL file for manual database setup
     */
    private function generate_sql_file()
    {
        $sql_content = "-- Amal Social Media Database Schema\n";
        $sql_content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        $sql_content .= "CREATE TABLE IF NOT EXISTS wp_amal_social_posts (\n";
        $sql_content .= "    id mediumint(9) NOT NULL AUTO_INCREMENT,\n";
        $sql_content .= "    user_id mediumint(9) NOT NULL,\n";
        $sql_content .= "    content text NOT NULL,\n";
        $sql_content .= "    created_at datetime DEFAULT CURRENT_TIMESTAMP,\n";
        $sql_content .= "    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
        $sql_content .= "    is_active tinyint(1) DEFAULT 1,\n";
        $sql_content .= "    PRIMARY KEY (id),\n";
        $sql_content .= "    KEY user_id (user_id),\n";
        $sql_content .= "    KEY created_at (created_at)\n";
        $sql_content .= ") DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;\n\n";
        
        $sql_content .= "CREATE TABLE IF NOT EXISTS wp_amal_social_reactions (\n";
        $sql_content .= "    id mediumint(9) NOT NULL AUTO_INCREMENT,\n";
        $sql_content .= "    post_id mediumint(9) NOT NULL,\n";
        $sql_content .= "    user_id mediumint(9) NOT NULL,\n";
        $sql_content .= "    reaction_type enum('like', 'dislike') NOT NULL,\n";
        $sql_content .= "    created_at datetime DEFAULT CURRENT_TIMESTAMP,\n";
        $sql_content .= "    PRIMARY KEY (id),\n";
        $sql_content .= "    UNIQUE KEY unique_user_post_reaction (post_id, user_id),\n";
        $sql_content .= "    KEY post_id (post_id),\n";
        $sql_content .= "    KEY user_id (user_id)\n";
        $sql_content .= ") DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;\n\n";
        
        file_put_contents(plugin_dir_path(__FILE__) . 'social.sql', $sql_content);
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'amal-social',
            plugin_dir_url(__FILE__) . 'assets/social.js',
            ['jquery'],
            '1.0.0',
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('amal-social', 'amal_social_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amal_social_nonce')
        ]);
        
        wp_enqueue_style(
            'amal-social',
            plugin_dir_url(__FILE__) . 'assets/social.css',
            [],
            '1.0.0'
        );
    }
    
    /**
     * Get posts for the feed
     */
    public function get_posts($limit = 20, $offset = 0)
    {
        global $wpdb;
        
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.*, u.first_name, u.last_name, u.user_type,
                        (SELECT COUNT(*) FROM {$this->table_reactions} r 
                         WHERE r.post_id = p.id AND r.reaction_type = 'like') as like_count,
                        (SELECT COUNT(*) FROM {$this->table_reactions} r 
                         WHERE r.post_id = p.id AND r.reaction_type = 'dislike') as dislike_count
                 FROM {$this->table_posts} p
                 LEFT JOIN {$wpdb->prefix}amal_users u ON p.user_id = u.id
                 WHERE p.is_active = 1 AND u.is_active = 1
                 ORDER BY p.created_at DESC
                 LIMIT %d OFFSET %d",
                $limit,
                $offset
            )
        );
        
        return $posts;
    }
    
    /**
     * Get posts by a specific user
     */
    public function get_user_posts($user_id, $limit = 20, $offset = 0)
    {
        global $wpdb;
        
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.*, u.first_name, u.last_name, u.user_type,
                        (SELECT COUNT(*) FROM {$this->table_reactions} r 
                         WHERE r.post_id = p.id AND r.reaction_type = 'like') as like_count,
                        (SELECT COUNT(*) FROM {$this->table_reactions} r 
                         WHERE r.post_id = p.id AND r.reaction_type = 'dislike') as dislike_count
                 FROM {$this->table_posts} p
                 LEFT JOIN {$wpdb->prefix}amal_users u ON p.user_id = u.id
                 WHERE p.is_active = 1 AND u.is_active = 1 AND p.user_id = %d
                 ORDER BY p.created_at DESC
                 LIMIT %d OFFSET %d",
                $user_id,
                $limit,
                $offset
            )
        );
        
        return $posts;
    }
    
    /**
     * Handle post creation
     */
    public function handle_create_post()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_social_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        // Check if user is logged in
        if (!function_exists('amal_is_logged_in') || !amal_is_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to create posts']);
            return;
        }
        
        $content = sanitize_textarea_field($_POST['content']);
        $user_id = amal_current_user_id();
        
        if (empty($content)) {
            wp_send_json_error(['message' => 'Post content cannot be empty']);
            return;
        }
        
        global $wpdb;
        
        $result = $wpdb->insert(
            $this->table_posts,
            [
                'user_id' => $user_id,
                'content' => $content,
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s']
        );
        
        if ($result) {
            wp_send_json_success([
                'message' => 'Post created successfully',
                'post_id' => $wpdb->insert_id
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to create post']);
        }
    }
    
    /**
     * Handle reactions (like/dislike)
     */
    public function handle_reaction()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amal_social_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        // Check if user is logged in
        if (!function_exists('amal_is_logged_in') || !amal_is_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to react to posts']);
            return;
        }
        
        $post_id = intval($_POST['post_id']);
        $reaction_type = sanitize_text_field($_POST['reaction_type']);
        $user_id = amal_current_user_id();
        
        if (!in_array($reaction_type, ['like', 'dislike'])) {
            wp_send_json_error(['message' => 'Invalid reaction type']);
            return;
        }
        
        global $wpdb;
        
        // Check if user already reacted to this post
        $existing_reaction = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_reactions} WHERE post_id = %d AND user_id = %d",
                $post_id,
                $user_id
            )
        );
        
        if ($existing_reaction) {
            if ($existing_reaction->reaction_type === $reaction_type) {
                // Remove reaction if clicking the same reaction
                $wpdb->delete(
                    $this->table_reactions,
                    ['post_id' => $post_id, 'user_id' => $user_id],
                    ['%d', '%d']
                );
                $action = 'removed';
            } else {
                // Update reaction type
                $wpdb->update(
                    $this->table_reactions,
                    ['reaction_type' => $reaction_type],
                    ['post_id' => $post_id, 'user_id' => $user_id],
                    ['%s'],
                    ['%d', '%d']
                );
                $action = 'updated';
            }
        } else {
            // Add new reaction
            $wpdb->insert(
                $this->table_reactions,
                [
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => $reaction_type
                ],
                ['%d', '%d', '%s']
            );
            $action = 'added';
        }
        
        // Get updated counts
        $like_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_reactions} WHERE post_id = %d AND reaction_type = 'like'",
                $post_id
            )
        );
        
        $dislike_count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_reactions} WHERE post_id = %d AND reaction_type = 'dislike'",
                $post_id
            )
        );
        
        wp_send_json_success([
            'message' => 'Reaction ' . $action,
            'like_count' => (int) $like_count,
            'dislike_count' => (int) $dislike_count
        ]);
    }
    
    /**
     * Handle loading posts via AJAX
     */
    public function handle_load_posts()
    {
        $limit = intval($_POST['limit'] ?? 20);
        $offset = intval($_POST['offset'] ?? 0);
        
        $posts = $this->get_posts($limit, $offset);
        
        ob_start();
        foreach ($posts as $post) {
            include plugin_dir_path(__FILE__) . 'templates/post-card.php';
        }
        $html = ob_get_clean();
        
        wp_send_json_success(['html' => $html]);
    }
}

// Initialize the plugin
new AmalSocialPlugin();