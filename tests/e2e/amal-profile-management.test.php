<?php
/**
 * Profile Management Test Page
 * This page tests the profile management functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management Test - Amal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .test-title { color: #007cba; font-size: 18px; margin-bottom: 10px; }
        .test-result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .test-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .test-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .test-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .shortcode-demo { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>';

echo '<h1>Amal Profile Management System Test</h1>';

// Test 1: Check if plugin is loaded
echo '<div class="test-section">';
echo '<div class="test-title">Test 1: Plugin Status</div>';
if (class_exists('AmalAuthPlugin')) {
    echo '<div class="test-result test-success">✅ AmalAuthPlugin class exists</div>';
} else {
    echo '<div class="test-result test-error">❌ AmalAuthPlugin class not found</div>';
}

if (class_exists('AmalAuthHelper')) {
    echo '<div class="test-result test-success">✅ AmalAuthHelper class exists</div>';
} else {
    echo '<div class="test-result test-error">❌ AmalAuthHelper class not found</div>';
}
echo '</div>';

// Test 2: Check database tables
echo '<div class="test-section">';
echo '<div class="test-title">Test 2: Database Tables</div>';
global $wpdb;

$tables_to_check = [
    'amal_users' => 'Users table',
    'amal_pets' => 'Pets table', 
    'amal_services' => 'Services table',
    'amal_bookings' => 'Bookings table'
];

foreach ($tables_to_check as $table_suffix => $description) {
    $table_name = $wpdb->prefix . $table_suffix;
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    if ($table_exists) {
        echo '<div class="test-result test-success">✅ ' . $description . ' exists</div>';
    } else {
        echo '<div class="test-result test-warning">⚠️ ' . $description . ' does not exist (run migration)</div>';
    }
}
echo '</div>';

// Test 3: Check helper functions
echo '<div class="test-section">';
echo '<div class="test-title">Test 3: Helper Functions</div>';

$helper_methods = [
    'is_logged_in' => 'Login check function',
    'get_current_user' => 'Get current user function',
    'update_user_profile' => 'Update profile function',
    'get_user_pets' => 'Get pets function',
    'add_pet' => 'Add pet function',
    'get_user_services' => 'Get services function',
    'add_service' => 'Add service function',
    'get_user_bookings' => 'Get bookings function'
];

foreach ($helper_methods as $method => $description) {
    if (method_exists('AmalAuthHelper', $method)) {
        echo '<div class="test-result test-success">✅ ' . $description . ' exists</div>';
    } else {
        echo '<div class="test-result test-error">❌ ' . $description . ' missing</div>';
    }
}
echo '</div>';

// Test 4: Check shortcodes
echo '<div class="test-section">';
echo '<div class="test-title">Test 4: Shortcodes</div>';

$shortcodes = [
    'amal_register_form' => 'Registration form shortcode',
    'amal_login_form' => 'Login form shortcode', 
    'amal_user_info' => 'User info shortcode',
    'amal_profile_management' => 'Profile management shortcode'
];

foreach ($shortcodes as $shortcode => $description) {
    if (shortcode_exists($shortcode)) {
        echo '<div class="test-result test-success">✅ ' . $description . ' registered</div>';
    } else {
        echo '<div class="test-result test-error">❌ ' . $description . ' not registered</div>';
    }
}
echo '</div>';

// Test 5: Check AJAX actions
echo '<div class="test-section">';
echo '<div class="test-title">Test 5: AJAX Actions</div>';

$ajax_actions = [
    'amal_register' => 'Registration handler',
    'amal_login' => 'Login handler',
    'amal_logout' => 'Logout handler',
    'amal_update_profile' => 'Update profile handler',
    'amal_add_pet' => 'Add pet handler',
    'amal_update_pet' => 'Update pet handler', 
    'amal_delete_pet' => 'Delete pet handler',
    'amal_add_service' => 'Add service handler',
    'amal_update_service' => 'Update service handler',
    'amal_delete_service' => 'Delete service handler',
    'amal_upload_image' => 'Image upload handler'
];

foreach ($ajax_actions as $action => $description) {
    if (has_action('wp_ajax_' . $action) || has_action('wp_ajax_nopriv_' . $action)) {
        echo '<div class="test-result test-success">✅ ' . $description . ' registered</div>';
    } else {
        echo '<div class="test-result test-error">❌ ' . $description . ' not registered</div>';
    }
}
echo '</div>';

// Test 6: File structure
echo '<div class="test-section">';
echo '<div class="test-title">Test 6: File Structure</div>';

$files_to_check = [
    AMAL_AUTH_PLUGIN_PATH . 'amal-auth.php' => 'Main plugin file',
    AMAL_AUTH_PLUGIN_PATH . 'includes/helper-functions.php' => 'Helper functions file',
    AMAL_AUTH_PLUGIN_PATH . 'assets/amal-auth.css' => 'CSS file',
    AMAL_AUTH_PLUGIN_PATH . 'assets/amal-auth.js' => 'JavaScript file',
    AMAL_AUTH_PLUGIN_PATH . 'templates/profile-management.php' => 'Profile template',
    AMAL_AUTH_PLUGIN_PATH . 'profile-management-migration.sql' => 'Migration SQL file'
];

foreach ($files_to_check as $file_path => $description) {
    if (file_exists($file_path)) {
        echo '<div class="test-result test-success">✅ ' . $description . ' exists</div>';
    } else {
        echo '<div class="test-result test-error">❌ ' . $description . ' missing</div>';
    }
}
echo '</div>';

// Demo section
echo '<div class="test-section">';
echo '<div class="test-title">Demo: Profile Management Shortcode</div>';
echo '<p>Below is the profile management interface (requires login):</p>';
echo '<div class="shortcode-demo">';
if (AmalAuthHelper::is_logged_in()) {
    echo do_shortcode('[amal_profile_management]');
} else {
    echo '<p>You must be logged in to view the profile management interface.</p>';
    echo '<h4>Login Form:</h4>';
    echo do_shortcode('[amal_login_form]');
    echo '<h4>Registration Form:</h4>';
    echo do_shortcode('[amal_register_form]');
}
echo '</div>';
echo '</div>';

// Summary
echo '<div class="test-section">';
echo '<div class="test-title">Summary</div>';
echo '<p>This test page verifies that the profile management system is properly installed and configured.</p>';
echo '<p><strong>Next steps:</strong></p>';
echo '<ul>';
echo '<li>Run the database migration SQL if tables don\'t exist</li>';
echo '<li>Create a user account and test the profile management features</li>';
echo '<li>Test file uploads and CRUD operations</li>';
echo '<li>Verify responsive design on mobile devices</li>';
echo '</ul>';
echo '</div>';

echo '</body></html>';
?>