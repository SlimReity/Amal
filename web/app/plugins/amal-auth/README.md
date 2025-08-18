# Amal Authentication System

A secure, production-ready user login and registration system for the Amal pet services platform. Built as a WordPress plugin with modern security practices and seamless integration with Bedrock/Sage.

## 🚀 Features

### ✅ Core Authentication
- **Secure Registration**: Email and password registration with validation
- **Secure Login**: Password verification with session management
- **Password Security**: bcrypt hashing with `password_hash(PASSWORD_DEFAULT)`
- **User Types**: Support for Pet Owners and Service Providers

### ✅ Security Features
- **SQL Injection Protection**: All queries use prepared statements
- **CSRF Protection**: WordPress nonces for all AJAX requests
- **Password Strength**: Enforced requirements (8+ chars, letters, numbers, symbols)
- **Email Validation**: Format validation and duplicate checking
- **Input Sanitization**: All user data properly sanitized

### ✅ Database Integration
- **MySQL Compatible**: Works with XAMPP/Bedrock MySQL setup
- **SQL Generation**: Generates INSERT statements for manual execution
- **Auto Table Creation**: Database table created on plugin activation
- **Export Ready**: `users.sql` file with all registration data

### ✅ Frontend Integration
- **WordPress Shortcodes**: Easy integration with any theme
- **AJAX Forms**: Smooth user experience without page reloads
- **Responsive Design**: Mobile-friendly forms
- **Real-time Validation**: Password strength and email format checking
- **Accessibility**: Proper labels, focus indicators, keyboard navigation

### ✅ Profile Management System (NEW!)
- **User Profiles**: Comprehensive profile management with personal details and preferences
- **Pet Management**: Add, edit, and manage pets with photos and health information
- **Service Provider Tools**: Manage services, pricing, and availability (for service providers)
- **Booking History**: View and track service bookings and activity
- **File Uploads**: Secure image upload for profiles and pets
- **Responsive Interface**: Tabbed interface optimized for all devices

## 📁 File Structure

```
web/app/plugins/amal-auth/
├── amal-auth.php                    # Main plugin file
├── assets/
│   ├── amal-auth.js                # Frontend JavaScript (AJAX forms + profile management)
│   └── amal-auth.css               # Styling for forms and profile interface
├── includes/
│   └── helper-functions.php        # Session management + profile utilities
├── templates/
│   └── profile-management.php      # Profile management interface template
├── users.sql                       # Generated SQL statements
├── profile-management-migration.sql # Database migration for new features
├── test-auth.php                   # Test page for authentication
├── test-profile-management.php     # Test page for profile features
├── PROFILE_MANAGEMENT_DOCS.md      # Detailed profile system documentation
└── README.md                       # This documentation
```

## ⚡ Quick Start

### 1. Plugin Installation

1. Copy the `amal-auth` folder to `/web/app/plugins/`
2. Activate the plugin in WordPress admin: **Plugins → "Amal Authentication System"**
3. Database table is created automatically on activation

### 2. Manual Database Setup (Alternative)

If you prefer manual database setup:

```bash
# Import the SQL file
mysql -u username -p database_name < users.sql

# Or execute in phpMyAdmin/MySQL client
```

### 3. Add Forms to Pages

Use WordPress shortcodes in posts, pages, or theme templates:

```php
// Registration form
[amal_register_form]

// Login form  
[amal_login_form]

// User info display (for logged-in users)
[amal_user_info]

// Profile management interface (NEW!)
[amal_profile_management]
```

### 4. Database Migration (Profile Management)

If using the new profile management features, run the migration:

```bash
# Import the profile management migration
mysql -u username -p database_name < profile-management-migration.sql
```

## 🏗️ Database Schema

### Core Users Table
```sql
CREATE TABLE wp_amal_users (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    email varchar(100) NOT NULL,
    password_hash varchar(255) NOT NULL,
    first_name varchar(50) DEFAULT '',
    last_name varchar(50) DEFAULT '',
    phone varchar(20) DEFAULT '',                    -- NEW
    address text DEFAULT '',                         -- NEW  
    profile_picture varchar(255) DEFAULT '',         -- NEW
    notification_email tinyint(1) DEFAULT 1,        -- NEW
    notification_push tinyint(1) DEFAULT 1,         -- NEW
    notification_sms tinyint(1) DEFAULT 0,          -- NEW
    subscription_type enum('free', 'premium') DEFAULT 'free', -- NEW
    user_type enum('pet_owner', 'service_provider') DEFAULT 'pet_owner',
    registration_date datetime DEFAULT CURRENT_TIMESTAMP,
    last_login datetime DEFAULT NULL,
    is_active tinyint(1) DEFAULT 1,
    email_verified tinyint(1) DEFAULT 0,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
);
```

### New Profile Management Tables
- **wp_amal_pets**: Pet information and photos
- **wp_amal_services**: Service provider offerings  
- **wp_amal_bookings**: Service booking records

See `PROFILE_MANAGEMENT_DOCS.md` for complete schema details.

## 🔧 Usage Examples

### Helper Functions

```php
// Check if user is logged in
if (amal_is_logged_in()) {
    echo "Welcome!";
}

// Get current user data
$user = amal_current_user();
echo "Hello, " . $user->first_name;

// Check user type
if (amal_is_pet_owner()) {
    echo "Pet owner dashboard";
} elseif (amal_is_service_provider()) {
    echo "Service provider dashboard";
}

// Require login for protected pages
amal_require_login();

// Get current user ID
$user_id = amal_current_user_id();
```

### Class Methods

```php
// Using the helper class directly
if (AmalAuthHelper::is_logged_in()) {
    $user = AmalAuthHelper::get_current_user();
    echo $user->email;
}

// Update user data
AmalAuthHelper::update_user($user_id, [
    'first_name' => 'New Name',
    'last_login' => current_time('mysql')
]);

// Get user statistics
$pet_owners = AmalAuthHelper::get_user_count_by_type('pet_owner');
$service_providers = AmalAuthHelper::get_user_count_by_type('service_provider');
```

### Profile Management Functions (NEW!)

```php
// Get user's pets
$pets = AmalAuthHelper::get_user_pets($user_id);

// Add new pet
$pet_id = AmalAuthHelper::add_pet($user_id, [
    'name' => 'Buddy',
    'type' => 'dog',
    'breed' => 'Golden Retriever',
    'age' => 3,
    'weight' => 25.5
]);

// Get user's services (for service providers)
$services = AmalAuthHelper::get_user_services($user_id);

// Add new service
$service_id = AmalAuthHelper::add_service($user_id, [
    'title' => 'Dog Walking Service',
    'category' => 'dog_walking',
    'description' => 'Professional dog walking in your neighborhood',
    'price' => 25.00,
    'location' => 'Downtown Area'
]);

// Get user's bookings
$bookings = AmalAuthHelper::get_user_bookings($user_id);

// Update profile information
AmalAuthHelper::update_user_profile($user_id, [
    'phone' => '+1-555-0123',
    'address' => '123 Main St, City, State',
    'notification_email' => 1,
    'subscription_type' => 'premium'
]);
```

### AJAX Integration

The system handles AJAX requests automatically. Forms submit without page reloads and provide real-time feedback.

**JavaScript Events:**
```javascript
// Custom handling after successful login
$(document).on('amal_login_success', function(event, userData) {
    console.log('User logged in:', userData);
    // Custom redirect or UI updates
});

// Custom handling after registration
$(document).on('amal_register_success', function(event, userData) {
    console.log('User registered:', userData);
    // Show welcome message, etc.
});
```

## 🔐 Security Features

### Password Requirements
- Minimum 8 characters
- At least one letter (a-z, A-Z)
- At least one number (0-9)  
- At least one symbol (!@#$%^&*, etc.)

### Validation & Protection
- **Email Format**: Validated using `filter_var(FILTER_VALIDATE_EMAIL)`
- **Duplicate Prevention**: Checks for existing emails before registration
- **SQL Injection**: All queries use `$wpdb->prepare()` with placeholders
- **CSRF Protection**: WordPress nonces verify all form submissions
- **Session Security**: Secure session management with automatic cleanup

### Data Sanitization
- Email: `sanitize_email()`
- Text fields: `sanitize_text_field()`
- Database queries: `esc_sql()` and prepared statements

## 🧪 Testing

### Standalone Testing
Open `test-auth.php` in your browser to test functionality without WordPress:

```
http://your-site.com/wp-content/plugins/amal-auth/test-auth.php
```

The test page includes:
- System requirements check
- Core function validation
- Password strength testing
- Email validation testing
- Demo forms with styling

### Integration Testing
1. Activate the plugin in WordPress
2. Add shortcodes to a test page
3. Test registration and login flows
4. Verify database records in `wp_amal_users` table
5. Check generated SQL in `users.sql`

## 🎨 Customization

### Styling
Modify `assets/amal-auth.css` to match your theme:

```css
/* Custom colors */
.amal-register-form,
.amal-login-form {
    --primary-color: #your-color;
    --border-color: #your-border;
}

/* Custom form width */
.amal-register-form {
    max-width: 500px; /* Default: 400px */
}
```

### Form Customization
Use shortcode attributes for basic customization:

```php
[amal_register_form class="custom-class" redirect="/welcome"]
[amal_login_form class="custom-login" redirect="/dashboard"]
```

### Hooks & Filters
The plugin provides WordPress hooks for advanced customization:

```php
// Modify registration data before saving
add_filter('amal_auth_registration_data', function($data) {
    // Custom processing
    return $data;
});

// Custom actions after successful login
add_action('amal_auth_login_success', function($user_data) {
    // Custom logic
});
```

## 🌱 Future Extensions

The system is designed for easy extension:

### Email Verification
```php
// Add to user registration
'email_verified' => 0,
'verification_token' => AmalAuthHelper::generate_token()
```

### Password Reset
```php
// Add password reset functionality
'reset_token' => null,
'reset_expires' => null
```

### Social Login
```php
// Add OAuth fields
'provider' => null,
'provider_id' => null
```

## 📊 Database Management

### Export User Data
```sql
-- Export all users
SELECT * FROM wp_amal_users WHERE is_active = 1;

-- Export by user type
SELECT * FROM wp_amal_users WHERE user_type = 'pet_owner';
```

### User Statistics
```sql
-- Registration statistics
SELECT 
    DATE(registration_date) as date,
    COUNT(*) as registrations
FROM wp_amal_users 
GROUP BY DATE(registration_date)
ORDER BY date DESC;

-- User type breakdown
SELECT 
    user_type,
    COUNT(*) as count
FROM wp_amal_users 
WHERE is_active = 1
GROUP BY user_type;
```

## 🔧 Troubleshooting

### Common Issues

**Plugin Not Activating**
- Check PHP version (requires 8.1+)
- Verify write permissions on plugin directory
- Check error logs for specific issues

**Forms Not Submitting**
- Ensure jQuery is loaded
- Check browser console for JavaScript errors
- Verify AJAX URLs and nonces

**Database Connection Issues**
- Verify MySQL credentials in `wp-config.php`
- Check database server status
- Ensure table creation permissions

**Session Issues**
- Verify session.save_path is writable
- Check if sessions are enabled in PHP
- Clear browser cookies and try again

### Debug Mode
Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📋 Requirements

- **PHP**: 8.1 or higher
- **WordPress**: 5.0 or higher
- **MySQL**: 5.7 or higher
- **Extensions**: PDO, PDO_MySQL, Sessions
- **Permissions**: Write access to plugin directory

## 🤝 Support

For issues, questions, or contributions:

1. Check the test page (`test-auth.php`) for system diagnostics
2. Review the troubleshooting section above
3. Check WordPress error logs
4. Verify all requirements are met

## 📄 License

This authentication system is part of the Amal project and follows the same licensing terms.

---

**Built with ❤️ for the Amal pet services community**