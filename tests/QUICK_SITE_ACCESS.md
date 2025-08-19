# Quick Site Access Guide

This is a quick reference for accessing all the different sites and interfaces in the Amal repository.

## 🚀 Quick Setup

```bash
# 1. Install dependencies (REQUIRED)
composer install --timeout=600
cd web/app/themes/Amal_Sage && composer install && npm install && npm run build
```

## 🌐 All Sites & Access Points

### Main Application Sites

| **Site** | **URL** | **Description** | **Login Required** |
|----------|---------|-----------------|-------------------|
| **Main WordPress Site** | `/` or `http://your-domain.com` | Main website with Sage theme | No |
| **Social Blog** | `/blog/` | Social media feed with posts | No (but login needed to post) |
| **User Profiles** | `/blog/profile.php?user=1` | Individual user profile pages | No |

### Admin Interfaces (Admin Login Required)

| **Admin Interface** | **URL** | **Description** |
|---------------------|---------|-----------------|
| **Inventory Management** | `/admin/inventory/` | Manage store items, add/edit products |
| **Add New Item** | `/admin/inventory/add` | Add new store items |
| **Edit Item** | `/admin/inventory/edit?id=1` | Edit existing store items |
| **Order Management** | `/admin/orders/` | View and manage customer orders |

### Store Frontend (Customer-Facing)

| **Store Interface** | **Access Method** | **Description** |
|---------------------|-------------------|-----------------|
| **Storefront** | WordPress pages with store templates | Browse and purchase items |
| **Item Details** | Store item pages | Individual product pages |
| **Cart/Checkout** | Store workflow | Shopping cart and checkout process |

### User Account Features

| **Feature** | **Access Method** | **Description** |
|-------------|-------------------|-----------------|
| **Registration** | Add `[amal_register_form]` shortcode to page | User registration form |
| **Login** | Add `[amal_login_form]` shortcode to page | User login form |
| **Profile Management** | Add profile management shortcode to page | Manage pets, services, bookings |

## 🧪 Test & Demo Pages

### Standalone Test Scripts (Run in Command Line)
```bash
# Test social media functionality
php web/app/plugins/amal-social/test-social.php

# Test store checkout
php web/app/plugins/amal-store/test-checkout.php

# Test order management
php web/app/plugins/amal-store/test-order-management.php
```

### Demo HTML Pages (Open in Browser)
```bash
# Admin inventory demo
web/app/plugins/amal-store/admin/test-inventory-admin.html

# Store demos
web/app/plugins/amal-store/demo/storefront-implementation.html
web/app/plugins/amal-store/demo/cart-demo.html
web/app/plugins/amal-store/demo/checkout-implementation.html
web/app/plugins/amal-store/demo/order-management-implementation.html

# Profile management demo
web/app/plugins/amal-auth/profile-management-demo.html
```

## 🔐 User Types & Access Levels

### Regular Users (pet_owner, service_provider)
- ✅ Can access main site, blog, profiles
- ✅ Can register/login via forms
- ✅ Can create posts in social feed (when logged in)
- ✅ Can manage their own profile, pets, services
- ❌ Cannot access admin inventory/orders

### Admin Users  
- ✅ All regular user permissions
- ✅ Can access `/admin/inventory/` and `/admin/orders/`
- ✅ Can manage store items and customer orders

## 🏃‍♂️ Quick Testing Workflow

### 1. Test Main Sites (2 minutes)
```bash
# Open these URLs in browser:
http://your-domain.com          # Main site
http://your-domain.com/blog/    # Social feed  
http://your-domain.com/blog/profile.php?user=1  # User profile
```

### 2. Test Admin Areas (requires admin login)
```bash
# Open these URLs in browser (after admin login):
http://your-domain.com/admin/inventory/     # Inventory management
http://your-domain.com/admin/orders/        # Order management
```

### 3. Run Quick Plugin Tests (30 seconds)
```bash
# Run in terminal:
php web/app/plugins/amal-social/test-social.php
```

### 4. View Demo Pages (1 minute)
```bash
# Open in browser:
web/app/plugins/amal-store/admin/test-inventory-admin.html
```

## 🚨 Troubleshooting

**Site not loading?**
- Check if WordPress is properly configured
- Verify web server is running
- Check file permissions

**Admin areas giving 404?**
- Ensure plugins are activated in WordPress admin
- Check if user is logged in as admin
- Verify rewrite rules are working

**Demo pages not displaying correctly?**
- Open them directly in browser (they're static HTML)
- Check browser console for any errors

**Plugin tests failing?**
- Ensure Composer dependencies are installed
- Check PHP version (requires 8.1+)
- Verify database connection if using WordPress features

## 📚 More Information

For detailed testing instructions, see:
- **Full Manual Testing Guide**: `/tests/MANUAL_TESTING_GUIDE.md`
- **Plugin Documentation**: Individual `README.md` files in each plugin directory
- **Overall Testing Framework**: `/TESTING.md` in repository root