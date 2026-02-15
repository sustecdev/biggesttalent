<?php
// Script to add 'province' column to 'bt_nominations' table

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

// Load Config
Config::load();

// Init DB connection
$db = Database::getInstance();
$mysqli = $db->getConnection();

echo "Checking database schema...\n";

// Check if column exists
$checkQuery = "SHOW COLUMNS FROM `bt_nominations` LIKE 'province'";
$result = $mysqli->query($checkQuery);

if ($result && $result->num_rows > 0) {
    echo "Column 'province' already exists in 'bt_nominations'.\n";
} else {
    echo "Column 'province' not found. Adding it now...\n";

    // Add column after 'country'
    $alterQuery = "ALTER TABLE `bt_nominations` ADD COLUMN `province` VARCHAR(100) NULL AFTER `country`";

    if ($mysqli->query($alterQuery)) {
        echo "Successfully added 'province' column to 'bt_nominations'.\n";
    } else {
        echo "Error adding column: " . $mysqli->error . "\n";
    }
}

echo "Done.\n";
