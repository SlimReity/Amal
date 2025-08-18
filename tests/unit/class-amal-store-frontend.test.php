<?php
/**
 * Test for Amal Store Frontend functionality
 * Simple validation without full WordPress setup
 */

// Include the frontend class
require_once dirname(__DIR__, 2) . '/web/app/plugins/amal-store/includes/class-amal-store-frontend.php';

class Amal_Store_Frontend_Test {
    
    private $tests_passed = 0;
    private $tests_failed = 0;
    
    public function __construct() {
        echo "🏪 Amal Store Frontend Test Runner\n";
        echo str_repeat("=", 50) . "\n\n";
    }
    
    public function run_tests() {
        $this->test_class_structure();
        $this->test_template_files();
        $this->test_asset_files();
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Test Results:\n";
        echo "✅ Passed: {$this->tests_passed}\n";
        echo "❌ Failed: {$this->tests_failed}\n";
        echo "Total: " . ($this->tests_passed + $this->tests_failed) . "\n";
        
        if ($this->tests_failed === 0) {
            echo "\n🎉 All frontend tests passed!\n";
        } else {
            echo "\n⚠️  Some tests failed. Please review the errors above.\n";
        }
    }
    
    private function test_class_structure() {
        echo "📁 Testing Frontend Class Structure...\n";
        
        // Test if class exists
        $this->assert_true(class_exists('Amal_Store_Frontend'), 'Frontend class exists');
        
        // Test if class has required methods
        if (class_exists('Amal_Store_Frontend')) {
            $methods = get_class_methods('Amal_Store_Frontend');
            $required_methods = [
                'render_storefront',
                'render_item_detail',
                'get_items',
                'get_item',
                'get_categories',
                'handle_add_to_cart'
            ];
            
            foreach ($required_methods as $method) {
                $this->assert_true(in_array($method, $methods), "Method '$method' exists");
            }
        }
        
        echo "\n";
    }
    
    private function test_template_files() {
        echo "📄 Testing Template Files...\n";
        
        $template_dir = dirname(__DIR__, 2) . '/web/app/plugins/amal-store/templates/';
        
        $required_templates = [
            'storefront.php' => 'Storefront template',
            'item-detail.php' => 'Item detail template'
        ];
        
        foreach ($required_templates as $file => $description) {
            $this->assert_file_exists($template_dir . $file, $description);
        }
        
        // Test if templates contain expected content
        if (file_exists($template_dir . 'storefront.php')) {
            $storefront_content = file_get_contents($template_dir . 'storefront.php');
            $this->assert_true(strpos($storefront_content, 'amal-storefront') !== false, 'Storefront template has correct CSS classes');
            $this->assert_true(strpos($storefront_content, 'amal-items-grid') !== false, 'Storefront template has items grid');
        }
        
        if (file_exists($template_dir . 'item-detail.php')) {
            $item_detail_content = file_get_contents($template_dir . 'item-detail.php');
            $this->assert_true(strpos($item_detail_content, 'amal-item-detail') !== false, 'Item detail template has correct CSS classes');
            $this->assert_true(strpos($item_detail_content, 'amal-add-to-cart') !== false, 'Item detail template has add to cart button');
        }
        
        echo "\n";
    }
    
    private function test_asset_files() {
        echo "💻 Testing Asset Files...\n";
        
        $assets_dir = dirname(__DIR__, 2) . '/web/app/plugins/amal-store/assets/';
        
        // Test CSS file
        $css_file = $assets_dir . 'css/frontend.css';
        $this->assert_file_exists($css_file, 'Frontend CSS file');
        
        if (file_exists($css_file)) {
            $css_content = file_get_contents($css_file);
            $required_css_classes = [
                '.amal-storefront',
                '.amal-items-grid',
                '.amal-item-card',
                '.amal-item-detail',
                '.amal-add-to-cart'
            ];
            
            foreach ($required_css_classes as $class) {
                $this->assert_true(strpos($css_content, $class) !== false, "CSS contains '$class' class");
            }
        }
        
        // Test JS file
        $js_file = $assets_dir . 'js/frontend.js';
        $this->assert_file_exists($js_file, 'Frontend JS file');
        
        if (file_exists($js_file)) {
            $js_content = file_get_contents($js_file);
            $this->assert_true(strpos($js_content, 'AmalStore') !== false, 'JS contains AmalStore object');
            $this->assert_true(strpos($js_content, 'handleAddToCart') !== false, 'JS contains add to cart handler');
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
    
    private function assert_file_exists($path, $description) {
        $this->assert_true(file_exists($path), "$description exists");
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $test_runner = new Amal_Store_Frontend_Test();
    $test_runner->run_tests();
}