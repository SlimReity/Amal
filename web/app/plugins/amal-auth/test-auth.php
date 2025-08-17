<?php
/**
 * Amal Authentication System Test Page
 * 
 * This page allows you to test the authentication functionality
 * without requiring WordPress activation.
 */

// Start session if not already started
if (!session_id()) {
    session_start();
}

// Simple mock for WordPress functions if not available
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) {
        return $nonce === 'test_nonce_' . $action;
    }
    
    function wp_create_nonce($action) {
        return 'test_nonce_' . $action;
    }
    
    function sanitize_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
    function sanitize_text_field($text) {
        return htmlspecialchars(strip_tags($text), ENT_QUOTES, 'UTF-8');
    }
    
    function current_time($type = 'mysql') {
        return date('Y-m-d H:i:s');
    }
}

// Simple database connection (replace with your credentials)
$test_db_config = [
    'host' => 'localhost',
    'dbname' => 'amal_test',
    'username' => 'root',
    'password' => ''
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amal Authentication System Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .test-section {
            margin-bottom: 40px;
        }
        
        .test-section h2 {
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }
        
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-weight: 500;
        }
        
        .status.success {
            background-color: #d1f2d1;
            color: #00a32a;
            border: 1px solid #00a32a;
        }
        
        .status.error {
            background-color: #ffeaea;
            color: #d63638;
            border: 1px solid #d63638;
        }
        
        .status.info {
            background-color: #e8f4f8;
            color: #007cba;
            border: 1px solid #007cba;
        }
        
        .test-item {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .test-item h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .test-item p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        
        code {
            background-color: #f0f0f0;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .demo-forms {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .demo-forms {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="assets/amal-auth.css">
</head>
<body>
    <div class="test-container">
        <h1>🔐 Amal Authentication System Test</h1>
        <p>This page tests the core functionality of the Amal authentication system.</p>
    </div>

    <div class="test-container">
        <div class="test-section">
            <h2>🔍 System Requirements Check</h2>
            
            <div class="test-item">
                <h4>PHP Version</h4>
                <?php
                $php_version = phpversion();
                $php_ok = version_compare($php_version, '8.1', '>=');
                ?>
                <div class="status <?php echo $php_ok ? 'success' : 'error'; ?>">
                    Current: <?php echo $php_version; ?> 
                    <?php echo $php_ok ? '✅ Compatible' : '❌ Requires PHP 8.1+'; ?>
                </div>
            </div>
            
            <div class="test-item">
                <h4>Required Extensions</h4>
                <?php
                $required_extensions = ['pdo', 'pdo_mysql', 'session', 'filter'];
                foreach ($required_extensions as $ext) {
                    $loaded = extension_loaded($ext);
                    echo "<div class='status " . ($loaded ? 'success' : 'error') . "'>";
                    echo $ext . ": " . ($loaded ? '✅ Loaded' : '❌ Missing');
                    echo "</div>";
                }
                ?>
            </div>
            
            <div class="test-item">
                <h4>Session Support</h4>
                <?php
                $session_ok = session_status() !== PHP_SESSION_DISABLED;
                ?>
                <div class="status <?php echo $session_ok ? 'success' : 'error'; ?>">
                    <?php echo $session_ok ? '✅ Sessions Available' : '❌ Sessions Disabled'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="test-container">
        <div class="test-section">
            <h2>🧪 Core Function Tests</h2>
            
            <div class="test-item">
                <h4>Password Validation</h4>
                <?php
                // Test password validation
                $test_passwords = [
                    ['password' => 'weak', 'expected' => false],
                    ['password' => 'StrongPass123!', 'expected' => true],
                    ['password' => '12345678', 'expected' => false],
                    ['password' => 'NoNumbers!', 'expected' => false],
                ];
                
                function test_password_validation($password) {
                    if (strlen($password) < 8) return false;
                    if (!preg_match('/[a-zA-Z]/', $password)) return false;
                    if (!preg_match('/[0-9]/', $password)) return false;
                    if (!preg_match('/[^a-zA-Z0-9]/', $password)) return false;
                    return true;
                }
                
                foreach ($test_passwords as $test) {
                    $result = test_password_validation($test['password']);
                    $status = $result === $test['expected'] ? 'success' : 'error';
                    echo "<div class='status $status'>";
                    echo "Password: '<code>{$test['password']}</code>' - " . ($result === $test['expected'] ? '✅ Pass' : '❌ Fail');
                    echo "</div>";
                }
                ?>
            </div>
            
            <div class="test-item">
                <h4>Email Validation</h4>
                <?php
                $test_emails = [
                    ['email' => 'user@example.com', 'expected' => true],
                    ['email' => 'invalid-email', 'expected' => false],
                    ['email' => 'test@domain', 'expected' => false],
                    ['email' => 'valid.email+tag@domain.co.uk', 'expected' => true],
                ];
                
                foreach ($test_emails as $test) {
                    $result = filter_var($test['email'], FILTER_VALIDATE_EMAIL) !== false;
                    $status = $result === $test['expected'] ? 'success' : 'error';
                    echo "<div class='status $status'>";
                    echo "Email: '<code>{$test['email']}</code>' - " . ($result === $test['expected'] ? '✅ Pass' : '❌ Fail');
                    echo "</div>";
                }
                ?>
            </div>
            
            <div class="test-item">
                <h4>Password Hashing</h4>
                <?php
                $test_password = 'TestPassword123!';
                $hash = password_hash($test_password, PASSWORD_DEFAULT);
                $verify = password_verify($test_password, $hash);
                ?>
                <div class="status <?php echo $verify ? 'success' : 'error'; ?>">
                    Hash verification: <?php echo $verify ? '✅ Working' : '❌ Failed'; ?>
                </div>
                <p><small>Hash: <code><?php echo substr($hash, 0, 50); ?>...</code></small></p>
            </div>
        </div>
    </div>

    <div class="test-container">
        <div class="test-section">
            <h2>📊 Database Schema</h2>
            
            <div class="test-item">
                <h4>Table Structure</h4>
                <p>The following SQL creates the user table:</p>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;"><?php echo htmlspecialchars(file_get_contents('users.sql')); ?></pre>
            </div>
        </div>
    </div>

    <div class="test-container">
        <div class="test-section">
            <h2>🎨 Demo Forms</h2>
            <p>These forms demonstrate the registration and login interface:</p>
            
            <div class="demo-forms">
                <div>
                    <h3>Registration Form</h3>
                    <div class="amal-register-form">
                        <form id="demo-register-form">
                            <div class="form-group">
                                <label for="demo-register-email">Email *</label>
                                <input type="email" id="demo-register-email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="demo-register-password">Password *</label>
                                <input type="password" id="demo-register-password" name="password" required>
                                <small>Must be at least 8 characters with letters, numbers, and symbols</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="demo-register-first-name">First Name</label>
                                <input type="text" id="demo-register-first-name" name="first_name">
                            </div>
                            
                            <div class="form-group">
                                <label for="demo-register-last-name">Last Name</label>
                                <input type="text" id="demo-register-last-name" name="last_name">
                            </div>
                            
                            <div class="form-group">
                                <label for="demo-register-user-type">I am a:</label>
                                <select id="demo-register-user-type" name="user_type">
                                    <option value="pet_owner">Pet Owner</option>
                                    <option value="service_provider">Service Provider</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="button" onclick="alert('This is a demo form. Integrate with WordPress to enable functionality.')">Register (Demo)</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div>
                    <h3>Login Form</h3>
                    <div class="amal-login-form">
                        <form id="demo-login-form">
                            <div class="form-group">
                                <label for="demo-login-email">Email</label>
                                <input type="email" id="demo-login-email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="demo-login-password">Password</label>
                                <input type="password" id="demo-login-password" name="password" required>
                            </div>
                            
                            <div class="form-group">
                                <button type="button" onclick="alert('This is a demo form. Integrate with WordPress to enable functionality.')">Login (Demo)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="test-container">
        <div class="test-section">
            <h2>📋 Integration Instructions</h2>
            
            <div class="test-item">
                <h4>WordPress Plugin Activation</h4>
                <p>1. Copy the plugin folder to <code>/web/app/plugins/amal-auth/</code></p>
                <p>2. Activate the plugin in WordPress admin</p>
                <p>3. The database table will be created automatically</p>
            </div>
            
            <div class="test-item">
                <h4>Using Shortcodes</h4>
                <p>Registration form: <code>[amal_register_form]</code></p>
                <p>Login form: <code>[amal_login_form]</code></p>
                <p>User info: <code>[amal_user_info]</code></p>
            </div>
            
            <div class="test-item">
                <h4>Helper Functions</h4>
                <p>Check if logged in: <code>amal_is_logged_in()</code></p>
                <p>Get current user: <code>amal_current_user()</code></p>
                <p>Check user type: <code>amal_is_pet_owner()</code> or <code>amal_is_service_provider()</code></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Demo password strength checker
        jQuery(document).ready(function($) {
            $('#demo-register-password').on('input', function() {
                const password = $(this).val();
                let strength = 0;
                let feedback = [];
                
                if (password.length >= 8) strength++;
                else feedback.push('At least 8 characters');
                
                if (/[a-zA-Z]/.test(password)) strength++;
                else feedback.push('At least one letter');
                
                if (/[0-9]/.test(password)) strength++;
                else feedback.push('At least one number');
                
                if (/[^a-zA-Z0-9]/.test(password)) strength++;
                else feedback.push('At least one symbol');
                
                $('.password-strength').remove();
                
                if (password.length > 0) {
                    let strengthClass = '';
                    let strengthText = '';
                    
                    switch(strength) {
                        case 0:
                        case 1:
                            strengthClass = 'weak';
                            strengthText = 'Weak';
                            break;
                        case 2:
                            strengthClass = 'fair';
                            strengthText = 'Fair';
                            break;
                        case 3:
                            strengthClass = 'good';
                            strengthText = 'Good';
                            break;
                        case 4:
                            strengthClass = 'strong';
                            strengthText = 'Strong';
                            break;
                    }
                    
                    const strengthHtml = `
                        <div class="password-strength ${strengthClass}">
                            <div class="strength-bar">
                                <div class="strength-fill" style="width: ${(strength / 4) * 100}%"></div>
                            </div>
                            <div class="strength-text">${strengthText}</div>
                            ${feedback.length > 0 ? '<div class="strength-feedback">Missing: ' + feedback.join(', ') + '</div>' : ''}
                        </div>
                    `;
                    
                    $(this).after(strengthHtml);
                }
            });
        });
    </script>
</body>
</html>