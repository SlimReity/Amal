/**
 * Amal Profile Management Plugin - JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initTabNavigation();
        initFormHandlers();
        initActionButtons();
    });

    /**
     * Initialize tab navigation functionality
     */
    function initTabNavigation() {
        $('.amal-tab-btn').on('click', function(e) {
            e.preventDefault();
            const tabName = $(this).data('tab');
            showTab(tabName, this);
        });
    }

    /**
     * Show specific tab and update navigation
     */
    function showTab(tabName, clickedButton) {
        // Hide all tab contents
        $('.amal-tab-content').removeClass('active');
        
        // Remove active class from all tab buttons
        $('.amal-tab-btn').removeClass('active');
        
        // Show selected tab content
        $('#amal-tab-' + tabName).addClass('active');
        
        // Add active class to clicked button
        $(clickedButton).addClass('active');
    }

    /**
     * Initialize form handlers for profile management
     */
    function initFormHandlers() {
        // Profile form submission
        $('#amal-profile-form').on('submit', function(e) {
            e.preventDefault();
            handleProfileUpdate();
        });

        // Pet form submission
        $('#amal-pet-form').on('submit', function(e) {
            e.preventDefault();
            handlePetUpdate();
        });

        // Service form submission
        $('#amal-service-form').on('submit', function(e) {
            e.preventDefault();
            handleServiceUpdate();
        });

        // File upload handling
        $('.amal-file-upload').on('change', function() {
            handleFileUpload(this);
        });
    }

    /**
     * Initialize action buttons (Edit, Delete, etc.)
     */
    function initActionButtons() {
        // Edit buttons
        $(document).on('click', '.amal-btn-edit', function(e) {
            e.preventDefault();
            const itemType = $(this).data('type');
            const itemId = $(this).data('id');
            handleEdit(itemType, itemId);
        });

        // Delete buttons
        $(document).on('click', '.amal-btn-delete', function(e) {
            e.preventDefault();
            const itemType = $(this).data('type');
            const itemId = $(this).data('id');
            handleDelete(itemType, itemId);
        });

        // Add new buttons
        $('.amal-btn-add-new').on('click', function(e) {
            e.preventDefault();
            const itemType = $(this).data('type');
            handleAddNew(itemType);
        });
    }

    /**
     * Handle profile update via AJAX
     */
    function handleProfileUpdate() {
        const formData = new FormData($('#amal-profile-form')[0]);
        formData.append('action', 'amal_update_profile');
        formData.append('nonce', amalProfile.nonce);

        $.ajax({
            url: amalProfile.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading('#amal-profile-form');
            },
            success: function(response) {
                hideLoading('#amal-profile-form');
                if (response.success) {
                    showMessage('success', amalProfile.messages.profileUpdated);
                } else {
                    showMessage('error', response.data.message || amalProfile.messages.error);
                }
            },
            error: function() {
                hideLoading('#amal-profile-form');
                showMessage('error', amalProfile.messages.error);
            }
        });
    }

    /**
     * Handle pet update via AJAX
     */
    function handlePetUpdate() {
        const formData = new FormData($('#amal-pet-form')[0]);
        formData.append('action', 'amal_update_pet');
        formData.append('nonce', amalProfile.nonce);

        $.ajax({
            url: amalProfile.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading('#amal-pet-form');
            },
            success: function(response) {
                hideLoading('#amal-pet-form');
                if (response.success) {
                    showMessage('success', amalProfile.messages.petUpdated);
                    refreshPetList();
                } else {
                    showMessage('error', response.data.message || amalProfile.messages.error);
                }
            },
            error: function() {
                hideLoading('#amal-pet-form');
                showMessage('error', amalProfile.messages.error);
            }
        });
    }

    /**
     * Handle service update via AJAX
     */
    function handleServiceUpdate() {
        const formData = new FormData($('#amal-service-form')[0]);
        formData.append('action', 'amal_update_service');
        formData.append('nonce', amalProfile.nonce);

        $.ajax({
            url: amalProfile.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading('#amal-service-form');
            },
            success: function(response) {
                hideLoading('#amal-service-form');
                if (response.success) {
                    showMessage('success', amalProfile.messages.serviceUpdated);
                    refreshServiceList();
                } else {
                    showMessage('error', response.data.message || amalProfile.messages.error);
                }
            },
            error: function() {
                hideLoading('#amal-service-form');
                showMessage('error', amalProfile.messages.error);
            }
        });
    }

    /**
     * Handle file upload with preview
     */
    function handleFileUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(input).siblings('.amal-image-preview');
                if (preview.length) {
                    preview.attr('src', e.target.result).show();
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * Handle edit action
     */
    function handleEdit(itemType, itemId) {
        // Open edit modal or navigate to edit form
        const editUrl = amalProfile.editUrls[itemType] + '&id=' + itemId;
        window.location.href = editUrl;
    }

    /**
     * Handle delete action
     */
    function handleDelete(itemType, itemId) {
        if (!confirm(amalProfile.messages.confirmDelete)) {
            return;
        }

        $.ajax({
            url: amalProfile.ajaxUrl,
            type: 'POST',
            data: {
                action: 'amal_delete_' + itemType,
                id: itemId,
                nonce: amalProfile.nonce
            },
            beforeSend: function() {
                showLoading('.amal-' + itemType + '-card[data-id="' + itemId + '"]');
            },
            success: function(response) {
                if (response.success) {
                    $('.amal-' + itemType + '-card[data-id="' + itemId + '"]').fadeOut();
                    showMessage('success', amalProfile.messages[itemType + 'Deleted']);
                } else {
                    hideLoading('.amal-' + itemType + '-card[data-id="' + itemId + '"]');
                    showMessage('error', response.data.message || amalProfile.messages.error);
                }
            },
            error: function() {
                hideLoading('.amal-' + itemType + '-card[data-id="' + itemId + '"]');
                showMessage('error', amalProfile.messages.error);
            }
        });
    }

    /**
     * Handle add new item
     */
    function handleAddNew(itemType) {
        const addUrl = amalProfile.addUrls[itemType];
        window.location.href = addUrl;
    }

    /**
     * Refresh pet list
     */
    function refreshPetList() {
        $('#amal-tab-pets .amal-pets-container').load(
            amalProfile.ajaxUrl + '?action=amal_get_pets&nonce=' + amalProfile.nonce
        );
    }

    /**
     * Refresh service list
     */
    function refreshServiceList() {
        $('#amal-tab-services .amal-services-container').load(
            amalProfile.ajaxUrl + '?action=amal_get_services&nonce=' + amalProfile.nonce
        );
    }

    /**
     * Show loading indicator
     */
    function showLoading(selector) {
        $(selector).addClass('amal-loading').append('<div class="amal-spinner"></div>');
    }

    /**
     * Hide loading indicator
     */
    function hideLoading(selector) {
        $(selector).removeClass('amal-loading').find('.amal-spinner').remove();
    }

    /**
     * Show message to user
     */
    function showMessage(type, message) {
        const messageHtml = '<div class="amal-message amal-message-' + type + '">' + message + '</div>';
        $('.amal-profile-container').prepend(messageHtml);
        
        setTimeout(function() {
            $('.amal-message').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Form validation helpers
     */
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/\s/g, ''));
    }

    // Export functions for external use if needed
    window.amalProfile = window.amalProfile || {};
    window.amalProfile.showTab = showTab;
    window.amalProfile.validateEmail = validateEmail;
    window.amalProfile.validatePhone = validatePhone;

})(jQuery);