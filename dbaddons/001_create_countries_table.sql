-- Migration Script 001: Create Countries Table
-- Purpose: Store all world countries with ISO codes
-- Run this script first

CREATE TABLE IF NOT EXISTS countries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    iso_code CHAR(2) NOT NULL UNIQUE COMMENT 'ISO 3166-1 alpha-2 code',
    iso3_code CHAR(3) COMMENT 'ISO 3166-1 alpha-3 code',
    phone_code VARCHAR(10) COMMENT 'International dialing code',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_iso_code (iso_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
