<?php
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
use App\Core\Config;
use App\Core\Database;

Config::load();
$db = Database::getInstance();
$mysqli = $db->getConnection();

// Check for active season
$seasonQuery = $mysqli->query("SELECT * FROM bt_seasons WHERE is_active = 1 LIMIT 1");
if (!$seasonQuery || $seasonQuery->num_rows === 0) {
    echo "No active season found.\n";
    $anySeason = $mysqli->query("SELECT COUNT(*) as count FROM bt_seasons");
    $row = $anySeason->fetch_assoc();
    echo "Total seasons in DB: " . $row['count'] . "\n";
    exit;
}

$season = $seasonQuery->fetch_assoc();
echo "Active Season: " . $season['title'] . " (ID: " . $season['id'] . ")\n";

// Check contestants
$contestantQuery = $mysqli->query("SELECT COUNT(*) as count FROM bt_nominations WHERE season_id = " . $season['id']);
$contestantRow = $contestantQuery->fetch_assoc();
echo "Total Contestants in Season: " . $contestantRow['count'] . "\n";

// Check approved contestants
$approvedQuery = $mysqli->query("SELECT COUNT(*) as count FROM bt_nominations WHERE season_id = " . $season['id'] . " AND status='approved'");
$approvedRow = $approvedQuery->fetch_assoc();
echo "Approved Contestants in Season: " . $approvedRow['count'] . "\n";

// Check votes
$voteQuery = $mysqli->query("SELECT COUNT(*) as count FROM bt_votes");
$voteRow = $voteQuery->fetch_assoc();
echo "Total Votes in System: " . $voteRow['count'] . "\n";
