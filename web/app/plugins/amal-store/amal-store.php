<?php
/**
 * Plugin Name: Amal Store
 * Description: Database schema and functionality for the Amal pet store e-commerce operations
 * Version: 1.0.0
 * Author: Amal Development Team
 * License: MIT
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AMAL_STORE_VERSION', '1.0.0');
define('AMAL_STORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMAL_STORE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main plugin class
require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store.php';

// Initialize the plugin
function amal_store_init() {
    new Amal_Store();
}
add_action('plugins_loaded', 'amal_store_init');

// Activation hook
function amal_store_activate() {
    require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-database.php';
    Amal_Store_Database::create_tables();
}
register_activation_hook(__FILE__, 'amal_store_activate');

// Deactivation hook
function amal_store_deactivate() {
    require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-database.php';
    Amal_Store_Database::drop_tables();
}
register_deactivation_hook(__FILE__, 'amal_store_deactivate');