<?php
/**
 * Amal Store - Sample Items Population Script
 * 
 * This script populates the store with comprehensive sample items
 * for testing and demonstration purposes.
 * 
 * Usage: 
 * - Via WP_CLI: wp amal-store populate [--clear]
 * - Via WordPress admin or direct inclusion
 * - Via URL parameters (for testing)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress context, try to load WordPress
    $wp_load_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once $wp_load_path;
    } else {
        die('WordPress not found. Please run this script from WordPress admin or ensure wp-load.php is accessible.');
    }
}
class Amal_Store_Sample_Items {
    protected $wpdb;
    protected $items_table;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->items_table = $wpdb->prefix . 'amal_store_items';
    }

    public function get_sample_items() {
        return array(
            // Dog Products
            array(
                'title' => 'Premium Dry Dog Food - Adult Formula',
                'category' => 'Food',
                'description' => 'High-quality dry dog food formulated for adult dogs. Contains real chicken as the first ingredient, essential vitamins, and minerals for optimal health. Suitable for medium to large breed dogs.',
                'price' => 42.00,
                'stock_qty' => 100,
                'image_url' => 'https://via.placeholder.com/400x300/667eea/ffffff?text=Premium+Dog+Food',
                'is_active' => 1
            ),
            array(
                'title' => 'Leather Dog Leash - 6ft',
                'category' => 'Accessories',
                'description' => 'Durable genuine leather dog leash, 6 feet long. Features comfortable padded handle and strong metal clasp. Perfect for daily walks and training.',
                'price' => 22.00,
                'stock_qty' => 50,
                'image_url' => 'https://via.placeholder.com/400x300/f39c12/ffffff?text=Dog+Leash',
                'is_active' => 1
            ),
            array(
                'title' => 'Interactive Dog Toy Set',
                'category' => 'Toys',
                'description' => 'Set of 5 interactive dog toys including rope toy, squeaky ball, puzzle feeder, and chew bones. Perfect for keeping your dog entertained and mentally stimulated.',
                'price' => 29.90,
                'stock_qty' => 75,
                'image_url' => 'https://via.placeholder.com/400x300/e74c3c/ffffff?text=Dog+Toys',
                'is_active' => 1
            ),
            array(
                'title' => 'Orthopedic Dog Bed - Large',
                'category' => 'Housing',
                'description' => 'Memory foam orthopedic dog bed designed for senior dogs or dogs with joint issues. Removable, machine-washable cover. Available in large size.',
                'price' => 79.90,
                'stock_qty' => 20,
                'image_url' => 'https://via.placeholder.com/400x300/9b59b6/ffffff?text=Dog+Bed',
                'is_active' => 1
            ),
            // Cat Products
            array(
                'title' => 'Automatic Cat Litter Box',
                'category' => 'Accessories',
                'description' => 'Self-cleaning automatic litter box with odor control technology. Features waste compartment, tracking mat, and quiet operation. Suitable for cats up to 15 lbs.',
                'price' => 79.90,
                'stock_qty' => 25,
                'image_url' => 'https://via.placeholder.com/400x300/764ba2/ffffff?text=Cat+Litter+Box',
                'is_active' => 1
            ),
            array(
                'title' => 'Premium Cat Food - Salmon & Rice',
                'category' => 'Food',
                'description' => 'Grain-free premium cat food with real salmon and rice. Supports healthy skin, coat, and digestion. Formulated for adult cats with sensitive stomachs.',
                'price' => 35.50,
                'stock_qty' => 80,
                'image_url' => 'https://via.placeholder.com/400x300/2ecc71/ffffff?text=Cat+Food',
                'is_active' => 1
            ),
            array(
                'title' => 'Interactive Cat Toys Bundle',
                'category' => 'Toys',
                'description' => 'Collection of interactive cat toys including feather wand, laser pointer, catnip mice, and puzzle feeders. Keeps indoor cats active and engaged.',
                'price' => 18.50,
                'stock_qty' => 60,
                'image_url' => 'https://via.placeholder.com/400x300/3498db/ffffff?text=Cat+Toys',
                'is_active' => 1
            ),
            array(
                'title' => 'Multi-Level Cat Tree Tower',
                'category' => 'Housing',
                'description' => 'Large multi-level cat tree with scratching posts, perches, and hiding spots. Covered in soft plush fabric. Perfect for multiple cats or active climbers.',
                'price' => 115.00,
                'stock_qty' => 15,
                'image_url' => 'https://via.placeholder.com/400x300/95a5a6/ffffff?text=Cat+Tree',
                'is_active' => 1
            ),
            // Bird Products
            array(
                'title' => 'Large Bird Cage with Stand',
                'category' => 'Housing',
                'description' => 'Spacious bird cage suitable for medium to large birds. Includes multiple perches, feeding stations, and removable bottom tray. Comes with wheeled stand.',
                'price' => 135.00,
                'stock_qty' => 15,
                'image_url' => 'https://via.placeholder.com/400x300/4ecdc4/ffffff?text=Bird+Cage',
                'is_active' => 1
            ),
            array(
                'title' => 'Premium Bird Seed Mix',
                'category' => 'Food',
                'description' => 'Nutritious seed mix for parrots and large birds. Contains sunflower seeds, millet, safflower, and dried fruits. Supports optimal health and vibrant feathers.',
                'price' => 21.00,
                'stock_qty' => 45,
                'image_url' => 'https://via.placeholder.com/400x300/f39c12/ffffff?text=Bird+Food',
                'is_active' => 1
            ),
            array(
                'title' => 'Bird Toys Variety Pack',
                'category' => 'Toys',
                'description' => 'Assorted bird toys for mental stimulation and beak exercise. Includes wooden blocks, rope toys, bells, and foraging toys. Safe for all bird species.',
                'price' => 14.50,
                'stock_qty' => 40,
                'image_url' => 'https://via.placeholder.com/400x300/e67e22/ffffff?text=Bird+Toys',
                'is_active' => 1
            ),
            // Fish/Aquarium Products
            array(
                'title' => 'Advanced Aquarium Filter System',
                'category' => 'Aquarium',
                'description' => 'High-performance filtration system for aquariums up to 75 gallons. Features biological, mechanical, and chemical filtration. Quiet operation with adjustable flow.',
                'price' => 61.00,
                'stock_qty' => 30,
                'image_url' => 'https://via.placeholder.com/400x300/44bd32/ffffff?text=Fish+Filter',
                'is_active' => 1
            ),
            array(
                'title' => 'LED Aquarium Light Strip',
                'category' => 'Aquarium',
                'description' => 'Full-spectrum LED lighting system for planted aquariums. Programmable day/night cycles, supports plant growth and enhances fish colors. Energy efficient.',
                'price' => 49.50,
                'stock_qty' => 35,
                'image_url' => 'https://via.placeholder.com/400x300/1abc9c/ffffff?text=Aquarium+Light',
                'is_active' => 1
            ),
            array(
                'title' => 'Tropical Fish Food Flakes',
                'category' => 'Food',
                'description' => 'Complete nutrition tropical fish food flakes. Enhances color, supports immune system, and promotes healthy growth. Suitable for all tropical fish species.',
                'price' => 11.90,
                'stock_qty' => 90,
                'image_url' => 'https://via.placeholder.com/400x300/2980b9/ffffff?text=Fish+Food',
                'is_active' => 1
            ),
            array(
                'title' => 'Aquarium Decoration Set',
                'category' => 'Aquarium',
                'description' => 'Beautiful aquarium decoration set including artificial plants, driftwood, and decorative stones. Creates natural-looking underwater landscape.',
                'price' => 27.00,
                'stock_qty' => 25,
                'image_url' => 'https://via.placeholder.com/400x300/8e44ad/ffffff?text=Aquarium+Decor',
                'is_active' => 1
            ),
            // Small Pet Products
            array(
                'title' => 'Hamster Cage Starter Kit',
                'category' => 'Housing',
                'description' => 'Complete hamster habitat including cage, water bottle, food dish, exercise wheel, and bedding. Perfect starter kit for new hamster owners.',
                'price' => 72.00,
                'stock_qty' => 20,
                'image_url' => 'https://via.placeholder.com/400x300/d35400/ffffff?text=Hamster+Cage',
                'is_active' => 1
            ),
            array(
                'title' => 'Small Pet Food Mix',
                'category' => 'Food',
                'description' => 'Nutritionally balanced food mix for rabbits, guinea pigs, and other small pets. Contains timothy hay, pellets, and dried vegetables.',
                'price' => 15.50,
                'stock_qty' => 55,
                'image_url' => 'https://via.placeholder.com/400x300/27ae60/ffffff?text=Small+Pet+Food',
                'is_active' => 1
            ),
            array(
                'title' => 'Rabbit Exercise Playpen',
                'category' => 'Accessories',
                'description' => 'Expandable exercise playpen for rabbits and small pets. Easy assembly, foldable design. Provides safe outdoor exercise area.',
                'price' => 45.00,
                'stock_qty' => 18,
                'image_url' => 'https://via.placeholder.com/400x300/16a085/ffffff?text=Pet+Playpen',
                'is_active' => 1
            ),
            // Health & Grooming
            array(
                'title' => 'Pet Grooming Kit Professional',
                'category' => 'Health',
                'description' => 'Professional-grade grooming kit including clippers, scissors, brushes, nail clippers, and storage case. Suitable for dogs and cats.',
                'price' => 67.50,
                'stock_qty' => 25,
                'image_url' => 'https://via.placeholder.com/400x300/c0392b/ffffff?text=Grooming+Kit',
                'is_active' => 1
            ),
            array(
                'title' => 'Pet Vitamins & Supplements',
                'category' => 'Health',
                'description' => 'Daily multivitamin supplements for dogs and cats. Supports joint health, immune system, and coat shine. Veterinarian recommended formula.',
                'price' => 26.50,
                'stock_qty' => 40,
                'image_url' => 'https://via.placeholder.com/400x300/7f8c8d/ffffff?text=Pet+Vitamins',
                'is_active' => 1
            ),
            // Out of Stock Item (for testing)
            array(
                'title' => 'Limited Edition Pet Carrier',
                'category' => 'Accessories',
                'description' => 'Premium pet carrier with airline approval. Features ventilation windows, comfortable padding, and secure locking mechanism. Currently out of stock.',
                'price' => 79.90,
                'stock_qty' => 0,
                'image_url' => 'https://via.placeholder.com/400x300/95a5a6/ffffff?text=Pet+Carrier+SOLD+OUT',
                'is_active' => 1
            )
        );
    }

    public function table_exists() {
        return $this->wpdb->get_var("SHOW TABLES LIKE '{$this->items_table}'") == $this->items_table;
    }

    public function clear_existing_items() {
        $result = $this->wpdb->query("DELETE FROM {$this->items_table}");
        return $result !== false;
    }

    public function populate_items($clear_existing = false) {
        if (!$this->table_exists()) {
            return array(
                'success' => false,
                'message' => 'Items table does not exist. Please activate the Amal Store plugin first.',
                'items_added' => 0
            );
        }

        if ($clear_existing) {
            $this->clear_existing_items();
        }

        $items = $this->get_sample_items();
        $items_added = 0;
        $items_skipped = 0;
        $errors = array();

        foreach ($items as $item) {
            $existing = $this->wpdb->get_var($this->wpdb->prepare(
                "SELECT id FROM {$this->items_table} WHERE title = %s",
                $item['title']
            ));

            if ($existing && !$clear_existing) {
                $items_skipped++;
                continue;
            }

            $result = $this->wpdb->insert(
                $this->items_table,
                array(
                    'title' => $item['title'],
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'stock_qty' => $item['stock_qty'],
                    'image_url' => $item['image_url'],
                    'is_active' => $item['is_active']
                ),
                array('%s', '%s', '%s', '%f', '%d', '%s', '%d')
            );

            if ($result !== false) {
                $items_added++;
            } else {
                $errors[] = "Failed to insert: " . $item['title'];
            }
        }

        return array(
            'success' => true,
            'message' => "Successfully populated store with sample items.",
            'items_added' => $items_added,
            'items_skipped' => $items_skipped,
            'total_items' => count($items),
            'errors' => $errors
        );
    }

    public function get_items_summary() {
        if (!$this->table_exists()) {
            return array('error' => 'Items table does not exist');
        }

        $total = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->items_table}");
        $active = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->items_table} WHERE is_active = 1");
        $categories = $this->wpdb->get_results("SELECT category, COUNT(*) as count FROM {$this->items_table} GROUP BY category");

        return array(
            'total_items' => intval($total),
            'active_items' => intval($active),
            'categories' => $categories
        );
    }
}

// If running directly (not included)
if (!defined('WP_CLI') && isset($_GET['action'])) {
    $populator = new Amal_Store_Sample_Items();
    
    switch ($_GET['action']) {
        case 'populate':
            $clear = isset($_GET['clear']) && $_GET['clear'] === 'true';
            $result = $populator->populate_items($clear);
            break;
        case 'summary':
            $result = $populator->get_items_summary();
            break;
        default:
            $result = array('error' => 'Invalid action');
    }
    
    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}

// If included in another file, just make the class available