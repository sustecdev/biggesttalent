-- Add voting stages and qualifiers

-- Step 1: Add current_stage to bt_seasons
ALTER TABLE `bt_seasons` 
ADD COLUMN `current_stage` ENUM('national', 'round_1', 'round_2', 'round_3', 'closed') NOT NULL DEFAULT 'national' AFTER `is_active`;

-- Step 2: Add stage to bt_votes
ALTER TABLE `bt_votes` 
ADD COLUMN `stage` VARCHAR(50) NOT NULL DEFAULT 'national' AFTER `nomination_id`;

-- Step 3: Create table for qualifiers
CREATE TABLE IF NOT EXISTS `bt_contest_stage_qualifiers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `season_id` INT NOT NULL,
  `nomination_id` INT NOT NULL,
  `stage` VARCHAR(50) NOT NULL COMMENT 'The stage the participant QUALIFIED FOR (e.g. round_1 means they are in Round 1)',
  `qualified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_qualifier` (`season_id`, `nomination_id`, `stage`),
  INDEX `idx_stage` (`stage`),
  FOREIGN KEY (`season_id`) REFERENCES `bt_seasons`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`nomination_id`) REFERENCES `bt_nominations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
