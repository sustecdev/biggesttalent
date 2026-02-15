-- Migration Script 005: Add Season Activation System
-- Purpose: Add is_active column to bt_seasons and season_id to bt_nominations
-- Run this script after previous migrations

-- Add is_active column to bt_seasons table
ALTER TABLE bt_seasons 
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Only one season can be active at a time',
ADD INDEX idx_is_active (is_active);

-- Add season_id column to bt_nominations table
ALTER TABLE bt_nominations 
ADD COLUMN IF NOT EXISTS season_id INT NULL COMMENT 'Links nomination to a specific season',
ADD INDEX idx_season_id (season_id);

-- Add foreign key constraint (optional, but recommended for data integrity)
-- Note: This will only work if all existing nominations have valid season_id or NULL
ALTER TABLE bt_nominations
ADD CONSTRAINT fk_nominations_season 
FOREIGN KEY (season_id) REFERENCES bt_seasons(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Set the most recent season as active if no season is currently active
UPDATE bt_seasons 
SET is_active = 1 
WHERE id = (
    SELECT id FROM (
        SELECT id FROM bt_seasons 
        ORDER BY start_date DESC 
        LIMIT 1
    ) AS temp
)
AND (SELECT COUNT(*) FROM bt_seasons WHERE is_active = 1) = 0;
