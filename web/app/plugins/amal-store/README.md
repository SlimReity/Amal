# Amal Store Plugin

A WordPress plugin that implements the database schema for the Amal pet store e-commerce functionality.

## Overview

This plugin creates and manages three core database tables that form the foundation of the Amal pet store:

- **Items Table** (`wp_amal_items`): Product catalog with inventory management
- **Orders Table** (`wp_amal_orders`): Order tracking and management  
- **Order Items Table** (`wp_amal_order_items`): Junction table for order line items

## Database Schema

### Items Table (`wp_amal_items`)

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint(20) unsigned | Primary key, auto-increment |
| `title` | varchar(255) | Product title |
| `category` | varchar(100) | Product category |
| `description` | text | Product description |
| `price` | decimal(10,2) | Product price |
| `stock_qty` | int(11) | Stock quantity (≥ 0) |
| `image_url` | varchar(500) | Product image URL |
| `is_active` | tinyint(1) | Active status (1=active, 0=inactive) |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

**Constraints:**
- `stock_qty >= 0` (prevents negative inventory)

**Indexes:**
- Primary key on `id`
- Index on `category` for filtering
- Index on `is_active` for active product queries
- Index on `price` for price-based sorting

### Orders Table (`wp_amal_orders`)

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint(20) unsigned | Primary key, auto-increment |
| `user_id` | bigint(20) unsigned | Foreign key to amal_users table |
| `total_price` | decimal(10,2) | Total order amount |
| `status` | enum | Order status (pending, processing, shipped, delivered, cancelled) |
| `created_at` | datetime | Order creation timestamp |
| `updated_at` | datetime | Last update timestamp |

**Foreign Keys:**
- `user_id` → `amal_users.id` (CASCADE DELETE)

**Indexes:**
- Primary key on `id`
- Index on `user_id` for user order queries
- Index on `status` for status filtering
- Index on `created_at` for chronological sorting

### Order Items Table (`wp_amal_order_items`)

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint(20) unsigned | Primary key, auto-increment |
| `order_id` | bigint(20) unsigned | Foreign key to orders table |
| `item_id` | bigint(20) unsigned | Foreign key to items table |
| `quantity` | int(11) | Quantity ordered |
| `price` | decimal(10,2) | Price at time of purchase |

**Foreign Keys:**
- `order_id` → `wp_amal_orders.id` (CASCADE DELETE)
- `item_id` → `wp_amal_items.id` (CASCADE DELETE)

**Indexes:**
- Primary key on `id`
- Index on `order_id` for order line item queries
- Index on `item_id` for item sales tracking

## Installation

1. Copy the plugin to your WordPress plugins directory: `wp-content/plugins/amal-store/`
2. Activate the plugin through the WordPress admin panel
3. Tables will be created automatically upon activation

## Usage

### Creating Tables Manually

If you need to create the tables manually, SQL files are generated in the uploads directory:

```
wp-content/uploads/amal-store-sql/
├── amal-store-schema.sql     # Complete schema with sample data
├── items-table.sql           # Items table only
├── orders-table.sql          # Orders table only
└── order-items-table.sql     # Order items table only
```

### Sample Usage

```php
// Get table names
$database = new Amal_Store_Database();
$tables = $database->get_table_names();

// Check if tables exist
if ($database->tables_exist()) {
    // Tables are ready for use
}
```

### Sample SQL Operations

```sql
-- Insert a new item
INSERT INTO wp_amal_items (title, category, description, price, stock_qty, is_active) 
VALUES ('Premium Dog Food', 'Food', 'High-quality dry dog food', 45.99, 100, 1);

-- Create an order
INSERT INTO wp_amal_orders (user_id, total_price, status) 
VALUES (1, 70.49, 'pending');

-- Add items to order
INSERT INTO wp_amal_order_items (order_id, item_id, quantity, price) 
VALUES (1, 1, 1, 45.99);

-- Update inventory after purchase
UPDATE wp_amal_items SET stock_qty = stock_qty - 1 WHERE id = 1;
```

## Data Integrity

The schema enforces data integrity through:

1. **Foreign Key Constraints**: Ensure referential integrity between tables
2. **CASCADE DELETE**: Automatically clean up related records when parent records are deleted
3. **CHECK Constraints**: Prevent invalid data (e.g., negative stock quantities)
4. **ENUM Types**: Restrict order status to valid values only

## Testing

Unit tests are included to verify:

- Table creation and structure
- Column definitions and constraints
- Foreign key relationships
- Data insertion and validation
- Table cleanup functionality

Run tests using your WordPress testing framework.

## File Structure

```
amal-store/
├── amal-store.php                      # Main plugin file
├── includes/
│   ├── class-amal-store.php            # Main plugin class
│   └── class-amal-store-database.php   # Database management class
├── tests/
│   └── test-database.php               # Unit tests
└── README.md                           # This file
```

## Requirements

- PHP 8.1+
- WordPress with Bedrock architecture
- MySQL 5.7+ or MariaDB 10.2+
- Existing `amal_users` table for foreign key relationships

## License

MIT License - See LICENSE.md for details.