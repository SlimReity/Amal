<?php
/**
 * Database management class for Amal Store
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store_Database {
    
    private $wpdb;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Create all database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Items table
        $items_table = $wpdb->prefix . 'amal_items';
        $items_sql = "CREATE TABLE $items_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            category varchar(100) NOT NULL,
            description text,
            price decimal(10,2) NOT NULL,
            stock_qty int(11) NOT NULL DEFAULT 0,
            image_url varchar(500),
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_category (category),
            KEY idx_is_active (is_active),
            KEY idx_price (price),
            CONSTRAINT chk_stock_qty CHECK (stock_qty >= 0)
        ) $charset_collate;";
        
        // Orders table
        $orders_table = $wpdb->prefix . 'amal_orders';
        $orders_sql = "CREATE TABLE $orders_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            total_price decimal(10,2) NOT NULL,
            status enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_user_id (user_id),
            KEY idx_status (status),
            KEY idx_created_at (created_at),
            CONSTRAINT fk_orders_user_id FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}amal_users (id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Order items table
        $order_items_table = $wpdb->prefix . 'amal_order_items';
        $order_items_sql = "CREATE TABLE $order_items_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            item_id bigint(20) unsigned NOT NULL,
            quantity int(11) NOT NULL,
            price decimal(10,2) NOT NULL,
            PRIMARY KEY (id),
            KEY idx_order_id (order_id),
            KEY idx_item_id (item_id),
            CONSTRAINT fk_order_items_order_id FOREIGN KEY (order_id) REFERENCES $orders_table (id) ON DELETE CASCADE,
            CONSTRAINT fk_order_items_item_id FOREIGN KEY (item_id) REFERENCES $items_table (id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Execute table creation
        dbDelta($items_sql);
        dbDelta($orders_sql);
        dbDelta($order_items_sql);
        
        // Save database version
        update_option('amal_store_db_version', AMAL_STORE_VERSION);
        
        // Generate SQL files for manual setup
        self::generate_sql_files($items_sql, $orders_sql, $order_items_sql);
    }
    
    /**
     * Drop all database tables (for deactivation)
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'amal_order_items',
            $wpdb->prefix . 'amal_orders',
            $wpdb->prefix . 'amal_items'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
        
        delete_option('amal_store_db_version');
    }
    
    /**
     * Generate SQL files for manual database setup
     */
    private static function generate_sql_files($items_sql, $orders_sql, $order_items_sql) {
        $upload_dir = wp_upload_dir();
        $sql_dir = $upload_dir['basedir'] . '/amal-store-sql/';
        
        if (!file_exists($sql_dir)) {
            wp_mkdir_p($sql_dir);
        }
        
        // Combined SQL file
        $combined_sql = "-- Amal Store Database Schema\n";
        $combined_sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $combined_sql .= "-- Items Table\n" . $items_sql . "\n\n";
        $combined_sql .= "-- Orders Table\n" . $orders_sql . "\n\n";
        $combined_sql .= "-- Order Items Table\n" . $order_items_sql . "\n\n";
        
        // Sample data
        $combined_sql .= self::get_sample_data_sql();
        
        file_put_contents($sql_dir . 'amal-store-schema.sql', $combined_sql);
        
        // Individual table files
        file_put_contents($sql_dir . 'items-table.sql', $items_sql);
        file_put_contents($sql_dir . 'orders-table.sql', $orders_sql);
        file_put_contents($sql_dir . 'order-items-table.sql', $order_items_sql);
    }
    
    /**
     * Get sample data SQL for testing
     */
    private static function get_sample_data_sql() {
        global $wpdb;
        
        $items_table = $wpdb->prefix . 'amal_items';
        $orders_table = $wpdb->prefix . 'amal_orders';
        $order_items_table = $wpdb->prefix . 'amal_order_items';
        
        return "-- Sample Data for Testing\n\n" .
               "-- Sample Items\n" .
               "INSERT INTO $items_table (title, category, description, price, stock_qty, is_active) VALUES\n" .
               "('Premium Dog Food', 'Food', 'High-quality dry dog food for adult dogs', 45.99, 100, 1),\n" .
               "('Cat Litter Box', 'Accessories', 'Self-cleaning automatic litter box', 89.99, 25, 1),\n" .
               "('Dog Leash', 'Accessories', 'Durable leather dog leash', 24.50, 50, 1),\n" .
               "('Bird Cage', 'Housing', 'Large bird cage with multiple perches', 149.99, 15, 1),\n" .
               "('Fish Tank Filter', 'Aquarium', 'Advanced filtration system for aquariums', 67.99, 30, 1);\n\n" .
               "-- Sample Orders (requires users to exist in amal_users table)\n" .
               "-- INSERT INTO $orders_table (user_id, total_price, status) VALUES\n" .
               "-- (1, 70.49, 'pending'),\n" .
               "-- (2, 149.99, 'processing'),\n" .
               "-- (1, 89.99, 'shipped');\n\n" .
               "-- Sample Order Items (uncomment after orders are created)\n" .
               "-- INSERT INTO $order_items_table (order_id, item_id, quantity, price) VALUES\n" .
               "-- (1, 1, 1, 45.99),\n" .
               "-- (1, 3, 1, 24.50),\n" .
               "-- (2, 4, 1, 149.99),\n" .
               "-- (3, 2, 1, 89.99);\n\n";
    }
    
    /**
     * Check if tables exist
     */
    public function tables_exist() {
        $tables = array(
            $this->wpdb->prefix . 'amal_items',
            $this->wpdb->prefix . 'amal_orders',
            $this->wpdb->prefix . 'amal_order_items'
        );
        
        foreach ($tables as $table) {
            if ($this->wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get table names
     */
    public function get_table_names() {
        return array(
            'items' => $this->wpdb->prefix . 'amal_items',
            'orders' => $this->wpdb->prefix . 'amal_orders',
            'order_items' => $this->wpdb->prefix . 'amal_order_items'
        );
    }
}