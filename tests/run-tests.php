<?php
/**
 * Main Test Runner for Amal Project
 * 
 * This script runs all organized tests across the project
 */

// Define base paths
define('AMAL_TEST_ROOT', __DIR__);
define('AMAL_PROJECT_ROOT', dirname(__DIR__));

class AmalTestRunner {
    
    private $tests_passed = 0;
    private $tests_failed = 0;
    private $test_files = [];
    
    public function __construct() {
        echo "🧪 Amal Project Test Suite\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $this->discover_tests();
    }
    
    /**
     * Discover all test files in the organized structure
     */
    private function discover_tests() {
        $test_directories = [
            'unit' => AMAL_TEST_ROOT . '/unit',
            'integration' => AMAL_TEST_ROOT . '/integration', 
            'e2e' => AMAL_TEST_ROOT . '/e2e'
        ];
        
        foreach ($test_directories as $type => $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.test.php');
                foreach ($files as $file) {
                    $this->test_files[$type][] = $file;
                }
            }
        }
    }
    
    /**
     * Run all tests
     */
    public function run_all_tests() {
        foreach (['unit', 'integration', 'e2e'] as $type) {
            $this->run_test_type($type);
        }
        
        $this->print_summary();
    }
    
    /**
     * Run tests of a specific type
     */
    public function run_test_type($type) {
        if (!isset($this->test_files[$type]) || empty($this->test_files[$type])) {
            echo "📁 No {$type} tests found\n\n";
            return;
        }
        
        echo "📁 Running " . strtoupper($type) . " Tests:\n";
        echo str_repeat("-", 40) . "\n";
        
        foreach ($this->test_files[$type] as $test_file) {
            $this->run_single_test($test_file);
        }
        
        echo "\n";
    }
    
    /**
     * Run a single test file
     */
    private function run_single_test($test_file) {
        $test_name = basename($test_file, '.test.php');
        echo "🔄 Running: {$test_name}\n";
        
        // Run test in separate process to capture all output
        $command = "cd " . escapeshellarg(AMAL_PROJECT_ROOT) . " && php " . escapeshellarg($test_file) . " 2>&1";
        $output = shell_exec($command);
        $return_code = 0; // shell_exec doesn't provide return code
        
        if ($output === null) {
            echo "  ❌ FAILED (Could not execute test)\n";
            $this->tests_failed++;
            return;
        }
        
        // Check if test passed based on output
        $passed = (
            strpos($output, '🎉') !== false || 
            strpos($output, 'All tests passed') !== false ||
            strpos($output, 'All integration tests passed') !== false ||
            strpos($output, 'All frontend tests passed') !== false ||
            (strpos($output, 'Failed: 0') !== false && strpos($output, 'Passed:') !== false) ||
            (strpos($output, '❌') === false && strpos($output, '✅') !== false)
        );
        
        if ($passed) {
            echo "  ✅ PASSED\n";
            $this->tests_passed++;
        } else {
            echo "  ❌ FAILED\n";
            $this->tests_failed++;
            
            // Show first few lines of output for debugging
            if (trim($output)) {
                echo "    Output:\n";
                $lines = explode("\n", $output);
                foreach (array_slice($lines, 0, 5) as $line) {
                    if (trim($line)) {
                        echo "    " . trim($line) . "\n";
                    }
                }
                if (count($lines) > 5) {
                    echo "    ... (truncated)\n";
                }
            }
        }
    }
    
    /**
     * Print test summary
     */
    private function print_summary() {
        echo str_repeat("=", 60) . "\n";
        echo "📊 Test Summary:\n";
        echo "✅ Passed: {$this->tests_passed}\n";
        echo "❌ Failed: {$this->tests_failed}\n";
        echo "Total: " . ($this->tests_passed + $this->tests_failed) . "\n\n";
        
        if ($this->tests_failed === 0) {
            echo "🎉 All tests passed! The Amal project is ready.\n";
        } else {
            echo "⚠️  Some tests failed. Please review the errors above.\n";
        }
    }
}

// Run tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $runner = new AmalTestRunner();
    
    // Check for specific test type argument
    $test_type = $argv[1] ?? 'all';
    
    if (in_array($test_type, ['unit', 'integration', 'e2e'])) {
        $runner->run_test_type($test_type);
    } else {
        $runner->run_all_tests();
    }
}