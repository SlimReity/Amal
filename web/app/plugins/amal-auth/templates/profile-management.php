<?php
/**
 * Profile Management Template
 * This template displays the user profile management interface
 * 
 * Variables available:
 * $user - Current user object
 * $pets - Array of user's pets
 * $services - Array of user's services (if service provider)
 * $bookings - Array of user's bookings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="amal-profile-management">
    <div class="profile-header">
        <h2>My Profile</h2>
        <p>Welcome back, <?php echo esc_html($user->first_name ?: $user->email); ?>!</p>
    </div>
    
    <div class="profile-tabs">
        <nav class="tab-nav">
            <button class="tab-btn active" data-tab="profile-info">Profile Info</button>
            <button class="tab-btn" data-tab="my-pets">My Pets</button>
            <?php if (method_exists('AmalAuthHelper', 'is_service_provider') && AmalAuthHelper::is_service_provider()): ?>
                <button class="tab-btn" data-tab="my-services">My Services</button>
            <?php endif; ?>
            <button class="tab-btn" data-tab="my-bookings">My Bookings</button>
        </nav>
        
        <!-- Profile Info Tab -->
        <div class="tab-content active" id="profile-info">
            <div class="profile-section">
                <h3>Personal Information</h3>
                <form id="profile-form" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($user->first_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($user->last_name); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email (read-only)</label>
                            <input type="email" id="email" name="email" value="<?php echo esc_attr($user->email); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo esc_attr($user->phone ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo esc_textarea($user->address ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <div class="image-upload-container">
                            <input type="file" id="profile_picture_file" accept="image/*" style="display: none;">
                            <button type="button" class="upload-btn" onclick="document.getElementById('profile_picture_file').click()">Choose Image</button>
                            <input type="url" id="profile_picture" name="profile_picture" value="<?php echo esc_attr($user->profile_picture ?? ''); ?>" placeholder="Image URL" readonly>
                            <?php if (!empty($user->profile_picture)): ?>
                                <img src="<?php echo esc_url($user->profile_picture); ?>" alt="Profile Picture" class="profile-preview">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h4>Notification Preferences</h4>
                    <div class="form-row">
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="notification_email" value="1" <?php checked($user->notification_email ?? 1, 1); ?>>
                                Email Notifications
                            </label>
                        </div>
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="notification_push" value="1" <?php checked($user->notification_push ?? 1, 1); ?>>
                                Push Notifications
                            </label>
                        </div>
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" name="notification_sms" value="1" <?php checked($user->notification_sms ?? 0, 1); ?>>
                                SMS Notifications
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subscription_type">Subscription</label>
                        <select id="subscription_type" name="subscription_type">
                            <option value="free" <?php selected($user->subscription_type ?? 'free', 'free'); ?>>Free</option>
                            <option value="premium" <?php selected($user->subscription_type ?? 'free', 'premium'); ?>>Premium</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
        
        <!-- My Pets Tab -->
        <div class="tab-content" id="my-pets">
            <div class="pets-section">
                <div class="section-header">
                    <h3>My Pets</h3>
                    <button class="btn btn-primary" id="add-pet-btn">Add New Pet</button>
                </div>
                
                <div class="pets-grid">
                    <?php if (empty($pets)): ?>
                        <p class="no-items">You haven't added any pets yet. Click "Add New Pet" to get started!</p>
                    <?php else: ?>
                        <?php foreach ($pets as $pet): ?>
                            <div class="pet-card" data-pet-id="<?php echo $pet->id; ?>">
                                <?php if (!empty($pet->photo_url)): ?>
                                    <img src="<?php echo esc_url($pet->photo_url); ?>" alt="<?php echo esc_attr($pet->name); ?>" class="pet-photo">
                                <?php endif; ?>
                                <div class="pet-info">
                                    <h4><?php echo esc_html($pet->name); ?></h4>
                                    <p><strong>Type:</strong> <?php echo esc_html($pet->type); ?></p>
                                    <p><strong>Breed:</strong> <?php echo esc_html($pet->breed); ?></p>
                                    <?php if ($pet->age): ?>
                                        <p><strong>Age:</strong> <?php echo esc_html($pet->age); ?> years</p>
                                    <?php endif; ?>
                                    <?php if ($pet->weight): ?>
                                        <p><strong>Weight:</strong> <?php echo esc_html($pet->weight); ?> kg</p>
                                    <?php endif; ?>
                                </div>
                                <div class="pet-actions">
                                    <button class="btn btn-small edit-pet-btn" data-pet-id="<?php echo $pet->id; ?>">Edit</button>
                                    <button class="btn btn-small btn-danger delete-pet-btn" data-pet-id="<?php echo $pet->id; ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- My Services Tab (Service Providers Only) -->
        <?php if (AmalAuthHelper::is_service_provider()): ?>
        <div class="tab-content" id="my-services">
            <div class="services-section">
                <div class="section-header">
                    <h3>My Services</h3>
                    <button class="btn btn-primary" id="add-service-btn">Add New Service</button>
                </div>
                
                <div class="services-grid">
                    <?php if (empty($services)): ?>
                        <p class="no-items">You haven't added any services yet. Click "Add New Service" to start offering your services!</p>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <div class="service-card" data-service-id="<?php echo $service->id; ?>">
                                <div class="service-info">
                                    <h4><?php echo esc_html($service->title); ?></h4>
                                    <p class="service-category"><?php echo esc_html($service->category); ?></p>
                                    <p class="service-price">$<?php echo esc_html(number_format($service->price, 2)); ?></p>
                                    <p class="service-description"><?php echo esc_html(wp_trim_words($service->description, 20)); ?></p>
                                    <?php if (!empty($service->location)): ?>
                                        <p><strong>Location:</strong> <?php echo esc_html($service->location); ?></p>
                                    <?php endif; ?>
                                    <p class="service-status">
                                        <span class="status-badge <?php echo $service->is_active ? 'active' : 'inactive'; ?>">
                                            <?php echo $service->is_active ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="service-actions">
                                    <button class="btn btn-small edit-service-btn" data-service-id="<?php echo $service->id; ?>">Edit</button>
                                    <button class="btn btn-small btn-danger delete-service-btn" data-service-id="<?php echo $service->id; ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- My Bookings Tab -->
        <div class="tab-content" id="my-bookings">
            <div class="bookings-section">
                <h3>My Bookings</h3>
                
                <div class="bookings-list">
                    <?php if (empty($bookings)): ?>
                        <p class="no-items">You haven't made any bookings yet.</p>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <div class="booking-card">
                                <div class="booking-info">
                                    <h4><?php echo esc_html($booking->service_title); ?></h4>
                                    <p><strong>Category:</strong> <?php echo esc_html($booking->service_category); ?></p>
                                    <p><strong>Date:</strong> <?php echo esc_html(date('F j, Y g:i A', strtotime($booking->booking_date))); ?></p>
                                    <p><strong>Amount:</strong> $<?php echo esc_html(number_format($booking->total_amount, 2)); ?></p>
                                    <?php if (!empty($booking->pet_name)): ?>
                                        <p><strong>Pet:</strong> <?php echo esc_html($booking->pet_name); ?></p>
                                    <?php endif; ?>
                                    <p class="booking-status">
                                        <span class="status-badge <?php echo esc_attr($booking->status); ?>">
                                            <?php echo esc_html(ucfirst($booking->status)); ?>
                                        </span>
                                    </p>
                                    <?php if (!empty($booking->notes)): ?>
                                        <p><strong>Notes:</strong> <?php echo esc_html($booking->notes); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message area for feedback -->
    <div id="profile-message" class="message-area"></div>
</div>

<!-- Pet Modal -->
<div id="pet-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="pet-modal-title">Add Pet</h3>
            <span class="close" onclick="closePetModal()">&times;</span>
        </div>
        <form id="pet-form">
            <input type="hidden" id="pet_id" name="pet_id">
            
            <div class="form-group">
                <label for="pet_name">Pet Name *</label>
                <input type="text" id="pet_name" name="name" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="pet_type">Type *</label>
                    <select id="pet_type" name="type" required>
                        <option value="">Select type...</option>
                        <option value="dog">Dog</option>
                        <option value="cat">Cat</option>
                        <option value="bird">Bird</option>
                        <option value="rabbit">Rabbit</option>
                        <option value="fish">Fish</option>
                        <option value="reptile">Reptile</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="pet_breed">Breed</label>
                    <input type="text" id="pet_breed" name="breed">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="pet_age">Age (years)</label>
                    <input type="number" id="pet_age" name="age" min="0" max="50">
                </div>
                <div class="form-group">
                    <label for="pet_weight">Weight (kg)</label>
                    <input type="number" id="pet_weight" name="weight" min="0" step="0.1">
                </div>
            </div>
            
            <div class="form-group">
                <label for="pet_health_notes">Health Notes</label>
                <textarea id="pet_health_notes" name="health_notes" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="pet_photo">Pet Photo</label>
                <div class="image-upload-container">
                    <input type="file" id="pet_photo_file" accept="image/*" style="display: none;">
                    <button type="button" class="upload-btn" onclick="document.getElementById('pet_photo_file').click()">Choose Image</button>
                    <input type="url" id="pet_photo" name="photo_url" placeholder="Image URL" readonly>
                    <div id="pet_photo_preview"></div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closePetModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Pet</button>
            </div>
        </form>
    </div>
</div>

<!-- Service Modal -->
<?php if (AmalAuthHelper::is_service_provider()): ?>
<div id="service-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="service-modal-title">Add Service</h3>
            <span class="close" onclick="closeServiceModal()">&times;</span>
        </div>
        <form id="service-form">
            <input type="hidden" id="service_id" name="service_id">
            
            <div class="form-group">
                <label for="service_title">Service Title *</label>
                <input type="text" id="service_title" name="title" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="service_category">Category *</label>
                    <select id="service_category" name="category" required>
                        <option value="">Select category...</option>
                        <option value="dog_walking">Dog Walking</option>
                        <option value="pet_sitting">Pet Sitting</option>
                        <option value="grooming">Grooming</option>
                        <option value="training">Training</option>
                        <option value="veterinary">Veterinary</option>
                        <option value="boarding">Boarding</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="service_price">Price ($) *</label>
                    <input type="number" id="service_price" name="price" min="0" step="0.01" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="service_description">Description</label>
                <textarea id="service_description" name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="service_location">Location</label>
                <input type="text" id="service_location" name="location">
            </div>
            
            <div class="form-group">
                <label for="service_availability">Availability</label>
                <textarea id="service_availability" name="availability" rows="3" placeholder="e.g., Monday-Friday 9AM-5PM, Weekends by appointment"></textarea>
            </div>
            
            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" id="service_is_active" name="is_active" value="1" checked>
                    Service is active (visible to customers)
                </label>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeServiceModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Service</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>