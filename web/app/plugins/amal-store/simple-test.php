<?php
echo "Simple PHP test\n";

// Test if classes exist
if (file_exists('includes/class-amal-store-database.php')) {
    echo "✅ Database class file exists\n";
} else {
    echo "❌ Database class file missing\n";
}

if (file_exists('includes/class-amal-store-admin.php')) {
    echo "✅ Admin class file exists\n";
} else {
    echo "❌ Admin class file missing\n";
}

echo "Test completed\n";