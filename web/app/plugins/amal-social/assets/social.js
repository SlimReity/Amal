/**
 * Amal Social Media JavaScript
 * 
 * @package AmalSocial
 * @version 1.0.0
 */

(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initializeSocialFeatures();
    });
    
    /**
     * Initialize all social media features
     */
    function initializeSocialFeatures() {
        // Post creation
        $('#create-post-form').on('submit', handlePostCreation);
        
        // Reactions
        $(document).on('click', '.reaction-btn', handleReaction);
        
        // Comments toggle
        $(document).on('click', '.comment-btn', toggleComments);
        
        // Add comment
        $(document).on('submit', '.add-comment-form', handleAddComment);
    }
    
    /**
     * Handle post creation
     */
    function handlePostCreation(e) {
        e.preventDefault();
        
        const form = $(this);
        const content = $('#post-content').val().trim();
        const submitBtn = form.find('button[type="submit"]');
        
        if (!content) {
            showMessage('Please enter some content for your post', 'error');
            return;
        }
        
        // Disable submit button
        submitBtn.prop('disabled', true).text('Posting...');
        
        // Prepare data
        const postData = {
            action: 'amal_create_post',
            content: content,
            nonce: getAjaxNonce()
        };
        
        // Submit via AJAX
        $.post(getAjaxUrl(), postData)
            .done(function(response) {
                if (response.success) {
                    // Clear form
                    $('#post-content').val('');
                    
                    // Show success message
                    showMessage('Post created successfully!', 'success');
                    
                    // Reload posts feed
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage(response.data.message || 'Failed to create post', 'error');
                }
            })
            .fail(function() {
                showMessage('Network error. Please try again.', 'error');
            })
            .always(function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Post');
            });
    }
    
    /**
     * Handle reactions (like/dislike)
     */
    function handleReaction(e) {
        e.preventDefault();
        
        const btn = $(this);
        const postId = btn.data('post-id');
        const reactionType = btn.data('reaction');
        
        // Disable button temporarily
        btn.prop('disabled', true);
        
        const postData = {
            action: 'amal_react_to_post',
            post_id: postId,
            reaction_type: reactionType,
            nonce: getAjaxNonce()
        };
        
        $.post(getAjaxUrl(), postData)
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Update like count
                    btn.closest('.flex').find('.like-count').text(data.like_count);
                    btn.closest('.flex').find('.dislike-count').text(data.dislike_count);
                    
                    // Update button states
                    updateReactionButtons(btn.closest('.flex'), postId);
                    
                    showMessage('Reaction updated!', 'success');
                } else {
                    showMessage(response.data.message || 'Failed to update reaction', 'error');
                }
            })
            .fail(function() {
                showMessage('Network error. Please try again.', 'error');
            })
            .always(function() {
                // Re-enable button
                btn.prop('disabled', false);
            });
    }
    
    /**
     * Toggle comments section
     */
    function toggleComments(e) {
        e.preventDefault();
        
        const btn = $(this);
        const postCard = btn.closest('[data-post-id]');
        const commentsSection = postCard.find('.comments-section');
        
        if (commentsSection.hasClass('hidden')) {
            commentsSection.removeClass('hidden');
            btn.addClass('bg-blue-100 text-blue-600');
            
            // Load comments if not already loaded
            loadComments(postCard.data('post-id'));
        } else {
            commentsSection.addClass('hidden');
            btn.removeClass('bg-blue-100 text-blue-600');
        }
    }
    
    /**
     * Load comments for a post
     */
    function loadComments(postId) {
        const commentsList = $(`[data-post-id="${postId}"] .comments-list`);
        
        // For now, show a placeholder. In a full implementation, this would load actual comments
        if (commentsList.children().length === 0) {
            commentsList.html('<div class="text-gray-500 text-sm text-center py-4">No comments yet. Be the first to comment!</div>');
        }
    }
    
    /**
     * Handle adding a comment
     */
    function handleAddComment(e) {
        e.preventDefault();
        
        const form = $(this);
        const postId = form.data('post-id');
        const commentText = form.find('textarea[name="comment"]').val().trim();
        const submitBtn = form.find('button[type="submit"]');
        
        if (!commentText) {
            showMessage('Please enter a comment', 'error');
            return;
        }
        
        // Disable submit button
        submitBtn.prop('disabled', true).text('Posting...');
        
        // For now, just add the comment locally
        // In a full implementation, this would save to database
        const commentHtml = createCommentHTML(commentText);
        const commentsList = form.closest('.comments-section').find('.comments-list');
        
        if (commentsList.find('.text-gray-500').length) {
            commentsList.empty();
        }
        
        commentsList.append(commentHtml);
        
        // Clear form
        form.find('textarea').val('');
        
        showMessage('Comment added!', 'success');
        
        // Re-enable submit button
        submitBtn.prop('disabled', false).text('Comment');
    }
    
    /**
     * Create comment HTML
     */
    function createCommentHTML(commentText) {
        const now = new Date();
        const timeAgo = 'just now';
        
        return `
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm">
                    ${getCurrentUserInitials()}
                </div>
                <div class="flex-1">
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <div class="font-medium text-sm text-gray-900">${getCurrentUserName()}</div>
                        <p class="text-gray-800 text-sm">${escapeHtml(commentText)}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">${timeAgo}</div>
                </div>
            </div>
        `;
    }
    
    /**
     * Update reaction button states
     */
    function updateReactionButtons(container, postId) {
        // This would typically fetch the user's current reaction state
        // For now, we'll just toggle the active state
        container.find('.reaction-btn').each(function() {
            const btn = $(this);
            const reactionType = btn.data('reaction');
            
            // Remove active classes
            btn.removeClass('bg-green-100 text-green-600 bg-red-100 text-red-600');
            
            // This is a simplified implementation
            // In reality, you'd check the server response for the user's current reaction
        });
    }
    
    /**
     * Show message to user
     */
    function showMessage(message, type = 'info') {
        const alertClass = type === 'error' ? 'bg-red-100 text-red-700 border-red-300' : 
                          type === 'success' ? 'bg-green-100 text-green-700 border-green-300' : 
                          'bg-blue-100 text-blue-700 border-blue-300';
        
        const alertHtml = `
            <div class="fixed top-4 right-4 z-50 max-w-sm w-full">
                <div class="${alertClass} border rounded-md p-4 shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-medium">${escapeHtml(message)}</p>
                        </div>
                        <button type="button" class="ml-4 text-gray-400 hover:text-gray-600" onclick="$(this).closest('.fixed').remove()">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('.fixed').last().fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * Get AJAX URL
     */
    function getAjaxUrl() {
        return window.amal_social_ajax ? window.amal_social_ajax.ajaxurl : '/wp-admin/admin-ajax.php';
    }
    
    /**
     * Get AJAX nonce
     */
    function getAjaxNonce() {
        return window.amal_social_ajax ? window.amal_social_ajax.nonce : '';
    }
    
    /**
     * Get current user initials (placeholder)
     */
    function getCurrentUserInitials() {
        // This would typically come from a global JS variable
        return 'ME';
    }
    
    /**
     * Get current user name (placeholder)
     */
    function getCurrentUserName() {
        // This would typically come from a global JS variable
        return 'You';
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
})(jQuery);