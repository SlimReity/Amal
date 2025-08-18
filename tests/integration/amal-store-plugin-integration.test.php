<?php
/**
 * Test runner for Amal Store Plugin
 * Run this script to validate the database schema implementation
 */

// Include WordPress
require_once dirname(__DIR__, 2) . '/web/wp-config.php';

// Include plugin classes
require_once dirname(__DIR__, 2) . '/web/app/plugins/amal-store/includes/class-amal-store-database.php';
require_once dirname(__DIR__, 2) . '/web/app/plugins/amal-store/tests/test-database.php';

class Amal_Store_Test_Runner {
    
    private $tests_passed = 0;
    private $tests_failed = 0;
    private $database;
    
    public function __construct() {
        $this->database = new Amal_Store_Database();
        echo "🏪 Amal Store Database Schema Test Runner\n";
        echo str_repeat("=", 50) . "\n\n";
    }
    
    public function run_all_tests() {
        $this->test_plugin_structure();
        $this->test_database_functionality();
        $this->test_schema_compliance();
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Test Results:\n";
        echo "✅ Passed: {$this->tests_passed}\n";
        echo "❌ Failed: {$this->tests_failed}\n";
        echo "Total: " . ($this->tests_passed + $this->tests_failed) . "\n";
        
        if ($this->tests_failed === 0) {
            echo "\n🎉 All tests passed! The database schema is ready for production.\n";
        } else {
            echo "\n⚠️  Some tests failed. Please review the errors above.\n";
        }
    }
    
    private function test_plugin_structure() {
        echo "📁 Testing Plugin Structure...\n";
        
        // Test main plugin file exists
        $plugin_root = dirname(__DIR__, 2) . '/web/app/plugins/amal-store';
        $this->assert_file_exists($plugin_root . '/amal-store.php', 'Main plugin file');
        
        // Test includes directory
        $this->assert_file_exists($plugin_root . '/includes/class-amal-store.php', 'Main plugin class');
        $this->assert_file_exists($plugin_root . '/includes/class-amal-store-database.php', 'Database class');
        
        // Test documentation
        $this->assert_file_exists($plugin_root . '/README.md', 'README documentation');
        
        // Test admin files
        $this->assert_file_exists($plugin_root . '/admin/test-schema.html', 'HTML test page');
        
        echo "\n";
    }
    
    private function test_database_functionality() {
        echo "🗄️ Testing Database Functionality...\n";
        
        // Test table creation
        try {
            Amal_Store_Database::create_tables();
            $this->assert_true($this->database->tables_exist(), 'Tables creation');
        } catch (Exception $e) {
            $this->assert_false(true, 'Tables creation failed: ' . $e->getMessage());
        }
        
        // Test table names
        $table_names = $this->database->get_table_names();
        $this->assert_true(isset($table_names['items']), 'Items table name exists');
        $this->assert_true(isset($table_names['orders']), 'Orders table name exists');
        $this->assert_true(isset($table_names['order_items']), 'Order items table name exists');
        
        echo "\n";
    }
    
    private function test_schema_compliance() {
        echo "📋 Testing Schema Compliance...\n";
        
        global $wpdb;
        
        if (!$this->database->tables_exist()) {
            $this->assert_false(true, 'Cannot test schema - tables do not exist');
            return;
        }
        
        $table_names = $this->database->get_table_names();
        
        // Test items table structure
        $items_columns = $wpdb->get_results("DESCRIBE {$table_names['items']}");
        $items_column_names = array_column($items_columns, 'Field');
        
        $required_items_columns = ['id', 'title', 'category', 'description', 'price', 'stock_qty', 'image_url', 'is_active', 'created_at', 'updated_at'];
        foreach ($required_items_columns as $column) {
            $this->assert_true(in_array($column, $items_column_names), "Items table has '$column' column");
        }
        
        // Test orders table structure
        $orders_columns = $wpdb->get_results("DESCRIBE {$table_names['orders']}");
        $orders_column_names = array_column($orders_columns, 'Field');
        
        $required_orders_columns = ['id', 'user_id', 'total_price', 'status', 'created_at', 'updated_at'];
        foreach ($required_orders_columns as $column) {
            $this->assert_true(in_array($column, $orders_column_names), "Orders table has '$column' column");
        }
        
        // Test order_items table structure
        $order_items_columns = $wpdb->get_results("DESCRIBE {$table_names['order_items']}");
        $order_items_column_names = array_column($order_items_columns, 'Field');
        
        $required_order_items_columns = ['id', 'order_id', 'item_id', 'quantity', 'price'];
        foreach ($required_order_items_columns as $column) {
            $this->assert_true(in_array($column, $order_items_column_names), "Order items table has '$column' column");
        }
        
        // Test enum values for status
        $status_column = null;
        foreach ($orders_columns as $column) {
            if ($column->Field === 'status') {
                $status_column = $column;
                break;
            }
        }
        
        if ($status_column) {
            $this->assert_true(strpos(strtolower($status_column->Type), 'enum') !== false, 'Order status is enum type');
            
            $expected_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            foreach ($expected_statuses as $status) {
                $this->assert_true(strpos($status_column->Type, $status) !== false, "Status enum contains '$status'");
            }
        }
        
        echo "\n";
    }
    
    private function assert_true($condition, $message) {
        if ($condition) {
            echo "✅ $message\n";
            $this->tests_passed++;
        } else {
            echo "❌ $message\n";
            $this->tests_failed++;
        }
    }
    
    private function assert_false($condition, $message) {
        $this->assert_true(!$condition, $message);
    }
    
    private function assert_file_exists($path, $description) {
        $file_path = dirname(__FILE__) . '/' . $path;
        $this->assert_true(file_exists($file_path), "$description exists");
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $test_runner = new Amal_Store_Test_Runner();
    $test_runner->run_all_tests();
}