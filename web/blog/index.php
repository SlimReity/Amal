<?php
/**
 * Amal Social Media Blog - Main Feed
 * 
 * @package AmalSocial
 * @version 1.0.0
 */

// Load WordPress
require_once '../wp-config.php';

// Start session for authentication
if (!session_id()) {
    session_start();
}

// Load Amal Auth Helper
require_once '../app/plugins/amal-auth/includes/helper-functions.php';

// Check if social plugin is available
if (!class_exists('AmalSocialPlugin')) {
    require_once '../app/plugins/amal-social/amal-social.php';
}

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Social Feed</h1>
            <p class="text-gray-600">Share your thoughts and connect with the pet community</p>
        </div>

        <?php if (amal_is_logged_in()): ?>
            <!-- Post Creation Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form id="create-post-form" class="space-y-4">
                    <div>
                        <textarea 
                            id="post-content" 
                            name="content" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="What's on your mind?"
                            required
                        ></textarea>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Posting as: <?php echo esc_html(amal_current_user()->first_name . ' ' . amal_current_user()->last_name); ?></span>
                        <button 
                            type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Post
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Login Prompt -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-center">
                <p class="text-gray-600 mb-4">Join the conversation! Login to create posts and interact with the community.</p>
                <a href="/login" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Login</a>
                <a href="/register" class="ml-4 bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">Register</a>
            </div>
        <?php endif; ?>

        <!-- Posts Feed -->
        <div id="posts-feed">
            <?php
            // Load posts using the social plugin
            if (class_exists('AmalSocialPlugin')) {
                $social_plugin = new AmalSocialPlugin();
                $posts = $social_plugin->get_posts();
                
                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        include '../app/plugins/amal-social/templates/post-card.php';
                    }
                } else {
                    echo '<div class="text-center py-12 text-gray-500">No posts yet. Be the first to share something!</div>';
                }
            } else {
                echo '<div class="text-center py-12 text-red-500">Social plugin not available. Please ensure amal-social plugin is activated.</div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Include social media JavaScript -->
<script src="../app/plugins/amal-social/assets/social.js"></script>

<?php get_footer(); ?>