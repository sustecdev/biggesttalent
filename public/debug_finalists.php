<?php
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
use App\Core\Config;
use App\Core\Database;

Config::load();
$db = Database::getInstance();
$mysqli = $db->getConnection();

// Get active season
$seasonQuery = $mysqli->query("SELECT * FROM bt_seasons WHERE is_active = 1 LIMIT 1");
if (!$seasonQuery || $seasonQuery->num_rows === 0) {
    die("No active season found.\n");
}
$season = $seasonQuery->fetch_assoc();
echo "Active Season: " . $season['title'] . " (ID: " . $season['id'] . ")\n";
echo "Current Stage: " . $season['current_stage'] . "\n";

$seasonId = $season['id'];
$stage = $season['current_stage'];

// Check qualifiers for this stage
$query = "SELECT COUNT(*) as count FROM bt_contest_stage_qualifiers WHERE season_id = $seasonId AND stage = '$stage'";
$res = $mysqli->query($query);
$count = $res->fetch_assoc()['count'];
echo "Qualifiers for '$stage': $count\n";

if ($count == 0) {
    echo "WARNING: No qualifiers found for current stage!\n";

    // Check previous stage qualifiers?
    // If stage is round_3, check round_2
    if ($stage == 'round_3') {
        $prev = 'round_2';
        $qPrev = $mysqli->query("SELECT COUNT(*) as count FROM bt_contest_stage_qualifiers WHERE season_id = $seasonId AND stage = '$prev'");
        echo "Qualifiers for previous stage '$prev': " . $qPrev->fetch_assoc()['count'] . "\n";
    }
} else {
    // List them
    $list = $mysqli->query("SELECT n.aname, n.id FROM bt_contest_stage_qualifiers q JOIN bt_nominations n ON q.nomination_id = n.id WHERE q.season_id = $seasonId AND q.stage = '$stage'");
    while ($row = $list->fetch_assoc()) {
        echo "- " . $row['aname'] . " (" . $row['id'] . ")\n";
    }
}
