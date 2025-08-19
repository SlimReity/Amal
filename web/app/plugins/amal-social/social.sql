-- Amal Social Media Database Schema
-- Generated on 2025-08-19 07:45:00

CREATE TABLE IF NOT EXISTS wp_amal_social_posts (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
    content text NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY created_at (created_at)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;

CREATE TABLE IF NOT EXISTS wp_amal_social_reactions (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    post_id mediumint(9) NOT NULL,
    user_id mediumint(9) NOT NULL,
    reaction_type enum('like', 'dislike') NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_post_reaction (post_id, user_id),
    KEY post_id (post_id),
    KEY user_id (user_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;

-- Sample INSERT statements will be added here when posts are created