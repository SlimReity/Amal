-- Amal Profile Management System Database Migration
-- Generated on 2024-08-18
-- This file contains database schema updates for the profile management system

-- Update existing users table with new fields for profile management
ALTER TABLE wp_amal_users 
ADD COLUMN phone varchar(20) DEFAULT '',
ADD COLUMN address text DEFAULT '',
ADD COLUMN profile_picture varchar(255) DEFAULT '',
ADD COLUMN notification_email tinyint(1) DEFAULT 1,
ADD COLUMN notification_push tinyint(1) DEFAULT 1,
ADD COLUMN notification_sms tinyint(1) DEFAULT 0,
ADD COLUMN subscription_type enum('free', 'premium') DEFAULT 'free';

-- Create pets table
CREATE TABLE IF NOT EXISTS wp_amal_pets (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    owner_id mediumint(9) NOT NULL,
    name varchar(100) NOT NULL,
    type varchar(50) NOT NULL,
    breed varchar(100) DEFAULT '',
    age int DEFAULT NULL,
    weight decimal(5,2) DEFAULT NULL,
    health_notes text DEFAULT '',
    photo_url varchar(255) DEFAULT '',
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (owner_id) REFERENCES wp_amal_users(id) ON DELETE CASCADE,
    INDEX idx_owner_id (owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create services table
CREATE TABLE IF NOT EXISTS wp_amal_services (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    provider_id mediumint(9) NOT NULL,
    title varchar(200) NOT NULL,
    category varchar(100) NOT NULL,
    description text DEFAULT '',
    price decimal(10,2) NOT NULL,
    availability text DEFAULT '',
    location varchar(255) DEFAULT '',
    is_active tinyint(1) DEFAULT 1,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (provider_id) REFERENCES wp_amal_users(id) ON DELETE CASCADE,
    INDEX idx_provider_id (provider_id),
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create bookings table
CREATE TABLE IF NOT EXISTS wp_amal_bookings (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
    service_id mediumint(9) NOT NULL,
    pet_id mediumint(9) DEFAULT NULL,
    booking_date datetime NOT NULL,
    status enum('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes text DEFAULT '',
    total_amount decimal(10,2) NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES wp_amal_users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES wp_amal_services(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES wp_amal_pets(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_service_id (service_id),
    INDEX idx_pet_id (pet_id),
    INDEX idx_booking_date (booking_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;