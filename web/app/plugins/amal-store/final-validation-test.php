<?php
/**
 * Amal Store Final Validation Test
 * 
 * Comprehensive test suite for the Amal Store plugin including
 * sample items population functionality.
 */

// Test environment setup
$start_time = microtime(true);
$test_results = array();
$failed_tests = 0;
$passed_tests = 0;

/**
 * Test utility functions
 */
function run_test($name, $callback) {
    global $test_results, $failed_tests, $passed_tests;
    
    try {
        $result = $callback();
        if ($result === true || (is_array($result) && $result['success'])) {
            echo "✅ PASS: $name\n";
            $test_results[$name] = array('status' => 'PASS', 'details' => $result);
            $passed_tests++;
        } else {
            echo "❌ FAIL: $name\n";
            if (is_array($result) && isset($result['error'])) {
                echo "   Error: " . $result['error'] . "\n";
            }
            $test_results[$name] = array('status' => 'FAIL', 'details' => $result);
            $failed_tests++;
        }
    } catch (Exception $e) {
        echo "❌ FAIL: $name (Exception: " . $e->getMessage() . ")\n";
        $test_results[$name] = array('status' => 'FAIL', 'error' => $e->getMessage());
        $failed_tests++;
    }
}

echo "🧪 Amal Store Final Validation Test\n";
echo "====================================\n\n";

// Test 1: Check if plugin files exist
run_test("Plugin Files Exist", function() {
    $required_files = array(
        'amal-store.php',
        'includes/class-amal-store.php',
        'includes/class-amal-store-database.php',
        'includes/class-amal-store-frontend.php',
        'includes/class-amal-store-admin.php',
        'populate-sample-items.php',
        'sample-items-manager.html',
        'SAMPLE_ITEMS_README.md'
    );
    
    foreach ($required_files as $file) {
        if (!file_exists($file)) {
            return array('success' => false, 'error' => "Missing file: $file");
        }
    }
    return true;
});

// Test 2: Check if classes can be loaded
run_test("Class Loading", function() {
    if (!class_exists('Amal_Store_Database')) {
        require_once 'includes/class-amal-store-database.php';
    }
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    return class_exists('Amal_Store_Database') && class_exists('Amal_Store_Sample_Items');
});

// Test 3: Database table structure validation
run_test("Database Table Structure", function() {
    if (!class_exists('Amal_Store_Database')) {
        require_once 'includes/class-amal-store-database.php';
    }
    
    $db = new Amal_Store_Database();
    $tables = $db->get_table_names();
    
    $expected_tables = array('items', 'orders', 'order_items');
    foreach ($expected_tables as $table) {
        if (!isset($tables[$table])) {
            return array('success' => false, 'error' => "Missing table: $table");
        }
    }
    
    return array('success' => true, 'tables' => $tables);
});

// Test 4: Sample Items Data Validation
run_test("Sample Items Data Structure", function() {
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $items = $populator->get_sample_items();
    
    if (empty($items)) {
        return array('success' => false, 'error' => 'No sample items found');
    }
    
    // Validate item structure
    $required_fields = array('title', 'category', 'description', 'price', 'stock_qty', 'image_url', 'is_active');
    foreach ($items as $index => $item) {
        foreach ($required_fields as $field) {
            if (!isset($item[$field])) {
                return array('success' => false, 'error' => "Item $index missing field: $field");
            }
        }
    }
    
    return array('success' => true, 'item_count' => count($items));
});

// Test 5: Category Coverage
run_test("Category Coverage", function() {
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $items = $populator->get_sample_items();
    
    $categories = array();
    foreach ($items as $item) {
        $categories[$item['category']] = ($categories[$item['category']] ?? 0) + 1;
    }
    
    $expected_categories = array('Food', 'Accessories', 'Toys', 'Housing', 'Aquarium', 'Health');
    foreach ($expected_categories as $cat) {
        if (!isset($categories[$cat])) {
            return array('success' => false, 'error' => "Missing category: $cat");
        }
    }
    
    return array('success' => true, 'categories' => $categories);
});

// Test 6: Price Range Validation
run_test("Price Range Validation", function() {
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $items = $populator->get_sample_items();
    
    $prices = array_column($items, 'price');
    $min_price = min($prices);
    $max_price = max($prices);
    
    // Should have reasonable price range for a pet store
    if ($min_price < 10 || $max_price > 200) {
        return array('success' => false, 'error' => "Price range unrealistic: $min_price - $max_price");
    }
    
    return array('success' => true, 'price_range' => "$min_price - $max_price");
});

// Test 7: Stock Quantity Validation
run_test("Stock Quantity Validation", function() {
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $items = $populator->get_sample_items();
    
    $in_stock = 0;
    $out_of_stock = 0;
    
    foreach ($items as $item) {
        if ($item['stock_qty'] > 0) {
            $in_stock++;
        } else {
            $out_of_stock++;
        }
    }
    
    // Should have mostly in-stock items with at least one out-of-stock for testing
    if ($in_stock < 15 || $out_of_stock < 1) {
        return array('success' => false, 'error' => "Stock distribution poor: $in_stock in stock, $out_of_stock out");
    }
    
    return array('success' => true, 'in_stock' => $in_stock, 'out_of_stock' => $out_of_stock);
});

// Test 8: Image URL Format
run_test("Image URL Format", function() {
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $items = $populator->get_sample_items();
    
    foreach ($items as $item) {
        if (!filter_var($item['image_url'], FILTER_VALIDATE_URL)) {
            return array('success' => false, 'error' => "Invalid image URL: " . $item['image_url']);
        }
    }
    
    return true;
});

// Test 9: HTML Manager File
run_test("HTML Manager Interface", function() {
    if (!file_exists('sample-items-manager.html')) {
        return array('success' => false, 'error' => 'HTML manager file not found');
    }
    
    $content = file_get_contents('sample-items-manager.html');
    if (strpos($content, 'Amal Store Sample Items Manager') === false) {
        return array('success' => false, 'error' => 'HTML manager content invalid');
    }
    
    return true;
});

// Test 10: Documentation Completeness
run_test("Documentation Completeness", function() {
    if (!file_exists('SAMPLE_ITEMS_README.md')) {
        return array('success' => false, 'error' => 'README file not found');
    }
    
    $content = file_get_contents('SAMPLE_ITEMS_README.md');
    $required_sections = array('Quick Start', 'Sample Data Overview', 'Testing Integration');
    
    foreach ($required_sections as $section) {
        if (strpos($content, $section) === false) {
            return array('success' => false, 'error' => "Missing documentation section: $section");
        }
    }
    
    return true;
});

// Test 11: Demo Integration
run_test("Demo Files Integration", function() {
    $demo_files = array(
        'demo/storefront-mockup.html',
        'demo/storefront-implementation.html'
    );
    
    foreach ($demo_files as $file) {
        if (!file_exists($file)) {
            return array('success' => false, 'error' => "Missing demo file: $file");
        }
    }
    
    return true;
});

// Test 12: Mock Population Test
run_test("Mock Population Test", function() {
    // Set up minimal mock environment
    if (!defined('ABSPATH')) {
        define('ABSPATH', dirname(dirname(dirname(dirname(__DIR__)))) . '/');
    }
    
    // Create mock wpdb
    global $wpdb;
    $wpdb = new stdClass();
    $wpdb->prefix = 'wp_';
    
    // Mock database methods
    $wpdb->get_var = function($query) {
        if (strpos($query, 'SHOW TABLES') !== false) {
            return 'wp_amal_items';
        }
        return null;
    };
    
    $wpdb->insert = function($table, $data, $format) {
        return true;
    };
    
    $wpdb->prepare = function($query, ...$args) {
        return $query;
    };
    
    if (!class_exists('Amal_Store_Sample_Items')) {
        require_once 'populate-sample-items.php';
    }
    
    $populator = new Amal_Store_Sample_Items();
    $result = $populator->populate_items(false);
    
    return $result;
});

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Passed: $passed_tests tests\n";
echo "❌ Failed: $failed_tests tests\n";
echo "⏱️  Total time: " . round(microtime(true) - $start_time, 2) . " seconds\n\n";

if ($failed_tests === 0) {
    echo "🎉 ALL TESTS PASSED! 🎉\n";
    echo "The Amal Store sample items system is ready for use.\n\n";
    echo "Next steps:\n";
    echo "1. Open sample-items-manager.html in your browser\n";
    echo "2. Click 'Add Sample Items' to populate your store\n";
    echo "3. Visit your storefront to see the new items\n";
} else {
    echo "⚠️  Some tests failed. Please review the errors above.\n";
}

echo "\n📋 Detailed Results:\n";
foreach ($test_results as $name => $result) {
    echo "- $name: " . $result['status'];
    if (isset($result['error'])) {
        echo " (" . $result['error'] . ")";
    }
    echo "\n";
}

exit($failed_tests > 0 ? 1 : 0);