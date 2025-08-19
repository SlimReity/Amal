<?php
/**
 * Amal User Profile Page
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

// Get user ID from query parameter
$user_id = isset($_GET['user']) ? intval($_GET['user']) : 0;

if (!$user_id) {
    wp_redirect('/blog');
    exit;
}

// Get user information
$user = AmalAuthHelper::get_user_by_id($user_id);

if (!$user) {
    wp_redirect('/blog');
    exit;
}

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back to Feed -->
        <div class="mb-6">
            <a href="/blog" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Feed
            </a>
        </div>

        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="flex items-center">
                <div class="w-24 h-24 bg-blue-500 text-white rounded-full flex items-center justify-center mr-6 text-2xl font-bold">
                    <?php echo esc_html(strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1))); ?>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo esc_html($user->first_name . ' ' . $user->last_name); ?>
                    </h1>
                    <p class="text-lg text-gray-600 capitalize mb-2">
                        <?php echo esc_html(str_replace('_', ' ', $user->user_type)); ?>
                    </p>
                    <p class="text-sm text-gray-500">
                        Member since <?php echo esc_html(date('F Y', strtotime($user->registration_date))); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- User's Posts -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Posts by <?php echo esc_html($user->first_name); ?></h2>
            
            <div id="user-posts-feed">
                <?php
                // Load user's posts using the social plugin
                if (class_exists('AmalSocialPlugin')) {
                    $social_plugin = new AmalSocialPlugin();
                    $posts = $social_plugin->get_user_posts($user_id);
                    
                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            include '../app/plugins/amal-social/templates/post-card.php';
                        }
                    } else {
                        echo '<div class="text-center py-12 text-gray-500">' . esc_html($user->first_name) . ' hasn\'t posted anything yet.</div>';
                    }
                } else {
                    echo '<div class="text-center py-12 text-red-500">Social plugin not available. Please ensure amal-social plugin is activated.</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Include social media JavaScript -->
<script src="../app/plugins/amal-social/assets/social.js"></script>

<?php get_footer(); ?>