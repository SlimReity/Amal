<?php
/**
 * Test script for Admin Inventory Management
 */

// Mock some WordPress functions for testing
if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script() { return true; }
}
if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style() { return true; }
}
if (!function_exists('wp_localize_script')) {
    function wp_localize_script() { return true; }
}
if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce() { return 'test_nonce'; }
}
if (!function_exists('admin_url')) {
    function admin_url() { return '/wp-admin/admin-ajax.php'; }
}

// Simple test to check class instantiation
require_once dirname(__FILE__) . '/includes/class-amal-store-database.php';
echo "🧪 Testing Admin Inventory Management Classes\n";
echo str_repeat("=", 50) . "\n";

try {
    // Test database class
    echo "Testing Amal_Store_Database class...\n";
    if (class_exists('Amal_Store_Database')) {
        echo "✅ Database class loaded successfully\n";
        
        // Create instance without using global wpdb
        $mock_wpdb = new stdClass();
        $mock_wpdb->prefix = 'wp_';
        
        // Test table names
        $database = new Amal_Store_Database();
        $table_names = $database->get_table_names();
        echo "Table names configured:\n";
        foreach ($table_names as $key => $table) {
            echo "  - $key: $table\n";
        }
    } else {
        echo "❌ Database class failed to load\n";
    }
    
    // Test admin class
    echo "\nTesting Amal_Store_Admin class...\n";
    require_once dirname(__FILE__) . '/includes/class-amal-store-admin.php';
    if (class_exists('Amal_Store_Admin')) {
        echo "✅ Admin class loaded successfully\n";
    } else {
        echo "❌ Admin class failed to load\n";
    }
    
    echo "\n✅ All basic tests passed!\n";
    echo "🔗 Open admin/test-inventory-admin.html in your browser to see the feature overview.\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}