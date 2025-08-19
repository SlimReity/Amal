<?php
/**
 * Social Media Post Card Template
 * 
 * @package AmalSocial
 */

// Prevent direct access
if (!defined('ABSPATH') && !isset($post)) {
    exit;
}

$time_ago = human_time_diff(strtotime($post->created_at)) . ' ago';
?>

<div class="bg-white rounded-lg shadow-md p-6 mb-6" data-post-id="<?php echo esc_attr($post->id); ?>">
    <!-- Post Header -->
    <div class="flex items-center mb-4">
        <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mr-4">
            <?php echo esc_html(strtoupper(substr($post->first_name, 0, 1) . substr($post->last_name, 0, 1))); ?>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <a href="/profile?user=<?php echo esc_attr($post->user_id); ?>" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                    <?php echo esc_html($post->first_name . ' ' . $post->last_name); ?>
                </a>
                <span class="text-sm text-gray-500 capitalize px-2 py-1 bg-gray-100 rounded">
                    <?php echo esc_html(str_replace('_', ' ', $post->user_type)); ?>
                </span>
            </div>
            <p class="text-sm text-gray-500"><?php echo esc_html($time_ago); ?></p>
        </div>
    </div>
    
    <!-- Post Content -->
    <div class="mb-4">
        <p class="text-gray-800 leading-relaxed"><?php echo nl2br(esc_html($post->content)); ?></p>
    </div>
    
    <!-- Post Actions -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <!-- Reactions -->
        <div class="flex items-center gap-4">
            <button 
                class="reaction-btn flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 transition-colors <?php echo (isset($post->user_reaction) && $post->user_reaction === 'like') ? 'bg-green-100 text-green-600' : 'text-gray-600'; ?>"
                data-post-id="<?php echo esc_attr($post->id); ?>"
                data-reaction="like"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                </svg>
                <span class="like-count"><?php echo esc_html($post->like_count ?? 0); ?></span>
            </button>
            
            <button 
                class="reaction-btn flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 transition-colors <?php echo (isset($post->user_reaction) && $post->user_reaction === 'dislike') ? 'bg-red-100 text-red-600' : 'text-gray-600'; ?>"
                data-post-id="<?php echo esc_attr($post->id); ?>"
                data-reaction="dislike"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.106-1.79l-.05-.025A4 4 0 0011.057 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"/>
                </svg>
                <span class="dislike-count"><?php echo esc_html($post->dislike_count ?? 0); ?></span>
            </button>
        </div>
        
        <!-- Comment Button -->
        <button 
            class="comment-btn flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 transition-colors text-gray-600"
            data-post-id="<?php echo esc_attr($post->id); ?>"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
            </svg>
            Comment
        </button>
    </div>
    
    <!-- Comments Section (Initially Hidden) -->
    <div class="comments-section mt-4 pt-4 border-t border-gray-200 hidden">
        <?php if (function_exists('amal_is_logged_in') && amal_is_logged_in()): ?>
            <!-- Add Comment Form -->
            <form class="add-comment-form mb-4" data-post-id="<?php echo esc_attr($post->id); ?>">
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-gray-300 text-white rounded-full flex items-center justify-center text-sm">
                        <?php 
                        $current_user = amal_current_user();
                        echo esc_html(strtoupper(substr($current_user->first_name, 0, 1) . substr($current_user->last_name, 0, 1))); 
                        ?>
                    </div>
                    <div class="flex-1">
                        <textarea 
                            name="comment" 
                            rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            placeholder="Write a comment..."
                            required
                        ></textarea>
                        <div class="mt-2 flex justify-end">
                            <button 
                                type="submit" 
                                class="bg-blue-600 text-white px-4 py-1 rounded-md hover:bg-blue-700 text-sm"
                            >
                                Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        
        <!-- Comments List -->
        <div class="comments-list space-y-3">
            <!-- Comments will be loaded here via AJAX -->
        </div>
    </div>
</div>