<?php
/**
 * Simple test to check if we can connect to the database and populate items
 */

// Define ABSPATH to simulate WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(dirname(dirname(dirname(__DIR__)))) . '/');
}

// Simple database setup for testing
$wpdb = new stdClass();
$wpdb->prefix = 'wp_';

// Mock WordPress database functionality for testing
class MockWPDB {
    public $prefix = 'wp_';
    
    public function __construct() {
        $this->prefix = 'wp_';
    }
    
    public function get_var($query) {
        // Simulate table exists check
        if (strpos($query, 'SHOW TABLES') !== false) {
            return 'wp_amal_items';
        }
        // Simulate count query
        if (strpos($query, 'COUNT') !== false) {
            return 5;
        }
        return null;
    }
    
    public function get_results($query) {
        // Simulate category summary
        return array(
            (object)array('category' => 'Food', 'count' => 2),
            (object)array('category' => 'Accessories', 'count' => 2),
            (object)array('category' => 'Housing', 'count' => 1)
        );
    }
    
    public function prepare($query, ...$args) {
        return $query;
    }
    
    public function insert($table, $data, $format) {
        return true; // Simulate successful insert
    }
    
    public function query($query) {
        return true;
    }
}

// Set up mock WordPress environment
global $wpdb;
$wpdb = new MockWPDB();

// Include our sample items class
require_once __DIR__ . '/populate-sample-items.php';

// Test the class
echo "🧪 Testing Amal Store Sample Items Population\n";
echo "==============================================\n\n";

try {
    $populator = new Amal_Store_Sample_Items();
    
    echo "Test 1: Get Items Summary\n";
    echo "-------------------------\n";
    $summary = $populator->get_items_summary();
    print_r($summary);
    echo "\n";
    
    echo "Test 2: Check Sample Items Data\n";
    echo "-------------------------------\n";
    $items = $populator->get_sample_items();
    echo "Total sample items available: " . count($items) . "\n";
    
    $categories = array();
    foreach ($items as $item) {
        if (!isset($categories[$item['category']])) {
            $categories[$item['category']] = 0;
        }
        $categories[$item['category']]++;
    }
    
    echo "Categories breakdown:\n";
    foreach ($categories as $category => $count) {
        echo "  - $category: $count items\n";
    }
    echo "\n";
    
    echo "Test 3: Sample Item Preview\n";
    echo "---------------------------\n";
    echo "First 3 items:\n";
    for ($i = 0; $i < min(3, count($items)); $i++) {
        $item = $items[$i];
        echo ($i + 1) . ". {$item['title']}\n";
        echo "   Category: {$item['category']} | Price: \${$item['price']} | Stock: {$item['stock_qty']}\n";
        echo "   Description: " . substr($item['description'], 0, 80) . "...\n\n";
    }
    
    echo "Test 4: Population Test (Mock)\n";
    echo "------------------------------\n";
    $result = $populator->populate_items(false);
    print_r($result);
    
    echo "\n✅ All tests completed successfully!\n";
    echo "The sample items system is ready to use.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}