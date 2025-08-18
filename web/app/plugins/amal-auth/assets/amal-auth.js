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
    
    // === PROFILE MANAGEMENT FUNCTIONALITY ===
    
    // Tab functionality
    $('.tab-btn').on('click', function() {
        const targetTab = $(this).data('tab');
        
        // Update active tab button
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        // Update active tab content
        $('.tab-content').removeClass('active');
        $('#' + targetTab).addClass('active');
    });
    
    // Profile form submission
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const messageDiv = $('#profile-message');
        
        submitBtn.prop('disabled', true).text('Updating...');
        messageDiv.html('');
        
        const formData = {
            action: 'amal_update_profile',
            nonce: amal_ajax.nonce,
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            phone: $('#phone').val(),
            address: $('#address').val(),
            profile_picture: $('#profile_picture').val(),
            notification_email: $('#profile-form input[name="notification_email"]').is(':checked') ? 1 : 0,
            notification_push: $('#profile-form input[name="notification_push"]').is(':checked') ? 1 : 0,
            notification_sms: $('#profile-form input[name="notification_sms"]').is(':checked') ? 1 : 0,
            subscription_type: $('#subscription_type').val()
        };
        
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="success">' + response.data.message + '</div>');
                } else {
                    messageDiv.html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                messageDiv.html('<div class="error">Update failed. Please try again.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Update Profile');
            }
        });
    });
    
    // Image upload functionality
    $('#profile_picture_file, #pet_photo_file').on('change', function() {
        const file = this.files[0];
        const targetInput = $(this).attr('id') === 'profile_picture_file' ? '#profile_picture' : '#pet_photo';
        const previewContainer = $(this).attr('id') === 'profile_picture_file' ? $(this).siblings('.profile-preview') : $('#pet_photo_preview');
        
        if (file) {
            const formData = new FormData();
            formData.append('action', 'amal_upload_image');
            formData.append('nonce', amal_ajax.nonce);
            formData.append('image', file);
            
            $.ajax({
                url: amal_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $(targetInput).val(response.data.url);
                        
                        // Update preview
                        if (inputId === 'profile_picture_file') {
                            if (previewContainer.length) {
                                previewContainer.attr('src', response.data.url);
                            } else {
                                $(targetInput).after('<img src="' + response.data.url + '" alt="Profile Picture" class="profile-preview">');
                            }
                        } else {
                            previewContainer.html('<img src="' + response.data.url + '" alt="Pet Photo">');
                        }
                        
                        $('#profile-message').html('<div class="success">Image uploaded successfully</div>');
                    } else {
                        $('#profile-message').html('<div class="error">' + response.data.message + '</div>');
                    }
                },
                error: function() {
                    $('#profile-message').html('<div class="error">Image upload failed. Please try again.</div>');
                }
            });
        }
    });
    
    // Pet management functions
    $('#add-pet-btn').on('click', function() {
        openPetModal();
    });
    
    $('.edit-pet-btn').on('click', function() {
        const petId = $(this).data('pet-id');
        openPetModal(petId);
    });
    
    $('.delete-pet-btn').on('click', function() {
        const petId = $(this).data('pet-id');
        if (confirm('Are you sure you want to delete this pet? This action cannot be undone.')) {
            deletePet(petId);
        }
    });
    
    // Service management functions
    $('#add-service-btn').on('click', function() {
        openServiceModal();
    });
    
    $('.edit-service-btn').on('click', function() {
        const serviceId = $(this).data('service-id');
        openServiceModal(serviceId);
    });
    
    $('.delete-service-btn').on('click', function() {
        const serviceId = $(this).data('service-id');
        if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
            deleteService(serviceId);
        }
    });
    
    // Pet form submission
    $('#pet-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const petId = $('#pet_id').val();
        const isEdit = petId && petId !== '';
        
        submitBtn.prop('disabled', true).text(isEdit ? 'Updating...' : 'Adding...');
        
        const formData = {
            action: isEdit ? 'amal_update_pet' : 'amal_add_pet',
            nonce: amal_ajax.nonce,
            name: $('#pet_name').val(),
            type: $('#pet_type').val(),
            breed: $('#pet_breed').val(),
            age: $('#pet_age').val(),
            weight: $('#pet_weight').val(),
            health_notes: $('#pet_health_notes').val(),
            photo_url: $('#pet_photo').val()
        };
        
        if (isEdit) {
            formData.pet_id = petId;
        }
        
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#profile-message').html('<div class="success">' + response.data.message + '</div>');
                    closePetModal();
                    // Refresh the page to show updated pets
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#profile-message').html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#profile-message').html('<div class="error">Operation failed. Please try again.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Save Pet');
            }
        });
    });
    
    // Service form submission
    $('#service-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const serviceId = $('#service_id').val();
        const isEdit = serviceId && serviceId !== '';
        
        submitBtn.prop('disabled', true).text(isEdit ? 'Updating...' : 'Adding...');
        
        const formData = {
            action: isEdit ? 'amal_update_service' : 'amal_add_service',
            nonce: amal_ajax.nonce,
            title: $('#service_title').val(),
            category: $('#service_category').val(),
            description: $('#service_description').val(),
            price: $('#service_price').val(),
            location: $('#service_location').val(),
            availability: $('#service_availability').val(),
            is_active: $('#service_is_active').is(':checked') ? 1 : 0
        };
        
        if (isEdit) {
            formData.service_id = serviceId;
        }
        
        $.ajax({
            url: amal_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#profile-message').html('<div class="success">' + response.data.message + '</div>');
                    closeServiceModal();
                    // Refresh the page to show updated services
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#profile-message').html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#profile-message').html('<div class="error">Operation failed. Please try again.</div>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Save Service');
            }
        });
    });
    
});

// Pet modal functions
function openPetModal(petId = null) {
    const modal = document.getElementById('pet-modal');
    const title = document.getElementById('pet-modal-title');
    const form = document.getElementById('pet-form');
    
    // Reset form
    form.reset();
    document.getElementById('pet_id').value = '';
    document.getElementById('pet_photo_preview').innerHTML = '';
    
    if (petId) {
        title.textContent = 'Edit Pet';
        // You could load pet data here via AJAX if needed
        document.getElementById('pet_id').value = petId;
    } else {
        title.textContent = 'Add Pet';
    }
    
    modal.style.display = 'flex';
}

function closePetModal() {
    document.getElementById('pet-modal').style.display = 'none';
}

function deletePet(petId) {
    jQuery.ajax({
        url: amal_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'amal_delete_pet',
            nonce: amal_ajax.nonce,
            pet_id: petId
        },
        success: function(response) {
            if (response.success) {
                jQuery('#profile-message').html('<div class="success">' + response.data.message + '</div>');
                // Refresh the page to show updated pets
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                jQuery('#profile-message').html('<div class="error">' + response.data.message + '</div>');
            }
        },
        error: function() {
            jQuery('#profile-message').html('<div class="error">Delete failed. Please try again.</div>');
        }
    });
}

// Service modal functions
function openServiceModal(serviceId = null) {
    const modal = document.getElementById('service-modal');
    if (!modal) return; // Only for service providers
    
    const title = document.getElementById('service-modal-title');
    const form = document.getElementById('service-form');
    
    // Reset form
    form.reset();
    document.getElementById('service_id').value = '';
    document.getElementById('service_is_active').checked = true;
    
    if (serviceId) {
        title.textContent = 'Edit Service';
        document.getElementById('service_id').value = serviceId;
    } else {
        title.textContent = 'Add Service';
    }
    
    modal.style.display = 'flex';
}

function closeServiceModal() {
    const modal = document.getElementById('service-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function deleteService(serviceId) {
    jQuery.ajax({
        url: amal_ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'amal_delete_service',
            nonce: amal_ajax.nonce,
            service_id: serviceId
        },
        success: function(response) {
            if (response.success) {
                jQuery('#profile-message').html('<div class="success">' + response.data.message + '</div>');
                // Refresh the page to show updated services
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                jQuery('#profile-message').html('<div class="error">' + response.data.message + '</div>');
            }
        },
        error: function() {
            jQuery('#profile-message').html('<div class="error">Delete failed. Please try again.</div>');
        }
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const petModal = document.getElementById('pet-modal');
    const serviceModal = document.getElementById('service-modal');
    
    if (event.target === petModal) {
        closePetModal();
    }
    if (event.target === serviceModal) {
        closeServiceModal();
    }
}