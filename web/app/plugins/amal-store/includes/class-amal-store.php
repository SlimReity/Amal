<?php
/**
 * Main plugin class for Amal Store
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store {
    
    private $database;
    
    public function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-database.php';
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-frontend.php';
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-admin.php';
        
        // Load WP_CLI commands if WP_CLI is available
        if (defined('WP_CLI') && WP_CLI) {
            require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-cli.php';
        }
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Plugin initialization logic
        $this->database = new Amal_Store_Database();
        
        // Initialize frontend functionality
        new Amal_Store_Frontend();
        
        // Initialize admin functionality
        $this->admin = new Amal_Store_Admin();
        
        // Register WP_CLI commands if WP_CLI is available
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('amal-store', 'Amal_Store_CLI');
        }
    }
}