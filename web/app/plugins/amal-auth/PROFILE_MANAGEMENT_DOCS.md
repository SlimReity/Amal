# Profile Management System Documentation

## Overview

The Amal Profile Management System is a comprehensive solution that allows users to manage their profiles, pets, services (for service providers), and view their booking history. It extends the existing Amal Authentication System with advanced user management features.

## Features

### 1. Profile Information Management
- **Personal Details**: First name, last name, phone, address
- **Profile Picture**: Upload and manage profile image
- **Notification Preferences**: Email, push, and SMS notification settings
- **Subscription Management**: Free/Premium subscription status

### 2. Pet Management
- **CRUD Operations**: Add, edit, delete pets
- **Pet Attributes**: Name, type, breed, age, weight, health notes
- **Pet Photos**: Upload and manage pet profile pictures
- **Activity History**: View services booked for each pet

### 3. Service Provider Features
- **Service Management**: Add, edit, delete offered services
- **Service Details**: Title, category, description, price, availability, location
- **Service Status**: Active/inactive toggle for service visibility
- **Categories**: Dog walking, grooming, sitting, training, veterinary, boarding, etc.

### 4. Booking Management
- **Booking History**: View all past and current bookings
- **Booking Details**: Service, date, amount, status, notes
- **Pet Association**: Link bookings to specific pets (optional)
- **Status Tracking**: Pending, confirmed, completed, cancelled

## Database Schema

### Updated Users Table
```sql
ALTER TABLE wp_amal_users 
ADD COLUMN phone varchar(20) DEFAULT '',
ADD COLUMN address text DEFAULT '',
ADD COLUMN profile_picture varchar(255) DEFAULT '',
ADD COLUMN notification_email tinyint(1) DEFAULT 1,
ADD COLUMN notification_push tinyint(1) DEFAULT 1,
ADD COLUMN notification_sms tinyint(1) DEFAULT 0,
ADD COLUMN subscription_type enum('free', 'premium') DEFAULT 'free';
```

### New Tables
- **wp_amal_pets**: Pet information and photos
- **wp_amal_services**: Service provider offerings
- **wp_amal_bookings**: Service booking records

## Usage

### WordPress Shortcode
```php
[amal_profile_management]
```

### Template Usage
```blade
@if(function_exists('amal_is_logged_in') && amal_is_logged_in())
  {!! do_shortcode('[amal_profile_management]') !!}
@else
  {!! do_shortcode('[amal_login_form]') !!}
@endif
```

### PHP Function Usage
```php
// Check if user is logged in
if (AmalAuthHelper::is_logged_in()) {
    $user = AmalAuthHelper::get_current_user();
    $pets = AmalAuthHelper::get_user_pets($user->id);
    $services = AmalAuthHelper::get_user_services($user->id);
    $bookings = AmalAuthHelper::get_user_bookings($user->id);
}
```

## API Functions

### Profile Management
- `AmalAuthHelper::update_user_profile($user_id, $profile_data)`
- `AmalAuthHelper::get_current_user()`

### Pet Management
- `AmalAuthHelper::get_user_pets($user_id)`
- `AmalAuthHelper::add_pet($user_id, $pet_data)`
- `AmalAuthHelper::update_pet($pet_id, $user_id, $pet_data)`
- `AmalAuthHelper::delete_pet($pet_id, $user_id)`

### Service Management
- `AmalAuthHelper::get_user_services($user_id)`
- `AmalAuthHelper::add_service($user_id, $service_data)`
- `AmalAuthHelper::update_service($service_id, $user_id, $service_data)`
- `AmalAuthHelper::delete_service($service_id, $user_id)`

### Booking Management
- `AmalAuthHelper::get_user_bookings($user_id)`

## AJAX Endpoints

- `amal_update_profile` - Update user profile information
- `amal_add_pet` - Add new pet
- `amal_update_pet` - Update existing pet
- `amal_delete_pet` - Delete pet
- `amal_add_service` - Add new service (service providers only)
- `amal_update_service` - Update existing service
- `amal_delete_service` - Delete service
- `amal_upload_image` - Upload profile/pet images

## Security Features

- **CSRF Protection**: All AJAX requests use WordPress nonces
- **Access Control**: User ownership validation for all operations
- **Input Sanitization**: All user input is properly sanitized
- **File Upload Security**: Image type validation and secure file handling
- **Role-based Access**: Service management only for service providers

## Responsive Design

The interface is fully responsive and includes:
- **Mobile-first Design**: Optimized for mobile devices
- **Tablet Support**: Adaptive layout for tablets
- **Desktop Experience**: Full-featured desktop interface
- **Accessibility**: Proper ARIA labels and keyboard navigation
- **Dark Mode Support**: Automatic dark mode detection

## Installation

1. **Database Migration**: Run the migration SQL file to create new tables
2. **Plugin Activation**: The plugin will automatically create tables on activation
3. **File Permissions**: Ensure upload directory is writable for image uploads
4. **Testing**: Use the test page to verify all functionality

## Files Structure

```
web/app/plugins/amal-auth/
├── amal-auth.php                    # Main plugin file (extended)
├── includes/
│   └── helper-functions.php        # Helper functions (extended)
├── templates/
│   └── profile-management.php      # Profile management template
├── assets/
│   ├── amal-auth.css               # Styles (extended)
│   └── amal-auth.js                # JavaScript (extended)
├── profile-management-migration.sql # Database migration
└── test-profile-management.php     # Test page
```

## Theme Integration

A sample Blade template is provided for easy integration with the Sage theme:

```
web/app/themes/Amal_Sage/resources/views/template-profile.blade.php
```

This template can be used to create a dedicated profile page in WordPress.

## Testing

Use the test page at `web/app/plugins/amal-auth/test-profile-management.php` to verify:
- Plugin installation
- Database table creation  
- Helper function availability
- Shortcode registration
- AJAX handler registration
- File structure integrity

## Troubleshooting

### Common Issues
1. **Database Tables Missing**: Run the migration SQL file manually
2. **Image Upload Fails**: Check file permissions on wp-content/uploads
3. **AJAX Errors**: Verify nonce generation and WordPress AJAX setup
4. **Styling Issues**: Ensure CSS file is properly enqueued

### Debug Information
Enable WordPress debug mode and check error logs for detailed information about any issues.

## Future Enhancements

The system is designed for easy extension with:
- Email verification for profile changes
- Advanced booking management
- Payment integration
- Service rating and review system
- Push notification implementation
- Social media integration