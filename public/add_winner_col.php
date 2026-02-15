<?php
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
use App\Core\Config;
use App\Core\Database;

Config::load();
$db = Database::getInstance();
$mysqli = $db->getConnection();

// Check if column exists
$check = $mysqli->query("SHOW COLUMNS FROM bt_seasons LIKE 'winner_id'");
if ($check->num_rows == 0) {
    echo "Adding winner_id column...\n";
    $mysqli->query("ALTER TABLE bt_seasons ADD COLUMN winner_id INT DEFAULT NULL");
    echo "Column added.\n";
} else {
    echo "Column winner_id already exists.\n";
}
