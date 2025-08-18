# Testing Guide for Amal Project

This guide provides comprehensive instructions for running, writing, and extending tests in the Amal project.

## Overview

The test suite is organized into three main categories:

- **Unit Tests** (`tests/unit/`): Test individual classes and functions in isolation
- **Integration Tests** (`tests/integration/`): Test multiple components working together
- **End-to-End Tests** (`tests/e2e/`): Test complete user workflows and browser-based functionality

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- WordPress test environment (for WordPress-specific tests)
- Composer (for dependency management)
- MySQL/MariaDB (for database tests)

### Environment Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd Amal
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure WordPress test environment:**
   ```bash
   # Copy wp-config-sample.php to wp-config.php and configure database
   cp web/wp-config-sample.php web/wp-config.php
   # Edit wp-config.php with your database credentials
   ```

4. **Set up test database:**
   ```bash
   # Create a separate test database
   mysql -u root -p -e "CREATE DATABASE amal_test;"
   ```

## Running Tests

### Using the Test Runner

The project includes a centralized test runner that can execute all tests or specific categories:

```bash
# Run all tests
php tests/run-tests.php

# Run only unit tests
php tests/run-tests.php unit

# Run only integration tests
php tests/run-tests.php integration

# Run only end-to-end tests
php tests/run-tests.php e2e
```

### Using Composer Scripts

Add these scripts to your workflow:

```bash
# Run all tests
composer test

# Run specific test types
composer test:unit
composer test:integration
composer test:e2e

# Lint code before testing
composer lint
```

### Running Individual Tests

You can also run individual test files directly:

```bash
# Run a specific unit test
php tests/unit/class-amal-store-database.test.php

# Run a specific integration test
php tests/integration/amal-store-admin-validation.test.php
```

## Writing New Tests

### Naming Conventions

- **Unit Tests**: `class-[class-name].test.php` (e.g., `class-amal-store-database.test.php`)
- **Integration Tests**: `[feature-name]-integration.test.php` (e.g., `amal-store-admin-validation.test.php`)
- **E2E Tests**: `[workflow-name].test.php` (e.g., `amal-auth-system.test.php`)

### File Structure

Place tests in the appropriate directory:

```
tests/
├── unit/           # Individual class/function tests
├── integration/    # Multi-component tests
├── e2e/           # End-to-end workflow tests
└── run-tests.php  # Main test runner
```

### Unit Test Template

```php
<?php
/**
 * Unit tests for [Class Name]
 */

// Include the class being tested
require_once dirname(__DIR__, 2) . '/web/app/plugins/[plugin]/includes/[class-file].php';

class [Class_Name]_Test extends WP_UnitTestCase {
    
    private $instance;
    
    public function setUp(): void {
        parent::setUp();
        $this->instance = new [Class_Name]();
    }
    
    public function tearDown(): void {
        // Clean up test data
        parent::tearDown();
    }
    
    /**
     * Test [specific functionality]
     */
    public function test_[function_name]() {
        // Arrange
        $input = 'test_input';
        
        // Act
        $result = $this->instance->method_name($input);
        
        // Assert
        $this->assertTrue($result, 'Method should return true');
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $test = new [Class_Name]_Test();
    $test->setUp();
    $test->test_[function_name]();
    $test->tearDown();
}
```

### Integration Test Template

```php
<?php
/**
 * Integration tests for [Feature Name]
 */

echo "🧪 [Feature Name] Integration Test\n";
echo str_repeat("=", 50) . "\n";

$errors = [];
$passed = 0;

// Test 1: Check component integration
try {
    // Test integration between components
    $result = test_integration();
    if ($result) {
        echo "✅ Integration test passed\n";
        $passed++;
    } else {
        echo "❌ Integration test failed\n";
        $errors[] = "Integration test failed";
    }
} catch (Exception $e) {
    echo "❌ Integration test failed: " . $e->getMessage() . "\n";
    $errors[] = "Integration test error: " . $e->getMessage();
}

// Test summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Passed: $passed\n";
echo "❌ Failed: " . count($errors) . "\n";

if (count($errors) === 0) {
    echo "🎉 All integration tests passed!\n";
} else {
    echo "⚠️ Some tests failed:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}
```

### E2E Test Template

For browser-based tests, create HTML test pages that verify complete workflows:

```php
<?php
/**
 * E2E tests for [Workflow Name]
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Workflow Name] E2E Test</title>
    <style>
        /* Test page styles */
        .test-result { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .test-success { background: #d4edda; color: #155724; }
        .test-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>[Workflow Name] E2E Test</h1>
    
    <?php
    // Test workflow steps
    echo '<div class="test-result test-success">✅ Step 1 completed</div>';
    ?>
</body>
</html>
```

## Mocking Dependencies

### WordPress Functions

For tests that don't run in full WordPress environment:

```php
// Mock WordPress functions if not available
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) {
        return $nonce === 'test_nonce_' . $action;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($text) {
        return htmlspecialchars(strip_tags($text), ENT_QUOTES, 'UTF-8');
    }
}
```

### Database Connections

```php
// Mock database for testing
$test_db_config = [
    'host' => 'localhost',
    'dbname' => 'amal_test',
    'username' => 'test_user',
    'password' => 'test_password'
];

try {
    $test_pdo = new PDO(
        "mysql:host={$test_db_config['host']};dbname={$test_db_config['dbname']}",
        $test_db_config['username'],
        $test_db_config['password']
    );
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
```

## Test Coverage

### Generating Coverage Reports

To enable test coverage reporting with Xdebug:

1. **Install Xdebug:**
   ```bash
   # Ubuntu/Debian
   sudo apt-get install php-xdebug
   
   # macOS with Homebrew
   brew install php-xdebug
   ```

2. **Configure Xdebug in php.ini:**
   ```ini
   [xdebug]
   xdebug.mode=coverage
   ```

3. **Generate coverage report:**
   ```bash
   php -d xdebug.mode=coverage tests/run-tests.php
   ```

### Coverage Goals

- **Unit Tests**: Aim for 80%+ code coverage
- **Integration Tests**: Focus on critical user paths
- **E2E Tests**: Cover major workflows and edge cases

## Continuous Integration

### GitHub Actions

Add to `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: pdo, pdo_mysql, xdebug
        
    - name: Install dependencies
      run: composer install
      
    - name: Run tests
      run: composer test
      
    - name: Generate coverage
      run: composer test:coverage
```

### Local Pre-commit Hooks

Set up pre-commit hooks to run tests automatically:

```bash
# Install pre-commit hook
echo '#!/bin/bash
composer lint && composer test:unit
' > .git/hooks/pre-commit

chmod +x .git/hooks/pre-commit
```

## Debugging Failed Tests

### Common Issues

1. **Path Issues**: Ensure file paths are correct relative to test location
2. **Missing Dependencies**: Check that all required classes are included
3. **Database Issues**: Verify test database is accessible and properly configured
4. **WordPress Functions**: Mock missing WordPress functions for standalone tests

### Debugging Tools

```php
// Add debug output to tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use var_dump for debugging
var_dump($variable);

// Add detailed error messages
$this->assertTrue($condition, "Detailed error message explaining what failed");
```

## Best Practices

### Test Organization

1. **One class per test file** for unit tests
2. **Group related functionality** in integration tests
3. **Test one workflow per file** for E2E tests
4. **Use descriptive test names** that explain what is being tested

### Test Quality

1. **Arrange-Act-Assert pattern** for clear test structure
2. **Test both success and failure cases**
3. **Use meaningful assertions** with descriptive messages
4. **Clean up test data** in tearDown methods
5. **Keep tests independent** - each test should run in isolation

### Performance

1. **Use database transactions** for tests that modify data
2. **Mock external dependencies** to avoid network calls
3. **Group similar tests** to reduce setup overhead
4. **Use test fixtures** for complex test data

## Troubleshooting

### Permission Issues
```bash
# Fix file permissions
chmod +x tests/run-tests.php
```

### Memory Issues
```bash
# Increase PHP memory limit
php -d memory_limit=512M tests/run-tests.php
```

### Database Issues
```bash
# Reset test database
mysql -u root -p -e "DROP DATABASE amal_test; CREATE DATABASE amal_test;"
```

## Contributing

When adding new features:

1. **Write tests first** (Test-Driven Development)
2. **Add both unit and integration tests** for new functionality
3. **Update this guide** if introducing new testing patterns
4. **Ensure all tests pass** before submitting pull requests

For questions or issues with testing, please create an issue in the project repository.