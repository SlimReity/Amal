# Amal Profile Management Plugin

A comprehensive WordPress plugin that provides complete user profile, pet, and service management functionality for the Amal pet services platform.

## Features

### 🎯 Core Functionality
- **Complete Profile Management System** with responsive, mobile-first design
- **Tab-based Interface** for Profile Info, My Pets, My Services, and My Bookings
- **WordPress Integration** following all best practices and standards
- **Translation Ready** with proper WordPress localization support
- **AJAX-Powered Interface** for smooth user experience without page reloads

### 🐾 Profile Management
- Personal information editing (name, email, phone, address)
- Profile picture upload capability
- Notification preferences management
- Subscription management
- Contact information updates

### 🐕 Pet Management
- Add, edit, and delete pets
- Pet photos and detailed information
- Health notes tracking
- Age and weight monitoring
- Activity history tracking

### 🏢 Service Provider Tools
- Service creation and editing
- Pricing and availability management
- Category management
- Location settings
- Service status control (active/inactive)

### 📋 Booking Management
- Comprehensive booking history view
- Status tracking (confirmed, completed, cancelled)
- Pet association with bookings
- Payment records tracking
- Service details display

## Installation

### Method 1: Manual Installation
1. Download the plugin files
2. Upload the `amal-profile-management` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The plugin will automatically create necessary database tables

### Method 2: WordPress Admin
1. Go to Plugins → Add New
2. Upload the plugin ZIP file
3. Click "Install Now" and then "Activate"

## Usage

### Shortcode
The plugin provides a simple shortcode to display the profile management interface:

```php
[amal_profile_management]
```

### PHP Template Usage
For direct PHP integration in themes:

```php
<?php echo do_shortcode('[amal_profile_management]'); ?>
```

### Sage Theme Integration
For Sage/Blade templates:

```php
{!! do_shortcode('[amal_profile_management]') !!}
```

### Shortcode Attributes
The shortcode accepts several optional attributes:

```php
[amal_profile_management show_header="true" show_features="true" show_implementation="true" default_tab="profile"]
```

- `show_header` - Show/hide the plugin header (default: true)
- `show_features` - Show/hide the features grid (default: true)  
- `show_implementation` - Show/hide implementation details (default: true)
- `default_tab` - Set the default active tab (default: profile)

## Technical Details

### File Structure
```
amal-profile-management/
├── amal-profile-management.php    # Main plugin file
├── assets/
│   ├── profile-management.css     # Stylesheet
│   └── profile-management.js      # JavaScript functionality
├── templates/
│   └── profile-management.php     # Main template
├── languages/                     # Translation files (future)
└── README.md                      # This documentation
```

### Database Tables
The plugin creates two custom tables:

#### wp_amal_pets
- `id` - Primary key
- `user_id` - WordPress user ID
- `name` - Pet name
- `type` - Pet type (dog, cat, etc.)
- `breed` - Pet breed
- `age` - Pet age
- `weight` - Pet weight
- `notes` - Health/care notes
- `image_url` - Pet photo URL
- `created_at` / `updated_at` - Timestamps

#### wp_amal_services
- `id` - Primary key
- `user_id` - WordPress user ID
- `name` - Service name
- `category` - Service category
- `price` - Service price
- `location` - Service location
- `description` - Service description
- `status` - Active/inactive status
- `created_at` / `updated_at` - Timestamps

### WordPress Hooks & Filters

#### Actions
- `amal_profile_updated` - Fired when profile is updated
- `amal_pet_added` - Fired when pet is added
- `amal_pet_updated` - Fired when pet is updated
- `amal_pet_deleted` - Fired when pet is deleted
- `amal_service_added` - Fired when service is added
- `amal_service_updated` - Fired when service is updated
- `amal_service_deleted` - Fired when service is deleted

#### Filters
- `amal_get_user_pets` - Filter user's pets data
- `amal_get_user_services` - Filter user's services data
- `amal_get_user_bookings` - Filter user's bookings data
- `amal_profile_management_load_pages` - Filter pages where assets load

### AJAX Endpoints
The plugin registers several AJAX endpoints:
- `amal_update_profile` - Update user profile
- `amal_update_pet` - Update pet information
- `amal_update_service` - Update service information
- `amal_delete_pet` - Delete a pet
- `amal_delete_service` - Delete a service
- `amal_get_pets` - Fetch user pets via AJAX
- `amal_get_services` - Fetch user services via AJAX

## Security Features

### WordPress Security Best Practices
- **Nonce Verification** for all AJAX requests
- **User Capability Checks** for all operations
- **Data Sanitization** using WordPress functions
- **SQL Injection Prevention** via prepared statements
- **XSS Protection** through proper escaping

### Input Validation
- Email format validation
- Phone number format validation
- Required field validation
- File upload security (for images)

## Styling & Customization

### CSS Classes
The plugin uses prefixed CSS classes to avoid conflicts:
- `.amal-profile-container` - Main container
- `.amal-tab-nav` - Tab navigation
- `.amal-tab-content` - Tab content areas
- `.amal-form-group` - Form field groups
- `.amal-btn` - Buttons
- `.amal-pet-card` / `.amal-service-card` - Item cards

### Responsive Design
- Mobile-first approach
- Flexible grid layouts
- Touch-friendly interface
- Optimized for all screen sizes

### Custom Styling
To customize the appearance, you can:

1. **Override CSS** in your theme:
```css
.amal-profile-container {
    /* Your custom styles */
}
```

2. **Dequeue default styles** and use your own:
```php
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('amal-profile-management');
    wp_enqueue_style('custom-amal-profile', 'path/to/your/styles.css');
}, 100);
```

## Developer Integration

### Extending Functionality
Developers can extend the plugin using WordPress hooks:

```php
// Add custom pet data
add_filter('amal_get_user_pets', function($pets, $user_id) {
    // Your custom logic to fetch pets
    return $custom_pets;
}, 10, 2);

// Add custom service data
add_filter('amal_get_user_services', function($services, $user_id) {
    // Your custom logic to fetch services
    return $custom_services;
}, 10, 2);
```

### Custom Templates
You can override the template by copying it to your theme:

1. Copy `templates/profile-management.php` to your theme
2. Modify as needed
3. The plugin will automatically use your custom template

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 5.6 or higher
- **jQuery:** Included with WordPress

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- iOS Safari 12+
- Android Chrome 60+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the code comments

## Changelog

### Version 1.0.0
- Initial release
- Complete profile management system
- Pet management functionality
- Service provider tools
- Booking management interface
- Mobile-responsive design
- WordPress best practices implementation
- Translation-ready interface

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed for the Amal pet services platform by the Amal Development Team.