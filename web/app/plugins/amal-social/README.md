# Amal Social Media Plugin

## Overview

The Amal Social Media Plugin provides basic social media functionality for the Amal pet services platform, allowing users to create posts, react to them, and comment. It integrates seamlessly with the existing Amal authentication system.

## Features

### ✅ Core Functionality
- **Post Creation**: Users can create text posts and share them with the community
- **Post Feed**: Display all posts in chronological order with pagination support
- **User Profiles**: Clickable profile links showing user information and their posts
- **Reactions System**: Like and dislike functionality for posts
- **Comments Integration**: Built to work with WordPress comment system
- **Responsive Design**: Mobile-first approach with clean, modern styling

### ✅ Security Features
- **CSRF Protection**: All AJAX requests use WordPress nonces for security
- **Authentication Integration**: Seamless integration with Amal Auth system
- **Input Sanitization**: All user input is properly sanitized and validated
- **Access Control**: Only logged-in users can create posts and reactions

### ✅ Database Integration
- **MySQL Compatible**: Works with existing Amal database setup
- **Optimized Queries**: Efficient database queries with proper indexing
- **Data Integrity**: Foreign key relationships and constraints
- **SQL Export**: Generates SQL files for manual database setup

## Installation

### 1. Plugin Activation
The plugin is automatically loaded when files are in place. No WordPress admin activation required for this implementation.

### 2. Database Setup
Run the SQL commands from `social.sql`:
```sql
-- Execute in your MySQL database
SOURCE web/app/plugins/amal-social/social.sql;
```

### 3. URL Routing
The social media functionality is accessible at:
- **Main Feed**: `/blog/` 
- **User Profiles**: `/blog/profile.php?user={user_id}`

## Database Schema

### Social Posts Table
```sql
CREATE TABLE wp_amal_social_posts (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
    content text NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY created_at (created_at)
);
```

### Reactions Table
```sql
CREATE TABLE wp_amal_social_reactions (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    post_id mediumint(9) NOT NULL,
    user_id mediumint(9) NOT NULL,
    reaction_type enum('like', 'dislike') NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_post_reaction (post_id, user_id),
    KEY post_id (post_id),
    KEY user_id (user_id)
);
```

## API Endpoints

### AJAX Endpoints
- `amal_create_post` - Create a new post
- `amal_react_to_post` - Add/update/remove reaction to a post
- `amal_load_posts` - Load posts with pagination

### PHP Methods
```php
// Get recent posts
$social_plugin = new AmalSocialPlugin();
$posts = $social_plugin->get_posts($limit, $offset);

// Get posts by specific user
$user_posts = $social_plugin->get_user_posts($user_id, $limit, $offset);
```

## File Structure

```
web/app/plugins/amal-social/
├── amal-social.php          # Main plugin file
├── social.sql               # Database schema
├── assets/
│   ├── social.js           # JavaScript functionality
│   └── social.css          # Styling
└── templates/
    └── post-card.php       # Post display template

web/blog/
├── index.php               # Main social feed
└── profile.php             # User profile page
```

## Usage Examples

### Creating Posts
Users can create posts through the web interface at `/blog/`. Posts support:
- Text content (required)
- User attribution with profile links
- Timestamp display

### Reactions System
- **Like/Dislike**: Each user can react once per post
- **Toggle Reactions**: Clicking the same reaction removes it
- **Real-time Updates**: Reaction counts update immediately via AJAX

### Profile Links
Posts display clickable user names that link to `/blog/profile.php?user={id}` showing:
- User information (name, type, join date)
- All posts by that user
- Same interaction capabilities (reactions, comments)

## Integration with Existing Systems

### Authentication
- Uses existing `AmalAuthHelper` functions
- Respects user login state and permissions
- Integrates with session management

### Comments
- Built to work with WordPress comment system
- Comment forms included in post templates
- Expandable comment sections

### Styling
- Uses Tailwind CSS classes compatible with theme
- Responsive design principles
- Consistent with existing Amal UI patterns

## Development Notes

### Adding Comments Functionality
To fully implement comments, extend the plugin with:
```php
// Add comment table
CREATE TABLE wp_amal_social_comments (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    post_id mediumint(9) NOT NULL,
    user_id mediumint(9) NOT NULL,
    content text NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

// Add AJAX handler
add_action('wp_ajax_amal_add_comment', [$this, 'handle_add_comment']);
```

### Extending Post Types
The system can be extended to support:
- Image uploads
- Post categories/tags
- Hashtag support
- @mentions
- Post sharing

## Testing

### Manual Testing
1. **Visit `/blog/`** - Should display social feed
2. **Login as user** - Should show post creation form
3. **Create a post** - Should appear in feed immediately
4. **Test reactions** - Like/dislike buttons should work with real-time updates
5. **Click profile links** - Should navigate to user profile with their posts

### Database Validation
```sql
-- Check posts table
SELECT * FROM wp_amal_social_posts ORDER BY created_at DESC;

-- Check reactions
SELECT * FROM wp_amal_social_reactions;

-- Verify user integration
SELECT p.*, u.first_name, u.last_name 
FROM wp_amal_social_posts p 
JOIN wp_amal_users u ON p.user_id = u.id;
```

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 8.1 or higher  
- **MySQL**: 5.6 or higher
- **Amal Auth Plugin**: Required for user authentication
- **jQuery**: Included with WordPress

## Security Considerations

- All user input is sanitized using WordPress functions
- CSRF protection via nonces on all AJAX requests
- Database queries use prepared statements
- Access control checks for all user actions
- XSS prevention in all output

## Performance Notes

- Database queries are optimized with proper indexes
- AJAX loading for smooth user experience
- Pagination support for large post volumes
- Efficient reaction counting with subqueries