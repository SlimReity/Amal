<?php
/**
 * Amal Social Media Plugin Test Script
 * 
 * @package AmalSocial
 * @version 1.0.0
 */

// Include WordPress configuration
require_once __DIR__ . '/../../wp-config.php';

// Include the social plugin
require_once __DIR__ . '/amal-social.php';

// Colors for output
function colorize($text, $color) {
    $colors = [
        'red' => '0;31',
        'green' => '0;32',
        'yellow' => '0;33',
        'blue' => '0;34'
    ];
    return "\033[" . $colors[$color] . "m" . $text . "\033[0m";
}

echo colorize("🧪 Amal Social Media Plugin Tests\n", 'blue');
echo colorize("=====================================\n", 'blue');

$tests_passed = 0;
$tests_failed = 0;

/**
 * Test function
 */
function test($description, $test_function) {
    global $tests_passed, $tests_failed;
    
    echo "\n📋 Testing: " . $description . "\n";
    
    try {
        $result = $test_function();
        if ($result) {
            echo colorize("✅ PASSED: " . $description . "\n", 'green');
            $tests_passed++;
        } else {
            echo colorize("❌ FAILED: " . $description . "\n", 'red');
            $tests_failed++;
        }
    } catch (Exception $e) {
        echo colorize("❌ ERROR: " . $description . " - " . $e->getMessage() . "\n", 'red');
        $tests_failed++;
    }
}

// Test 1: Plugin Class Instantiation
test("Social Plugin Class Instantiation", function() {
    return class_exists('AmalSocialPlugin');
});

// Test 2: Database Table Creation
test("Database Table Schema", function() {
    global $wpdb;
    
    $social_plugin = new AmalSocialPlugin();
    
    // Check if posts table exists
    $posts_table = $wpdb->prefix . 'amal_social_posts';
    $posts_exists = $wpdb->get_var("SHOW TABLES LIKE '$posts_table'") == $posts_table;
    
    // Check if reactions table exists
    $reactions_table = $wpdb->prefix . 'amal_social_reactions';
    $reactions_exists = $wpdb->get_var("SHOW TABLES LIKE '$reactions_table'") == $reactions_table;
    
    return $posts_exists && $reactions_exists;
});

// Test 3: SQL File Generation
test("SQL File Generation", function() {
    $sql_file = __DIR__ . '/social.sql';
    return file_exists($sql_file) && filesize($sql_file) > 0;
});

// Test 4: Plugin Methods
test("Plugin Methods Availability", function() {
    $social_plugin = new AmalSocialPlugin();
    return method_exists($social_plugin, 'get_posts') && 
           method_exists($social_plugin, 'get_user_posts');
});

// Test 5: Asset Files
test("Asset Files Existence", function() {
    $js_file = __DIR__ . '/assets/social.js';
    $css_file = __DIR__ . '/assets/social.css';
    return file_exists($js_file) && file_exists($css_file);
});

// Test 6: Template Files
test("Template Files Existence", function() {
    $template_file = __DIR__ . '/templates/post-card.php';
    return file_exists($template_file);
});

// Test 7: Blog Directory Structure
test("Blog Directory Structure", function() {
    $blog_index = __DIR__ . '/../../blog/index.php';
    $blog_profile = __DIR__ . '/../../blog/profile.php';
    return file_exists($blog_index) && file_exists($blog_profile);
});

// Test 8: Authentication Integration
test("Authentication Helper Integration", function() {
    if (file_exists(__DIR__ . '/../amal-auth/includes/helper-functions.php')) {
        require_once __DIR__ . '/../amal-auth/includes/helper-functions.php';
        return function_exists('amal_is_logged_in') && 
               function_exists('amal_current_user_id') &&
               class_exists('AmalAuthHelper');
    }
    return false;
});

// Test 9: WordPress Integration
test("WordPress Functions Availability", function() {
    return function_exists('wp_enqueue_script') && 
           function_exists('wp_send_json_success') && 
           function_exists('wp_verify_nonce');
});

// Test 10: Database Connection
test("Database Connection", function() {
    global $wpdb;
    return $wpdb && $wpdb->db_connect();
});

// Test 11: Plugin File Structure
test("Plugin File Structure", function() {
    $required_files = [
        __DIR__ . '/amal-social.php',
        __DIR__ . '/README.md',
        __DIR__ . '/social.sql',
        __DIR__ . '/assets/social.js',
        __DIR__ . '/assets/social.css',
        __DIR__ . '/templates/post-card.php'
    ];
    
    foreach ($required_files as $file) {
        if (!file_exists($file)) {
            return false;
        }
    }
    return true;
});

// Test 12: JavaScript Functions
test("JavaScript File Content", function() {
    $js_content = file_get_contents(__DIR__ . '/assets/social.js');
    return strpos($js_content, 'handlePostCreation') !== false &&
           strpos($js_content, 'handleReaction') !== false &&
           strpos($js_content, 'toggleComments') !== false;
});

// Test 13: CSS Styling
test("CSS File Content", function() {
    $css_content = file_get_contents(__DIR__ . '/assets/social.css');
    return strpos($css_content, 'amal-post-card') !== false &&
           strpos($css_content, 'amal-reaction-btn') !== false &&
           strpos($css_content, 'amal-social-feed') !== false;
});

// Test 14: Template Content
test("Post Template Content", function() {
    $template_content = file_get_contents(__DIR__ . '/templates/post-card.php');
    return strpos($template_content, 'data-post-id') !== false &&
           strpos($template_content, 'reaction-btn') !== false &&
           strpos($template_content, 'comment-btn') !== false;
});

// Test 15: Security Features
test("Security Implementation", function() {
    $plugin_content = file_get_contents(__DIR__ . '/amal-social.php');
    return strpos($plugin_content, 'wp_verify_nonce') !== false &&
           strpos($plugin_content, 'sanitize_textarea_field') !== false &&
           strpos($plugin_content, 'esc_html') !== false;
});

// Test Results Summary
echo "\n" . colorize("📊 Test Results Summary", 'blue') . "\n";
echo colorize("======================\n", 'blue');
echo colorize("✅ Tests Passed: " . $tests_passed . "\n", 'green');
echo colorize("❌ Tests Failed: " . $tests_failed . "\n", 'red');
echo colorize("📈 Success Rate: " . round(($tests_passed / ($tests_passed + $tests_failed)) * 100, 1) . "%\n", 'yellow');

if ($tests_failed === 0) {
    echo "\n" . colorize("🎉 All tests passed! Social media functionality is ready to use.", 'green') . "\n";
    echo colorize("🌐 Visit /blog/ to access the social media feed.", 'blue') . "\n";
} else {
    echo "\n" . colorize("⚠️  Some tests failed. Please check the implementation.", 'yellow') . "\n";
}

echo "\n" . colorize("🔗 Integration Instructions:", 'blue') . "\n";
echo "1. Ensure database tables are created (run social.sql)\n";
echo "2. Make sure Amal Auth plugin is active\n";
echo "3. Visit /blog/ to test the social media feed\n";
echo "4. Login as a user to test post creation and reactions\n";
echo "5. Click profile links to test user profiles\n";

?>