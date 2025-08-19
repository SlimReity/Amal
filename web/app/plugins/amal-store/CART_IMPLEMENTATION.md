# Shopping Cart Implementation

This document describes the new shopping cart functionality implemented for the Amal Store plugin.

## Features Implemented

### ✅ Core Cart Operations
- **Add items to cart** with quantity validation
- **Remove items from cart** with confirmation dialogs  
- **Update item quantities** using +/- buttons or direct input
- **Real-time total calculation** showing subtotal and total
- **Stock quantity validation** on all cart operations

### ✅ Cart Persistence 
- **PHP Session storage** for logged-in users
- **Cookie backup storage** for guest users (30-day persistence)
- **Cross-session cart recovery** when users return

### ✅ User Experience
- **AJAX-powered operations** for instant updates without page reload
- **Responsive design** works on mobile and desktop
- **User feedback** with success/error messages
- **Empty cart handling** with "continue shopping" links

### ✅ WordPress Integration
- **Cart shortcode** `[amal_cart]` for displaying cart anywhere
- **Guest user support** via `wp_ajax_nopriv` actions
- **WordPress security** with nonce verification

## Usage

### Displaying the Cart

Use the shortcode to display the cart on any page or post:

```php
[amal_cart]
```

Optional attributes:
```php
[amal_cart show_checkout="no"]  // Hide checkout button
```

### AJAX Endpoints

The following AJAX actions are available:

- `amal_add_to_cart` - Add item to cart
- `amal_update_cart` - Update item quantity 
- `amal_remove_from_cart` - Remove item from cart
- `amal_get_cart` - Get current cart contents

### Cart Data Structure

Cart items are stored with this structure:
```php
$cart = [
    'item_1' => [
        'item_id' => 1,
        'quantity' => 2, 
        'price' => 24.99,
        'title' => 'Product Name'
    ]
];
```

## Frontend JavaScript API

The `AmalStore` JavaScript object provides these methods:

- `handleAddToCart()` - Handle add to cart button clicks
- `handleRemoveItem()` - Handle remove item button clicks  
- `handleQuantityIncrease()` - Handle quantity + button clicks
- `handleQuantityDecrease()` - Handle quantity - button clicks
- `updateCartItem(itemId, quantity)` - Update cart via AJAX
- `updateCartDisplay(data)` - Update cart totals in UI
- `checkEmptyCart()` - Handle empty cart state

## CSS Classes

Key CSS classes for styling:

- `.amal-cart-container` - Main cart wrapper
- `.amal-cart-item` - Individual cart item
- `.amal-quantity-controls` - Quantity +/- controls
- `.amal-remove-item` - Remove item button
- `.amal-cart-summary` - Totals section
- `.amal-checkout-btn` - Checkout button

## Cart Validation

Stock validation is performed on:
- Adding items to cart
- Updating item quantities  
- Cart display/totals calculation

Validation ensures:
- Quantities don't exceed available stock
- Items are still available
- Minimum quantity of 1 for all items

## Browser Compatibility

The cart works with:
- Modern browsers supporting ES5+ JavaScript
- jQuery 3.0+
- Browsers with cookie support for guest persistence

## Security

Security measures implemented:
- WordPress nonce verification on all AJAX requests
- Input sanitization and validation
- SQL injection prevention via WordPress $wpdb
- XSS prevention via `esc_html()` and `esc_attr()`

## Performance

Optimizations included:
- Efficient session + cookie storage
- Minimal DOM manipulation
- AJAX requests only when needed
- CSS grid layout for responsive design

## Testing

Run the validation test:
```bash
cd web/app/plugins/amal-store
php comprehensive-cart-test.php
```

View the demo:
```bash
# Start local server
python3 -m http.server 8080
# Visit: http://localhost:8080/demo/cart-demo.html
```

## Acceptance Criteria Status

- ✅ **Cart updates instantly when items are added/removed** - AJAX operations provide instant feedback
- ✅ **Quantity limits respect stock_qty** - Validation checks stock on all operations  
- ✅ **Guest users can use cart until checkout** - Cookie persistence supports guest users
- ✅ **Users can add/remove items with quantity** - Full CRUD operations implemented
- ✅ **Cart persists across browsing sessions** - Session + cookie storage implemented
- ✅ **Display subtotal and total** - Real-time calculation and display

## Files Modified

- `includes/class-amal-store-frontend.php` - Added cart management methods
- `assets/js/frontend.js` - Added cart interaction handlers  
- `assets/css/frontend.css` - Added cart styling
- `templates/cart.php` - New cart display template
- `demo/cart-demo.html` - Interactive demo page

## Next Steps

Potential enhancements:
- Checkout process integration
- Cart abandonment recovery emails
- Quantity discounts/pricing tiers
- Cart export/import functionality
- Analytics integration