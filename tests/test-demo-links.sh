#!/bin/bash

# Test script to verify all demo links work correctly
# This script checks if all the files referenced in index.html exist and are accessible

echo "🧪 Testing Amal Demo Links..."
echo "================================"

cd /home/runner/work/Amal/Amal/tests

# Array of demo files that should exist
declare -a demo_files=(
    "web/app/plugins/amal-store/admin/test-inventory-admin.html"
    "web/app/plugins/amal-store/demo/order-management-implementation.html"
    "web/app/plugins/amal-store/demo/storefront-implementation.html"
    "web/app/plugins/amal-store/demo/cart-demo.html"
    "web/app/plugins/amal-store/demo/checkout-implementation.html"
    "web/app/plugins/amal-auth/profile-management-demo.html"
    "web/app/plugins/amal-store/admin/test-schema.html"
    "web/app/plugins/amal-store/demo/storefront-mockup.html"
    "web/app/plugins/amal-auth/README.md"
    "web/app/plugins/amal-store/README.md"
    "web/app/plugins/amal-social/README.md"
    "web/app/plugins/amal-profile-management/README.md"
)

# Test each file
failed_tests=0
total_tests=${#demo_files[@]}

for file in "${demo_files[@]}"; do
    if [[ -f "$file" ]]; then
        echo "✅ $file - EXISTS"
    else
        echo "❌ $file - NOT FOUND"
        ((failed_tests++))
    fi
done

echo ""
echo "Test Results:"
echo "============"
echo "Total tests: $total_tests"
echo "Passed: $((total_tests - failed_tests))"
echo "Failed: $failed_tests"

if [[ $failed_tests -eq 0 ]]; then
    echo "🎉 All demo links should work correctly!"
    exit 0
else
    echo "⚠️  Some files are missing. Check the failed paths above."
    exit 1
fi