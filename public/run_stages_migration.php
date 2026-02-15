<?php
/**
 * Run Voting Stages Migration
 * This script runs the 006_add_voting_stages.sql migration
 */

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

echo "Running Voting Stages Migration...\n";

$db = Database::getInstance();
$conn = $db->getConnection();

// Helper function to check if column exists
function columnExists($conn, $table, $column)
{
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

// Step 1: Add current_stage to bt_seasons
if (!columnExists($conn, 'bt_seasons', 'current_stage')) {
    echo "Adding current_stage to bt_seasons...\n";
    $sql1 = "ALTER TABLE `bt_seasons` 
             ADD COLUMN `current_stage` ENUM('national', 'round_1', 'round_2', 'round_3', 'closed') NOT NULL DEFAULT 'national' AFTER `is_active`";
    if ($conn->query($sql1)) {
        echo "✅ Added current_stage.\n";
    } else {
        echo "❌ Error adding current_stage: " . $conn->error . "\n";
    }
} else {
    echo "ℹ️ current_stage already exists.\n";
}

// Step 2: Add stage to bt_votes
if (!columnExists($conn, 'bt_votes', 'stage')) {
    echo "Adding stage to bt_votes...\n";
    $sql2 = "ALTER TABLE `bt_votes` 
             ADD COLUMN `stage` VARCHAR(50) NOT NULL DEFAULT 'national' AFTER `nomination_id`";
    if ($conn->query($sql2)) {
        echo "✅ Added stage.\n";
    } else {
        echo "❌ Error adding stage: " . $conn->error . "\n";
    }
} else {
    echo "ℹ️ stage already exists.\n";
}

// Step 3: Create qualifiers table
echo "Creating bt_contest_stage_qualifiers table...\n";
$sql3 = "CREATE TABLE IF NOT EXISTS `bt_contest_stage_qualifiers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `season_id` INT NOT NULL,
  `nomination_id` INT NOT NULL,
  `stage` VARCHAR(50) NOT NULL,
  `qualified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_qualifier` (`season_id`, `nomination_id`, `stage`),
  INDEX `idx_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql3)) {
    echo "✅ Created/Verified bt_contest_stage_qualifiers.\n";
} else {
    echo "❌ Error creating table: " . $conn->error . "\n";
}

echo "Migration Complete.\n";
