# Amal Store WP_CLI Commands

This document demonstrates the new WP_CLI commands available for managing sample items in the Amal Store plugin.

## Available Commands

### 1. Populate Store with Sample Items
```bash
wp amal-store populate
```
- Adds 21 sample items to the store
- Skips items that already exist
- Shows summary of items added/skipped

### 2. Populate Store (Replace All)
```bash
wp amal-store populate --clear
```
- Clears all existing items first
- Adds all 21 sample items
- Useful for resetting the store

### 3. View Store Summary
```bash
wp amal-store summary
```
- Shows total number of items
- Shows number of active items
- Breaks down items by category

### 4. List Available Sample Items
```bash
wp amal-store list-samples
```
- Shows all 21 sample items available
- Displays title, category, price, and stock
- Does NOT add items to database

### 5. Clear All Items
```bash
wp amal-store clear
```
- Removes all items from the store
- Prompts for confirmation
- Shows count of items removed

### 6. Clear All Items (No Confirmation)
```bash
wp amal-store clear --yes
```
- Removes all items without prompting
- Useful for automated scripts

## Sample Command Output

```
$ wp amal-store populate
ℹ️  Starting store population...
✅ SUCCESS: Successfully populated store with sample items. Items added: 21, Skipped: 0, Total available: 21

$ wp amal-store summary
ℹ️  Store Items Summary:
ℹ️  ==================
ℹ️  Total Items: 21
ℹ️  Active Items: 21
ℹ️  
ℹ️  Items by Category:
ℹ️    - Food: 5 items
ℹ️    - Accessories: 4 items
ℹ️    - Toys: 3 items
ℹ️    - Housing: 4 items
ℹ️    - Aquarium: 3 items
ℹ️    - Health: 2 items
✅ SUCCESS: Summary retrieved successfully.
```

## Integration

The WP_CLI commands are automatically registered when the Amal Store plugin is activated and WP_CLI is available. No additional setup is required.

The commands use the same underlying functionality as the existing populate-sample-items.php script, ensuring consistency across all interfaces.