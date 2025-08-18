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
 copilot/fix-10
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-frontend.php';
=======
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-admin.php';
main
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Plugin initialization logic
        $this->database = new Amal_Store_Database();
copilot/fix-10
        
        // Initialize frontend functionality
        new Amal_Store_Frontend();
=======
        $this->admin = new Amal_Store_Admin();
 main
    }
}