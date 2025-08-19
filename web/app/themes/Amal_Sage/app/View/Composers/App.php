<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class App extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        '*',
    ];

    /**
     * Retrieve the site name.
     */
    public function siteName(): string
    {
        return get_bloginfo('name', 'display');
    }

    /**
     * Retrieve the main navigation items.
     */
    public function navigationItems(): array
    {
        $items = [
            [
                'title' => __('Home', 'sage'),
                'url' => home_url('/'),
                'current' => is_front_page(),
            ],
            [
                'title' => __('Store', 'sage'),
                'url' => home_url('/store/'),
                'current' => is_page_template('template-store.blade.php'),
            ],
            [
                'title' => __('Profile', 'sage'),
                'url' => home_url('/profile/'),
                'current' => is_page_template('template-profile.blade.php'),
            ],
        ];

        // Add blog/posts link if not using static front page
        if (get_option('show_on_front') !== 'page') {
            $items[] = [
                'title' => __('Blog', 'sage'),
                'url' => home_url('/blog/'),
                'current' => is_home() || is_single() || is_category() || is_tag() || is_author(),
            ];
        }

        return apply_filters('sage_navigation_items', $items);
    }
}
