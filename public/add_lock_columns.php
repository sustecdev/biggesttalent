<?php
/**
 * Add Nomination and Voting Lock Columns
 */

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();
$db = Database::getInstance();
$conn = $db->getConnection();

echo "Updating bt_seasons table...\n";

// Helper function
function columnExists($conn, $table, $column)
{
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

// 1. Add is_nominations_open
if (!columnExists($conn, 'bt_seasons', 'is_nominations_open')) {
    $sql = "ALTER TABLE `bt_seasons` ADD COLUMN `is_nominations_open` TINYINT(1) DEFAULT 1 AFTER `is_active`";
    if ($conn->query($sql))
        echo "✅ Added column 'is_nominations_open'.\n";
    else
        echo "❌ Error adding 'is_nominations_open': " . $conn->error . "\n";
} else {
    echo "ℹ️ Column 'is_nominations_open' already exists.\n";
}

// 2. Add is_voting_open
if (!columnExists($conn, 'bt_seasons', 'is_voting_open')) {
    $sql = "ALTER TABLE `bt_seasons` ADD COLUMN `is_voting_open` TINYINT(1) DEFAULT 1 AFTER `is_nominations_open`";
    if ($conn->query($sql))
        echo "✅ Added column 'is_voting_open'.\n";
    else
        echo "❌ Error adding 'is_voting_open': " . $conn->error . "\n";
} else {
    echo "ℹ️ Column 'is_voting_open' already exists.\n";
}

echo "Done.\n";
