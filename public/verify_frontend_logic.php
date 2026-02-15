<?php
/**
 * Verify Frontend Logic
 * Tests the helper functions in functions.php to ensure they respect voting stages.
 */

// Include the autoloader and functions
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

// We need to manually include functions.php as it's not a class
// Adjust path if necessary. Based on file structure, it is in app/helpers/functions.php
require_once '../app/helpers/functions.php';

// Init DB connection for legacy functions
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();

echo "<!DOCTYPE html><html><head><title>Frontend Logic Verify</title><style>body{font-family:sans-serif;max-width:800px;margin:20px auto;line-height:1.5;} h3{border-bottom:1px solid #ccc;} .pass{color:green;} .fail{color:red;} code{background:#eee;padding:2px 5px;}</style></head><body>";

echo "<h1>Frontend Logic Verification</h1>";

// 1. Check Active Season
$season = getActiveSeasonSimple();
echo "<h3>1. Active Season</h3>";
if ($season) {
    echo "ID: " . $season['id'] . "<br>";
    echo "Title: " . $season['title'] . "<br>";
    echo "<strong>Current Stage: " . $season['current_stage'] . "</strong><br>";
} else {
    echo "<span class='fail'>No active season found.</span><br>";
    exit; // Cannot test further
}

// 2. Test getWeeklyLeaderboard
echo "<h3>2. getWeeklyLeaderboard(limit=5)</h3>";
$weekly = getWeeklyLeaderboard(5);
echo "Count: " . count($weekly) . "<br>";
if (count($weekly) > 0) {
    echo "<ul>";
    foreach ($weekly as $item) {
        echo "<li>#" . $item['rank'] . " " . htmlspecialchars($item['aname']) . " (" . $item['vote_count'] . " votes)</li>";
    }
    echo "</ul>";
} else {
    echo "No results (This might be correct if no votes week or no qualifiers).<br>";
}

// 3. Test getAllTimeLeaderboard
echo "<h3>3. getAllTimeLeaderboard(limit=5)</h3>";
$allTime = getAllTimeLeaderboard(5);
echo "Count: " . count($allTime) . "<br>";
if (count($allTime) > 0) {
    echo "<ul>";
    foreach ($allTime as $item) {
        echo "<li>#" . $item['rank'] . " " . htmlspecialchars($item['aname']) . " (" . $item['vote_count'] . " votes)</li>";
    }
    echo "</ul>";
} else {
    echo "No results.<br>";
}

// 4. Test getApprovedContestantsWithVotes
echo "<h3>4. getApprovedContestantsWithVotes(limit=5)</h3>";
$contestants = getApprovedContestantsWithVotes(5);
echo "Count: " . count($contestants) . "<br>";
echo "<em>Should match GetAllTimeLeaderboard logic closely but specific for voting page.</em><br>";
if (count($contestants) > 0) {
    echo "<ul>";
    foreach ($contestants as $item) {
        echo "<li>" . htmlspecialchars($item['aname']) . " (" . $item['vote_count'] . " votes)</li>";
    }
    echo "</ul>";
} else {
    echo "No results.<br>";
}

// 5. Test Search (simulated)
echo "<h3>5. searchNominations('a', limit=3)</h3>";
$search = searchNominations('a', 3);
echo "Count: " . count($search) . "<br>";

echo "</body></html>";
