<?php
/**
 * Fix Null Season IDs
 * Updates existing nominations with NULL season_id to the current active season
 */

// Handle CLI or Web execution
if (php_sapi_name() !== 'cli') {
    echo "<pre>";
}

require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;
use App\Models\Season;

// Load Config
Config::load();

echo "Starting Season ID Fix...\n";

// Init DB
$db = Database::getInstance();
$mysqli = $db->getConnection();

// Get Active Season
$seasonModel = new Season();
$activeSeason = $seasonModel->getActiveSeason();

if (!$activeSeason) {
    die("Error: No active season found. Please activate a season in the admin panel first.\n");
}

$activeSeasonId = (int) $activeSeason['id'];
echo "Active Season ID: " . $activeSeasonId . " (" . $activeSeason['title'] . ")\n";

// Count nominations with NULL season_id
$checkQuery = "SELECT COUNT(*) as count FROM bt_nominations WHERE season_id IS NULL";
$checkResult = $mysqli->query($checkQuery);
$count = 0;
if ($checkResult && $row = $checkResult->fetch_assoc()) {
    $count = (int) $row['count'];
}

echo "Found $count nominations with NULL season_id.\n";

if ($count > 0) {
    // Update nominations
    $updateQuery = "UPDATE bt_nominations SET season_id = $activeSeasonId WHERE season_id IS NULL";

    if ($mysqli->query($updateQuery)) {
        echo "Successfully updated $count nominations.\n";
    } else {
        echo "Error updating nominations: " . $mysqli->error . "\n";
    }
} else {
    echo "No updates needed.\n";
}

echo "Done.\n";
