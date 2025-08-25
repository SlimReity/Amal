# Social Media Plugin Frontend Setup Guide

## Overview
The Amal Social Media Plugin frontend has been enhanced with a dedicated social media page that matches the homepage design system. This implementation provides a seamless user experience while leveraging the existing social media plugin functionality.

## Files Created/Modified

### New Templates
- `web/app/themes/Amal_Sage/resources/views/template-social.blade.php` - Dedicated social media page template

### Modified Files
- `web/app/themes/Amal_Sage/app/View/Composers/App.php` - Updated navigation logic to include Community link
- `web/app/themes/Amal_Sage/resources/views/template-home.blade.php` - Added Community CTAs

## Features Implemented

### Design Consistency
- **Color Scheme**: Matches homepage blue gradient (`from-blue-600 to-blue-800`)
- **Typography**: Uses same font stack and sizing as homepage
- **Layout**: Responsive card-based design with proper spacing
- **Components**: Consistent button styles, form inputs, and shadows

### Enhanced User Experience
- **Smart Authentication**: Shows post creation for logged-in users, login prompt for guests
- **Responsive Design**: Mobile-optimized layout (tested at 375px width)
- **Visual Feedback**: Hover effects, focus states, and transitions
- **Loading States**: Proper loading indicators and error handling

### Integration Features
- **Existing Plugin**: Reuses existing `AmalSocialPlugin` backend functionality
- **AJAX Integration**: Uses existing AJAX handlers for post creation and loading
- **Enhanced CSS Classes**: Styled to match theme design system
- **Conditional Navigation**: Shows "Community" link for all users in main navigation
- **Homepage CTAs**: Dynamic call-to-action buttons for Community access

## Navigation Integration

The social media system is integrated into the site navigation:

### Header Navigation
- **All Users**: See "Community" link in main navigation (between Home and Store)

### Homepage Integration
- **Guests**: "Community" button leads to social page with login prompt
- **Logged-in Users**: "Community" button leads to full social experience

## CSS Architecture

### Template Integration
- Inline styles in `template-social.blade.php` for social-specific enhancements
- Leverages existing plugin styles in `web/app/plugins/amal-social/assets/social.css`
- Maintains accessibility and responsive design principles

### Enhanced Styling Features
- Post creation form with focus states
- Hover effects on post cards
- Gradient button styling matching homepage
- Loading spinner animations
- Responsive breakpoints for mobile

## How to Expose the Frontend

### Method 1: WordPress Page Creation (Recommended)
1. **Login to WordPress Admin**
   ```
   Navigate to: /wp-admin/
   ```

2. **Create Social Media Page**
   - Go to Pages → Add New
   - Set Title: "Community" or "Social Feed"
   - Set URL slug: "social"
   - In Page Attributes → Template, select "Social Media"
   - Publish the page

3. **Verify Navigation**
   - The "Community" link should automatically appear in the main navigation
   - Homepage CTAs should link to `/social/`

### Method 2: Programmatic Page Creation
Add this to your theme's `functions.php` or a plugin:

```php
function amal_create_social_page() {
    // Check if page already exists
    $page = get_page_by_path('social');
    
    if (!$page) {
        wp_insert_post([
            'post_title' => 'Community',
            'post_name' => 'social',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'template-social.blade.php'
        ]);
    }
}
add_action('after_setup_theme', 'amal_create_social_page');
```

### Method 3: Direct URL Access
- Once a page is created with the social template, users can directly access `/social/`
- The template handles both authenticated and non-authenticated states

## Plugin Dependencies

### Required Plugins
- **Amal Social**: `web/app/plugins/amal-social/` (provides backend functionality)
- **Amal Auth**: `web/app/plugins/amal-auth/` (provides authentication functions)

### Required Database Tables
The social plugin creates these tables automatically:
- `wp_amal_social_posts`
- `wp_amal_social_reactions`
- `wp_amal_users` (from auth plugin)

### JavaScript Dependencies
- **jQuery**: Included with WordPress
- **Social Plugin JS**: `web/app/plugins/amal-social/assets/social.js`

## Testing the Implementation

### Manual Testing
1. **Desktop View**: Navigate to `/social/` and test post creation (if logged in)
2. **Mobile View**: Test responsive design on mobile devices (< 768px width)
3. **Navigation**: Verify header navigation shows "Community" link
4. **Authentication States**: Test both logged-in and guest experiences

### Browser Testing
- Tested with responsive design (375px mobile width)
- Verified social media functionality
- Confirmed styling consistency with homepage

## Functionality Overview

### For Logged-In Users
- **Post Creation**: Rich text area with styled submit button
- **Social Feed**: View all community posts with like/dislike functionality
- **Profile Integration**: Click user names to view profiles
- **Real-time Updates**: AJAX-powered posting and reactions

### For Guest Users
- **Community Preview**: View all posts without ability to interact
- **Authentication Prompt**: Styled call-to-action to sign in or register
- **Seamless Redirect**: Direct links to existing auth pages

### Post Features
- **Rich Content**: Support for multi-line text posts
- **User Attribution**: Shows poster name, type, and timestamp
- **Reactions**: Like/dislike system with real-time counts
- **Comments**: Framework ready for comment system expansion

## Maintenance Notes

### Future Enhancements
- Comment system can be added without affecting current design
- Image upload functionality can be integrated seamlessly
- Additional post types (videos, links) can be added
- User tagging and hashtag support can be implemented

### Plugin Updates
- Template styles are separate from core plugin styles
- Plugin updates won't affect template-specific enhancements
- Base functionality remains unchanged

### Performance Considerations
- AJAX loading for better user experience
- Pagination support for large post volumes
- Optimized database queries in existing plugin
- CSS/JS minification via theme build system

## Accessibility Features
- **Keyboard Navigation**: All interactive elements are keyboard accessible
- **Focus Indicators**: Clear focus states for form elements and buttons
- **Screen Readers**: Proper heading hierarchy and ARIA labels
- **Color Contrast**: Maintains accessibility standards with blue color scheme
- **Responsive Design**: Works across all device sizes and orientations

This implementation provides a production-ready social media frontend that seamlessly integrates with the existing Amal platform while maintaining design consistency and user experience standards.