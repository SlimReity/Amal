<?php
/**
 * Main plugin class for Amal Store
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store {
    
    public function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once AMAL_STORE_PLUGIN_DIR . 'includes/class-amal-store-database.php';
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Plugin initialization logic
        $this->database = new Amal_Store_Database();
    }
}