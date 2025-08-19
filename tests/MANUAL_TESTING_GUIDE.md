# Amal Platform - Manual Testing Guide

This guide provides step-by-step instructions for manually testing all the sites, interfaces, and functionality available in the Amal pet services platform repository.

## 📋 Quick Reference - All Available Sites

| Interface | URL/Location | Purpose | Prerequisites |
|-----------|--------------|---------|---------------|
| **Main WordPress Site** | `/` | Main website frontend | WordPress setup |
| **Social Blog Feed** | `/blog/` | Social media posts and interactions | Auth plugin, Social plugin |
| **User Profiles** | `/blog/profile.php?user={id}` | Individual user profile pages | Auth plugin, Social plugin |
| **Admin Inventory** | `/admin/inventory/` | Manage store items | Store plugin, Admin access |
| **Admin Orders** | `/admin/orders/` | Manage customer orders | Store plugin, Admin access |
| **Store Frontend** | Store pages via theme templates | Browse and purchase items | Store plugin, Theme setup |
| **Profile Management** | Via shortcodes on pages | Manage pets, services, bookings | Profile Management plugin |

## 🚀 Prerequisites & Setup

### 1. Environment Requirements
- **PHP**: 8.1+ (tested with 8.3.6)
- **Node.js**: 20.0.0+ (tested with 20.19.4)
- **Composer**: 2.8+
- **MySQL**: For WordPress database
- **Web Server**: Apache/Nginx or local development server

### 2. Installation Steps
```bash
# 1. Install PHP dependencies (CRITICAL: Allow 10+ minutes)
composer install --timeout=600

# 2. Install theme dependencies (Allow 5+ minutes)
cd web/app/themes/Amal_Sage
composer install --timeout=300
npm install

# 3. Build theme assets
npm run build

# 4. Setup WordPress database (if using full WordPress)
# Import SQL files from plugin directories
```

### 3. Plugin Activation
1. Activate all Amal plugins in WordPress admin:
   - Amal Authentication System
   - Amal Store Plugin
   - Amal Social Media Plugin
   - Amal Profile Management Plugin

## 🌐 Website Interfaces Testing

### 1. Main WordPress Site (`/`)

**Purpose**: Main website frontend with Sage theme

**Testing Steps**:
1. Navigate to your WordPress site root URL
2. Verify Sage theme is active and rendering correctly
3. Check navigation menu functionality
4. Test responsive design on different screen sizes

**Expected Results**:
- ✅ Site loads without PHP errors
- ✅ Sage theme styles are applied
- ✅ Navigation works correctly
- ✅ Responsive layout functions properly

### 2. Social Blog Feed (`/blog/`)

**Purpose**: Social media feed where users can create posts, react, and interact

**Test File**: See `/web/app/plugins/amal-social/test-social.php` for backend validation

**Testing Steps**:
1. Navigate to `/blog/`
2. **If logged in**: Try creating a new post
3. Test reaction buttons (like/dislike) on existing posts
4. Click on usernames to visit profile pages
5. Test comment functionality if available

**Expected Results**:
- ✅ Social feed displays posts in chronological order
- ✅ Post creation form appears for authenticated users
- ✅ Reaction buttons work (AJAX updates)
- ✅ User links navigate to profile pages
- ✅ Responsive design on mobile devices

**Login Required**: Users must be authenticated via the auth system

### 3. User Profile Pages (`/blog/profile.php?user={id}`)

**Purpose**: Individual user profile pages showing user info and their posts

**Testing Steps**:
1. Navigate to `/blog/profile.php?user=1` (replace 1 with actual user ID)
2. Verify user information is displayed correctly
3. Check that user's posts are shown
4. Test reaction functionality on profile posts
5. Try different user IDs to test multiple profiles

**Expected Results**:
- ✅ User info displays (name, type, join date)
- ✅ User's posts are filtered and displayed
- ✅ Post interactions work same as main feed
- ✅ Profile links work for other users

## 🛒 Store & E-commerce Testing

### 4. Admin Inventory Management (`/admin/inventory/`)

**Purpose**: Admin interface for managing store items and inventory

**Test Files**: 
- `/web/app/plugins/amal-store/admin/test-inventory-admin.html`
- `/web/app/plugins/amal-store/admin/test-schema.html`

**Testing Steps**:
1. Log in as an admin user
2. Navigate to `/admin/inventory/`
3. **List View**: Verify inventory items are displayed
4. **Add New Item**: Navigate to `/admin/inventory/add`
   - Fill out item form (name, description, price, quantity)
   - Submit and verify item is created
5. **Edit Item**: Navigate to `/admin/inventory/edit?id={item_id}`
   - Modify item details
   - Save and verify changes persist
6. **Delete Item**: Test item deletion functionality

**Expected Results**:
- ✅ Admin-only access (redirects non-admins)
- ✅ Inventory list displays with pagination
- ✅ Add item form validation works
- ✅ Edit functionality persists changes to database
- ✅ Delete functionality removes items safely

### 5. Admin Order Management (`/admin/orders/`)

**Purpose**: Admin interface for managing customer orders

**Test Files**: `/web/app/plugins/amal-store/demo/order-management-implementation.html`

**Testing Steps**:
1. Log in as an admin user
2. Navigate to `/admin/orders/`
3. **Order List**: View all orders with filtering options
4. **Order Details**: Click on individual orders to view details
5. **Status Updates**: Test changing order status (pending → processing → shipped → completed)
6. **Filtering**: Test status and date filters

**Expected Results**:
- ✅ Orders display with customer information
- ✅ Order details show items, quantities, pricing
- ✅ Status updates persist in database
- ✅ Filtering works correctly
- ✅ Real-time status updates via AJAX

### 6. Store Frontend (Public Shopping)

**Purpose**: Customer-facing store interface for browsing and purchasing

**Test Files**: 
- `/web/app/plugins/amal-store/demo/storefront-implementation.html`
- `/web/app/plugins/amal-store/demo/storefront-mockup.html`

**Testing Steps**:
1. Navigate to store pages (via theme templates)
2. **Browse Items**: View product grid/list
3. **Item Details**: Click on items to view detailed pages
4. **Add to Cart**: Test cart functionality
5. **Checkout Process**: Complete purchase workflow

**Expected Results**:
- ✅ Only active items are displayed
- ✅ Category filtering works
- ✅ Search functionality operates
- ✅ Item detail pages show complete information
- ✅ Cart operations work correctly

## 👤 User Management & Authentication

### 7. Authentication System

**Purpose**: User registration, login, and session management

**Test Files**: 
- Check for `/web/app/plugins/amal-auth/test-auth.php` for standalone testing
- `/web/app/plugins/amal-auth/profile-management-demo.html`

**Testing Steps**:
1. **Registration**: Use `[amal_register_form]` shortcode
   - Test email validation
   - Test password strength requirements
   - Verify user types (pet_owner, service_provider)
2. **Login**: Use `[amal_login_form]` shortcode
   - Test with valid credentials
   - Test with invalid credentials
   - Verify session management
3. **Profile Display**: Use `[amal_user_info]` shortcode

**Expected Results**:
- ✅ Registration validates email format and password strength
- ✅ Passwords are securely hashed
- ✅ Login creates proper sessions
- ✅ User types are correctly assigned
- ✅ Forms work with AJAX (no page reload)

### 8. Profile Management Interface

**Purpose**: Comprehensive user profile, pet, and service management

**Testing Steps**:
1. Add profile management shortcode to a page
2. **Profile Tab**: Update user information
3. **My Pets Tab**: Add, edit, delete pet information
4. **My Services Tab**: Manage service offerings (for service providers)
5. **My Bookings Tab**: View booking history

**Expected Results**:
- ✅ Tab interface works smoothly
- ✅ AJAX updates work without page refresh
- ✅ Data persists correctly to database
- ✅ Responsive design on all devices
- ✅ User type restrictions apply correctly

## 🧪 Standalone Testing Scripts

### Plugin Validation Tests

**Run these to validate plugin functionality without full WordPress setup:**

```bash
# Test social media plugin
php web/app/plugins/amal-social/test-social.php

# Test store checkout functionality  
php web/app/plugins/amal-store/test-checkout.php

# Test order management
php web/app/plugins/amal-store/test-order-management.php
```

**Expected Results**:
- ✅ All tests pass with green checkmarks
- ✅ No PHP errors or warnings
- ✅ Database connections work (if configured)

### Demo HTML Pages

**View these in browser for visual demos:**

```bash
# Open in browser:
web/app/plugins/amal-store/admin/test-inventory-admin.html
web/app/plugins/amal-store/demo/cart-demo.html
web/app/plugins/amal-store/demo/checkout-implementation.html
web/app/plugins/amal-store/demo/order-management-implementation.html
web/app/plugins/amal-store/demo/storefront-implementation.html
```

## 🔍 Troubleshooting

### Common Issues

1. **404 Errors**: Ensure WordPress rewrite rules are configured
2. **Plugin Not Found**: Verify plugins are activated in WordPress admin
3. **Database Errors**: Check that plugin database tables exist
4. **Permission Denied**: Ensure user has correct role (admin for admin interfaces)
5. **AJAX Failures**: Check browser console for JavaScript errors

### Debug Mode

Enable WordPress debug mode by adding to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Build Issues

If theme assets aren't loading:
```bash
cd web/app/themes/Amal_Sage
npm run build
# Check that files appear in public/build/ directory
```

## ✅ Testing Checklist

Use this checklist to ensure comprehensive testing:

### Frontend Testing
- [ ] Main WordPress site loads correctly
- [ ] Blog feed displays and functions
- [ ] User profiles work with proper navigation
- [ ] Store frontend shows items correctly
- [ ] Authentication forms work properly
- [ ] Profile management interface functions

### Admin Testing  
- [ ] Admin inventory management works
- [ ] Order management interface functions
- [ ] Database operations persist correctly
- [ ] User role restrictions apply

### Plugin Testing
- [ ] All standalone test scripts pass
- [ ] Demo HTML pages display correctly
- [ ] AJAX functionality works without page refresh
- [ ] Mobile responsiveness verified

### Security Testing
- [ ] Admin areas require proper authentication
- [ ] User input is properly sanitized
- [ ] SQL injection protection works
- [ ] CSRF protection via nonces functions

## 📞 Support

For issues with specific plugins, refer to their individual README files:
- `/web/app/plugins/amal-auth/README.md`
- `/web/app/plugins/amal-store/README.md`
- `/web/app/plugins/amal-social/README.md`
- `/web/app/plugins/amal-profile-management/README.md`

For overall testing framework, see `/TESTING.md` in the repository root.