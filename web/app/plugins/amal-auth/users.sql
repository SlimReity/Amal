-- Amal Authentication System Database Schema
-- Generated on 2024-08-17 00:13:00

CREATE TABLE IF NOT EXISTS wp_amal_users (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    email varchar(100) NOT NULL,
    password_hash varchar(255) NOT NULL,
    first_name varchar(50) DEFAULT '',
    last_name varchar(50) DEFAULT '',
    user_type enum('pet_owner', 'service_provider') DEFAULT 'pet_owner',
    registration_date datetime DEFAULT CURRENT_TIMESTAMP,
    last_login datetime DEFAULT NULL,
    is_active tinyint(1) DEFAULT 1,
    email_verified tinyint(1) DEFAULT 0,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample INSERT statements will be added here when users register