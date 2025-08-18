<?php
/**
 * Amal Profile Management Template
 * 
 * @package AmalProfileManagement
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get current user data
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get user meta data
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$address = get_user_meta($user_id, 'address', true);

// Get pets, services, bookings, and orders (these would be from custom tables or post types)
$pets = apply_filters('amal_get_user_pets', [], $user_id);
$services = apply_filters('amal_get_user_services', [], $user_id);
$bookings = apply_filters('amal_get_user_bookings', [], $user_id);
$orders = apply_filters('amal_get_user_orders', [], $user_id);
?>

<div class="amal-profile-container">
    <div class="amal-profile-header">
        <h1 class="amal-profile-title"><?php esc_html_e('Amal Profile Management System', 'amal-profile-management'); ?></h1>
        <p class="amal-profile-subtitle"><?php esc_html_e('Complete user profile, pet, and service management for the Amal pet services platform', 'amal-profile-management'); ?></p>
    </div>

    <div class="amal-feature-grid">
        <div class="amal-feature-card">
            <h3 class="amal-feature-title"><?php esc_html_e('Profile Management', 'amal-profile-management'); ?></h3>
            <ul class="amal-feature-list">
                <li><?php esc_html_e('Personal information editing', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Profile picture upload', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Notification preferences', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Subscription management', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Contact information', 'amal-profile-management'); ?></li>
            </ul>
        </div>
        
        <div class="amal-feature-card">
            <h3 class="amal-feature-title"><?php esc_html_e('Pet Management', 'amal-profile-management'); ?></h3>
            <ul class="amal-feature-list">
                <li><?php esc_html_e('Add, edit, delete pets', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Pet photos and details', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Health notes tracking', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Age and weight monitoring', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Activity history', 'amal-profile-management'); ?></li>
            </ul>
        </div>
        
        <div class="amal-feature-card">
            <h3 class="amal-feature-title"><?php esc_html_e('Service Provider Tools', 'amal-profile-management'); ?></h3>
            <ul class="amal-feature-list">
                <li><?php esc_html_e('Service creation and editing', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Pricing and availability', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Category management', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Location settings', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Service status control', 'amal-profile-management'); ?></li>
            </ul>
        </div>
        
        <div class="amal-feature-card">
            <h3 class="amal-feature-title"><?php esc_html_e('Booking Management', 'amal-profile-management'); ?></h3>
            <ul class="amal-feature-list">
                <li><?php esc_html_e('Booking history view', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Status tracking', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Pet association', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Payment records', 'amal-profile-management'); ?></li>
                <li><?php esc_html_e('Service details', 'amal-profile-management'); ?></li>
            </ul>
        </div>
    </div>

    <div class="amal-interface-preview">
        <div class="amal-tab-nav">
            <button class="amal-tab-btn active" data-tab="profile"><?php esc_html_e('Profile Info', 'amal-profile-management'); ?></button>
            <button class="amal-tab-btn" data-tab="pets"><?php esc_html_e('My Pets', 'amal-profile-management'); ?></button>
            <button class="amal-tab-btn" data-tab="services"><?php esc_html_e('My Services', 'amal-profile-management'); ?></button>
            <button class="amal-tab-btn" data-tab="orders"><?php esc_html_e('My Orders', 'amal-profile-management'); ?></button>
            <button class="amal-tab-btn" data-tab="bookings"><?php esc_html_e('My Bookings', 'amal-profile-management'); ?></button>
        </div>
        
        <div class="amal-tab-content active" id="amal-tab-profile">
            <h3><?php esc_html_e('Profile Information', 'amal-profile-management'); ?></h3>
            <form id="amal-profile-form" method="post">
                <?php wp_nonce_field('amal_update_profile', 'amal_profile_nonce'); ?>
                <div class="amal-form-demo">
                    <div class="amal-form-group">
                        <label for="first_name"><?php esc_html_e('First Name', 'amal-profile-management'); ?></label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($first_name); ?>" required>
                    </div>
                    <div class="amal-form-group">
                        <label for="last_name"><?php esc_html_e('Last Name', 'amal-profile-management'); ?></label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($last_name); ?>" required>
                    </div>
                    <div class="amal-form-group">
                        <label for="email"><?php esc_html_e('Email', 'amal-profile-management'); ?></label>
                        <input type="email" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                    </div>
                    <div class="amal-form-group">
                        <label for="phone"><?php esc_html_e('Phone', 'amal-profile-management'); ?></label>
                        <input type="tel" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>">
                    </div>
                </div>
                <div class="amal-form-group">
                    <label for="address"><?php esc_html_e('Address', 'amal-profile-management'); ?></label>
                    <textarea id="address" name="address" rows="3"><?php echo esc_textarea($address); ?></textarea>
                </div>
                <button type="submit" class="amal-btn"><?php esc_html_e('Update Profile', 'amal-profile-management'); ?></button>
            </form>
        </div>
        
        <div class="amal-tab-content" id="amal-tab-pets">
            <h3><?php esc_html_e('My Pets', 'amal-profile-management'); ?></h3>
            <div class="amal-pets-container">
                <?php if (!empty($pets)) : ?>
                    <?php foreach ($pets as $pet) : ?>
                        <div class="amal-pet-card" data-id="<?php echo esc_attr($pet['id']); ?>">
                            <h4 class="amal-card-title"><?php echo esc_html($pet['name']); ?></h4>
                            <p class="amal-card-info"><strong><?php esc_html_e('Type:', 'amal-profile-management'); ?></strong> <?php echo esc_html($pet['type']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Breed:', 'amal-profile-management'); ?></strong> <?php echo esc_html($pet['breed']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Age:', 'amal-profile-management'); ?></strong> <?php echo esc_html($pet['age']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Weight:', 'amal-profile-management'); ?></strong> <?php echo esc_html($pet['weight']); ?></p>
                            <button class="amal-btn amal-btn-edit" data-type="pet" data-id="<?php echo esc_attr($pet['id']); ?>"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                            <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="pet" data-id="<?php echo esc_attr($pet['id']); ?>"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="amal-pet-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Buddy', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Type:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Dog', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Breed:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Golden Retriever', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Age:', 'amal-profile-management'); ?></strong> <?php esc_html_e('3 years', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Weight:', 'amal-profile-management'); ?></strong> <?php esc_html_e('65 lbs', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-edit" data-type="pet" data-id="1"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                        <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="pet" data-id="1"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                    </div>
                    <div class="amal-pet-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Whiskers', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Type:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Cat', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Breed:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Persian', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Age:', 'amal-profile-management'); ?></strong> <?php esc_html_e('2 years', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Weight:', 'amal-profile-management'); ?></strong> <?php esc_html_e('8 lbs', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-edit" data-type="pet" data-id="2"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                        <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="pet" data-id="2"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
            <button class="amal-btn amal-btn-add-new" data-type="pet"><?php esc_html_e('Add New Pet', 'amal-profile-management'); ?></button>
        </div>
        
        <div class="amal-tab-content" id="amal-tab-services">
            <h3><?php esc_html_e('My Services', 'amal-profile-management'); ?></h3>
            <div class="amal-services-container">
                <?php if (!empty($services)) : ?>
                    <?php foreach ($services as $service) : ?>
                        <div class="amal-service-card" data-id="<?php echo esc_attr($service['id']); ?>">
                            <h4 class="amal-card-title"><?php echo esc_html($service['name']); ?></h4>
                            <p class="amal-card-info"><strong><?php esc_html_e('Category:', 'amal-profile-management'); ?></strong> <?php echo esc_html($service['category']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Price:', 'amal-profile-management'); ?></strong> <?php echo esc_html($service['price']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Location:', 'amal-profile-management'); ?></strong> <?php echo esc_html($service['location']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php echo esc_html($service['status']); ?></p>
                            <button class="amal-btn amal-btn-edit" data-type="service" data-id="<?php echo esc_attr($service['id']); ?>"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                            <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="service" data-id="<?php echo esc_attr($service['id']); ?>"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="amal-service-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Professional Dog Walking', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Category:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Dog Walking', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Price:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$25/hour', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Location:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Downtown Area', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Active', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-edit" data-type="service" data-id="1"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                        <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="service" data-id="1"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                    </div>
                    <div class="amal-service-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Pet Sitting Service', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Category:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Pet Sitting', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Price:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$40/day', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Location:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Your Home', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Active', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-edit" data-type="service" data-id="2"><?php esc_html_e('Edit', 'amal-profile-management'); ?></button>
                        <button class="amal-btn amal-btn-danger amal-btn-delete" data-type="service" data-id="2"><?php esc_html_e('Delete', 'amal-profile-management'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
            <button class="amal-btn amal-btn-add-new" data-type="service"><?php esc_html_e('Add New Service', 'amal-profile-management'); ?></button>
        </div>
        
        <div class="amal-tab-content" id="amal-tab-orders">
            <h3><?php esc_html_e('My Orders', 'amal-profile-management'); ?></h3>
            <div class="amal-orders-container">
                <?php if (!empty($orders)) : ?>
                    <?php foreach ($orders as $order) : ?>
                        <?php 
                        $status_class = 'amal-status-' . strtolower($order['status']);
                        $order_date = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
                        ?>
                        <div class="amal-order-card" data-id="<?php echo esc_attr($order['id']); ?>">
                            <div class="amal-order-header">
                                <h4 class="amal-card-title"><?php echo sprintf(esc_html__('Order #%d', 'amal-profile-management'), $order['id']); ?></h4>
                                <span class="amal-order-status <?php echo esc_attr($status_class); ?>"><?php echo esc_html(ucfirst($order['status'])); ?></span>
                            </div>
                            <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php echo esc_html($order_date); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> $<?php echo esc_html(number_format($order['total_price'], 2)); ?></p>
                            <button class="amal-btn amal-btn-view-order" data-order-id="<?php echo esc_attr($order['id']); ?>"><?php esc_html_e('View Details', 'amal-profile-management'); ?></button>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="amal-order-card">
                        <div class="amal-order-header">
                            <h4 class="amal-card-title"><?php esc_html_e('Order #1', 'amal-profile-management'); ?></h4>
                            <span class="amal-order-status amal-status-delivered"><?php esc_html_e('Delivered', 'amal-profile-management'); ?></span>
                        </div>
                        <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php esc_html_e('August 15, 2024 at 10:30 AM', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$89.99', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-view-order" data-order-id="1"><?php esc_html_e('View Details', 'amal-profile-management'); ?></button>
                    </div>
                    <div class="amal-order-card">
                        <div class="amal-order-header">
                            <h4 class="amal-card-title"><?php esc_html_e('Order #2', 'amal-profile-management'); ?></h4>
                            <span class="amal-order-status amal-status-shipped"><?php esc_html_e('Shipped', 'amal-profile-management'); ?></span>
                        </div>
                        <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php esc_html_e('August 20, 2024 at 9:15 AM', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$124.50', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-view-order" data-order-id="2"><?php esc_html_e('View Details', 'amal-profile-management'); ?></button>
                    </div>
                    <div class="amal-order-card">
                        <div class="amal-order-header">
                            <h4 class="amal-card-title"><?php esc_html_e('Order #3', 'amal-profile-management'); ?></h4>
                            <span class="amal-order-status amal-status-processing"><?php esc_html_e('Processing', 'amal-profile-management'); ?></span>
                        </div>
                        <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php esc_html_e('August 22, 2024 at 2:22 PM', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Total:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$45.99', 'amal-profile-management'); ?></p>
                        <button class="amal-btn amal-btn-view-order" data-order-id="3"><?php esc_html_e('View Details', 'amal-profile-management'); ?></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="amal-tab-content" id="amal-tab-bookings">
            <h3><?php esc_html_e('My Bookings', 'amal-profile-management'); ?></h3>
            <div class="amal-bookings-container">
                <?php if (!empty($bookings)) : ?>
                    <?php foreach ($bookings as $booking) : ?>
                        <div class="amal-pet-card" data-id="<?php echo esc_attr($booking['id']); ?>">
                            <h4 class="amal-card-title"><?php echo esc_html($booking['service_name']); ?></h4>
                            <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php echo esc_html($booking['date']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Pet:', 'amal-profile-management'); ?></strong> <?php echo esc_html($booking['pet_name']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Amount:', 'amal-profile-management'); ?></strong> <?php echo esc_html($booking['amount']); ?></p>
                            <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php echo esc_html($booking['status']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="amal-pet-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Dog Walking Service', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php esc_html_e('August 20, 2024 at 3:00 PM', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Pet:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Buddy', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Amount:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$25.00', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Confirmed', 'amal-profile-management'); ?></p>
                    </div>
                    <div class="amal-pet-card">
                        <h4 class="amal-card-title"><?php esc_html_e('Grooming Service', 'amal-profile-management'); ?></h4>
                        <p class="amal-card-info"><strong><?php esc_html_e('Date:', 'amal-profile-management'); ?></strong> <?php esc_html_e('August 15, 2024 at 10:00 AM', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Pet:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Whiskers', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Amount:', 'amal-profile-management'); ?></strong> <?php esc_html_e('$45.00', 'amal-profile-management'); ?></p>
                        <p class="amal-card-info"><strong><?php esc_html_e('Status:', 'amal-profile-management'); ?></strong> <?php esc_html_e('Completed', 'amal-profile-management'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="amal-implementation-info">
        <h3 class="amal-implementation-title"><?php esc_html_e('Implementation Details', 'amal-profile-management'); ?></h3>
        <p><strong><?php esc_html_e('WordPress Shortcode:', 'amal-profile-management'); ?></strong></p>
        <div class="amal-code-block">[amal_profile_management]</div>
        
        <p><strong><?php esc_html_e('PHP Usage:', 'amal-profile-management'); ?></strong></p>
        <div class="amal-code-block">
<?php echo esc_html('if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $pets = apply_filters(\'amal_get_user_pets\', [], $user->ID);
    $services = apply_filters(\'amal_get_user_services\', [], $user->ID);
    $bookings = apply_filters(\'amal_get_user_bookings\', [], $user->ID);
}'); ?>
        </div>
        
        <p><strong><?php esc_html_e('Theme Integration:', 'amal-profile-management'); ?></strong></p>
        <div class="amal-code-block">
<?php echo esc_html('{!! do_shortcode(\'[amal_profile_management]\') !!}'); ?>
        </div>
        
        <p><strong><?php esc_html_e('Features:', 'amal-profile-management'); ?></strong></p>
        <ul>
            <li><?php esc_html_e('Fully responsive design with mobile-first approach', 'amal-profile-management'); ?></li>
            <li><?php esc_html_e('AJAX-powered interface for smooth user experience', 'amal-profile-management'); ?></li>
            <li><?php esc_html_e('Secure file upload for profile and pet images', 'amal-profile-management'); ?></li>
            <li><?php esc_html_e('Role-based access control (service provider features)', 'amal-profile-management'); ?></li>
            <li><?php esc_html_e('Real-time form validation and feedback', 'amal-profile-management'); ?></li>
            <li><?php esc_html_e('WordPress nonce security for all operations', 'amal-profile-management'); ?></li>
        </ul>
    </div>
</div>