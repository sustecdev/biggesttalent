<?php
/**
 * Add profile_photo column to bt_nominations.
 * Run: php dbaddons/add_profile_photo.php
 */
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
\App\Core\Config::load();

$db = \App\Core\Database::getInstance();
$conn = $db->getConnection();

$check = $conn->query("SHOW COLUMNS FROM bt_nominations LIKE 'profile_photo'");
if ($check && $check->num_rows > 0) {
    echo "Column profile_photo already exists.\n";
    exit(0);
}

$sql = "ALTER TABLE bt_nominations ADD COLUMN profile_photo VARCHAR(500) DEFAULT NULL AFTER thumbnail";
if ($conn->query($sql)) {
    echo "Added profile_photo column to bt_nominations.\n";
} else {
    echo "Error: " . $conn->error . "\n";
    exit(1);
}
