/**
 * Amal Authentication JavaScript
 * Handles AJAX form submissions and real-time validation
 */

jQuery(document).ready(function($) {
    
    // Password strength indicator
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];
        
        if (password.length >= 8) {
            strength++;
        } else {
            feedback.push('At least 8 characters');
        }
        
        if (/[a-zA-Z]/.test(password)) {
            strength++;
        } else {
            feedback.push('At least one letter');
        }
        
        if (/[0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('At least one number');
        }
        
        if (/[^a-zA-Z0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('At least one symbol');
        }
        
        return {
            strength: strength,
            feedback: feedback,
            isValid: strength === 4
        };
    }
    
    // Real-time password validation
    $('#register-password').on('input', function() {
        const password = $(this).val();
        const result = checkPasswordStrength(password);
        
        // Remove existing strength indicator
        $('.password-strength').remove();
        
        if (password.length > 0) {
            let strengthClass = '';
            let strengthText = '';
            
            switch(result.strength) {
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
                        <div class="strength-fill" style="width: ${(result.strength / 4) * 100}%"></div>
                    </div>
                    <div class="strength-text">${strengthText}</div>
                    ${result.feedback.length > 0 ? '<div class="strength-feedback">Missing: ' + result.feedback.join(', ') + '</div>' : ''}
                </div>
            `;
            
            $(this).after(strengthHtml);
        }
    });
    
    // Real-time email validation
    $('#register-email, #login-email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        $('.email-validation').remove();
        
        if (email.length > 0 && !emailRegex.test(email)) {
            $(this).after('<div class="email-validation error">Please enter a valid email address</div>');
        }
    });
    
    // Registration form submission
    $('#amal-register-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const messageDiv = $('#register-message');
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).text('Registering...');
        messageDiv.html('');
        
        // Get form data
        const formData = {
            action: 'amal_register',
            nonce: amal_ajax.nonce,
            email: $('#register-email').val(),
            password: $('#register-password').val(),
            first_name: $('#register-first-name').val(),
            last_name: $('#register-last-name').val(),
            user_type: $('#register-user-type').val()
        };
        
        // Client-side validation
        if (!formData.email || !formData.password) {
            messageDiv.html('<div class="error">Email and password are required</div>');
            submitBtn.prop('disabled', false).text('Register');
            return;
        }
        
        const passwordCheck = checkPasswordStrength(formData.password);
        if (!passwordCheck.isValid) {
            messageDiv.html('<div class="error">Password does not meet requirements</div>');
            submitBtn.prop('disabled', false).text('Register');
            return;
        }
        
        // AJAX request
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="success">' + response.data.message + '</div>');
                    form[0].reset();
                    $('.password-strength').remove();
                } else {
                    messageDiv.html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                messageDiv.html('<div class="error">Registration failed. Please try again.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Register');
            }
        });
    });
    
    // Login form submission
    $('#amal-login-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const messageDiv = $('#login-message');
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).text('Logging in...');
        messageDiv.html('');
        
        // Get form data
        const formData = {
            action: 'amal_login',
            nonce: amal_ajax.nonce,
            email: $('#login-email').val(),
            password: $('#login-password').val()
        };
        
        // Client-side validation
        if (!formData.email || !formData.password) {
            messageDiv.html('<div class="error">Email and password are required</div>');
            submitBtn.prop('disabled', false).text('Login');
            return;
        }
        
        // AJAX request
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="success">' + response.data.message + '</div>');
                    
                    // Redirect or reload page after successful login
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                messageDiv.html('<div class="error">Login failed. Please try again.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Login');
            }
        });
    });
    
    // Logout button
    $(document).on('click', '#amal-logout-btn', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        btn.prop('disabled', true).text('Logging out...');
        
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'amal_logout',
                nonce: amal_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function() {
                alert('Logout failed. Please try again.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Logout');
            }
        });
    });
    
    // Form accessibility improvements
    $('.amal-register-form input, .amal-login-form input').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group').removeClass('focused');
    });
    
    // Auto-focus first input field
    $('.amal-register-form input:first, .amal-login-form input:first').focus();
    
});