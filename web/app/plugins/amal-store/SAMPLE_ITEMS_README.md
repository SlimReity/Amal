# Amal Store Sample Items

This directory contains tools to populate your Amal Store with realistic sample items for testing and demonstration purposes.

## 🎯 Purpose

The sample items help you:
- Test the storefront display functionality
- Demonstrate the store to stakeholders
- Validate filtering, search, and categorization features
- Test cart and checkout workflows with realistic data

## 📦 What's Included

### Sample Items Dataset (20+ items)
- **Dog Products**: Food, leashes, toys, beds
- **Cat Products**: Food, litter boxes, toys, cat trees
- **Bird Products**: Cages, food, toys
- **Aquarium Products**: Filters, lights, food, decorations
- **Small Pet Products**: Hamster cages, rabbit food, playpens
- **Health & Grooming**: Professional grooming kits, vitamins

Each item includes:
- Realistic titles and descriptions
- Appropriate pricing ($12.99 - $149.99)
- Stock quantities
- Category classification
- Placeholder images
- Active/inactive status

## 🚀 Quick Start

### Option 1: Using the HTML Manager (Recommended)
1. Open `sample-items-manager.html` in your web browser
2. Click "Get Store Summary" to check current status
3. Click "Add Sample Items" to populate the database
4. View the storefront to see the new items

### Option 2: Direct PHP Script
```php
// Include the script in your WordPress environment
require_once 'populate-sample-items.php';

$populator = new Amal_Store_Sample_Items();

// Add items (keeping existing ones)
$result = $populator->populate_items(false);

// Or replace all items
$result = $populator->populate_items(true);

print_r($result);
```

### Option 3: URL Parameters
```
# Add sample items
http://your-site.com/wp-content/plugins/amal-store/populate-sample-items.php?action=populate

# Get current summary
http://your-site.com/wp-content/plugins/amal-store/populate-sample-items.php?action=summary

# Replace all items
http://your-site.com/wp-content/plugins/amal-store/populate-sample-items.php?action=populate&clear=true
```

## 📊 Sample Data Overview

| Category | Items | Price Range | Total Stock |
|----------|-------|-------------|-------------|
| Food | 5 items | $12.99 - $45.99 | 375 units |
| Accessories | 4 items | $19.99 - $89.99 | 153 units |
| Housing | 5 items | $79.99 - $149.99 | 95 units |
| Toys | 3 items | $15.99 - $32.99 | 175 units |
| Aquarium | 4 items | $12.99 - $67.99 | 150 units |
| Health | 2 items | $28.99 - $74.99 | 65 units |

**Total**: 23 items across 6 categories

## 🔧 Features

### Smart Population
- **Duplicate Detection**: Won't add items that already exist (unless forced)
- **Data Validation**: Ensures proper formatting and constraints
- **Error Handling**: Reports any issues during population
- **Flexible Options**: Add to existing items or replace all

### Item Variety
- **Stock Levels**: Mix of high-stock (100+) and low-stock (15-20) items
- **Out of Stock**: Includes one out-of-stock item for testing
- **Price Range**: Covers budget ($12.99) to premium ($149.99) items
- **Categories**: Balanced across all major pet store categories

### Testing Scenarios
- **Search Testing**: Items with varied keywords and descriptions
- **Filter Testing**: Multiple categories and price ranges
- **Stock Testing**: Different stock levels including zero stock
- **Display Testing**: Various title lengths and description styles

## 🎨 Visual Integration

All sample items include placeholder images that match the existing demo mockups:
- Consistent sizing (400x300px)
- Branded color schemes
- Descriptive text overlays
- Professional appearance

## 🔄 Maintenance

### Adding New Items
Edit the `get_sample_items()` method in `populate-sample-items.php`:

```php
array(
    'title' => 'Your New Product',
    'category' => 'Category Name',
    'description' => 'Detailed description...',
    'price' => 29.99,
    'stock_qty' => 40,
    'image_url' => 'https://via.placeholder.com/400x300/color/ffffff?text=Product+Name',
    'is_active' => 1
)
```

### Clearing Items
Use the HTML manager or call `populate_items(true)` to replace all existing items.

### Checking Status
Use the summary function to see current item counts and categories.

## 🧪 Testing Integration

These sample items are designed to work with:
- **Storefront Display**: Test the main store grid layout
- **Item Detail Pages**: Individual product pages
- **Search Functionality**: Various keywords and terms
- **Category Filtering**: All major categories represented
- **Cart Operations**: Add to cart, quantity changes
- **Stock Validation**: Including out-of-stock scenarios

## 📋 Requirements

- **WordPress**: Active WordPress installation
- **Amal Store Plugin**: Must be activated with database tables created
- **PHP 7.4+**: For modern PHP syntax support
- **MySQL**: For database operations

## 🐛 Troubleshooting

### "Table does not exist" Error
- Ensure Amal Store plugin is activated
- Check if database tables were created properly
- Try deactivating and reactivating the plugin

### "Items already exist" Message
- This is normal - script skips duplicates by default
- Use "Replace All Items" option to force refresh
- Or manually clear items first

### Script Timeout
- Increase PHP execution time if needed
- Items are inserted individually to handle errors gracefully

## 🎯 Next Steps

After populating with sample items:
1. **Test Storefront**: Visit your store page to see the items displayed
2. **Test Filtering**: Try category filters and search functionality
3. **Test Cart**: Add items to cart and test quantity changes
4. **Test Admin**: Use admin panel to manage the sample items
5. **Customize**: Replace placeholder images with real product photos

---

*These sample items provide a solid foundation for testing and demonstrating your Amal Store functionality. The realistic data helps showcase the store's capabilities while providing comprehensive test coverage.*