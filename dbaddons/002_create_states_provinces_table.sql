-- Migration Script 002: Create States/Provinces Table
-- Purpose: Store states/provinces for all countries
-- Run this script after 001_create_countries_table.sql

CREATE TABLE IF NOT EXISTS states_provinces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    country_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    state_code VARCHAR(10) COMMENT 'State/Province code',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    INDEX idx_country_id (country_id),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
