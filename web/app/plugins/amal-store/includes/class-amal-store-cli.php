<?php
/**
 * WP_CLI Commands for Amal Store
 * 
 * Provides command-line interface for managing store items and operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Amal_Store_CLI {
    
    /**
     * Populate the store with sample items
     * 
     * ## OPTIONS
     * 
     * [--clear]
     * : Clear existing items before populating
     * 
     * ## EXAMPLES
     * 
     *     wp amal-store populate
     *     wp amal-store populate --clear
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function populate($args, $assoc_args) {
        // Load the sample items class
        require_once AMAL_STORE_PLUGIN_DIR . 'populate-sample-items.php';
        
        $populator = new Amal_Store_Sample_Items();
        $clear_existing = isset($assoc_args['clear']) && $assoc_args['clear'];
        
        WP_CLI::log('Starting store population...');
        
        $result = $populator->populate_items($clear_existing);
        
        if ($result['success']) {
            WP_CLI::success(
                sprintf(
                    '%s Items added: %d, Skipped: %d, Total available: %d',
                    $result['message'],
                    $result['items_added'],
                    $result['items_skipped'],
                    $result['total_items']
                )
            );
            
            if (!empty($result['errors'])) {
                WP_CLI::warning("Some items failed to insert:");
                foreach ($result['errors'] as $error) {
                    WP_CLI::log("  - " . $error);
                }
            }
        } else {
            WP_CLI::error($result['message']);
        }
    }
    
    /**
     * Get summary of current store items
     * 
     * ## EXAMPLES
     * 
     *     wp amal-store summary
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function summary($args, $assoc_args) {
        // Load the sample items class
        require_once AMAL_STORE_PLUGIN_DIR . 'populate-sample-items.php';
        
        $populator = new Amal_Store_Sample_Items();
        $result = $populator->get_items_summary();
        
        if (isset($result['error'])) {
            WP_CLI::error($result['error']);
            return;
        }
        
        WP_CLI::log('Store Items Summary:');
        WP_CLI::log('==================');
        WP_CLI::log(sprintf('Total Items: %d', $result['total_items']));
        WP_CLI::log(sprintf('Active Items: %d', $result['active_items']));
        
        if (!empty($result['categories'])) {
            WP_CLI::log('');
            WP_CLI::log('Items by Category:');
            foreach ($result['categories'] as $category) {
                WP_CLI::log(sprintf('  - %s: %d items', $category->category, $category->count));
            }
        }
        
        WP_CLI::success('Summary retrieved successfully.');
    }
    
    /**
     * Clear all existing items from the store
     * 
     * ## OPTIONS
     * 
     * [--yes]
     * : Skip confirmation prompt
     * 
     * ## EXAMPLES
     * 
     *     wp amal-store clear
     *     wp amal-store clear --yes
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function clear($args, $assoc_args) {
        // Load the sample items class
        require_once AMAL_STORE_PLUGIN_DIR . 'populate-sample-items.php';
        
        $populator = new Amal_Store_Sample_Items();
        
        // Check if table exists
        if (!$populator->table_exists()) {
            WP_CLI::error('Items table does not exist. Please activate the Amal Store plugin first.');
            return;
        }
        
        // Get current count
        $summary = $populator->get_items_summary();
        $current_count = $summary['total_items'];
        
        if ($current_count === 0) {
            WP_CLI::log('No items to clear. Store is already empty.');
            return;
        }
        
        // Confirmation prompt unless --yes flag is used
        if (!isset($assoc_args['yes'])) {
            WP_CLI::confirm(sprintf('Are you sure you want to delete all %d items from the store?', $current_count));
        }
        
        WP_CLI::log('Clearing existing items...');
        
        $result = $populator->clear_existing_items();
        
        if ($result) {
            WP_CLI::success(sprintf('Successfully cleared %d items from the store.', $current_count));
        } else {
            WP_CLI::error('Failed to clear items from the store.');
        }
    }
    
    /**
     * Get list of available sample items (without adding to database)
     * 
     * ## EXAMPLES
     * 
     *     wp amal-store list-samples
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function list_samples($args, $assoc_args) {
        // Load the sample items class
        require_once AMAL_STORE_PLUGIN_DIR . 'populate-sample-items.php';
        
        $populator = new Amal_Store_Sample_Items();
        $items = $populator->get_sample_items();
        
        WP_CLI::log('Available Sample Items:');
        WP_CLI::log('======================');
        
        foreach ($items as $index => $item) {
            WP_CLI::log(sprintf(
                '%d. %s (Category: %s, Price: %.2f CHF, Stock: %d)',
                $index + 1,
                $item['title'],
                $item['category'],
                $item['price'],
                $item['stock_qty']
            ));
        }
        
        WP_CLI::success(sprintf('Listed %d available sample items.', count($items)));
    }
}