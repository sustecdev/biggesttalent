<?php
/**
 * Debug Stage Progression
 * Analyze current season status and simulate stage progression
 */

require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;
use App\Models\Season;
use App\Models\Vote;

Config::load();

$seasonModel = new Season();
$voteModel = new Vote();

$activeSeason = $seasonModel->getActiveSeason();

echo "<!DOCTYPE html><html><head><title>Stage Debug</title><style>body{font-family:sans-serif;max-width:800px;margin:20px auto;}</style></head><body>";
echo "<h1>Stage Progression Debugger</h1>";

if (!$activeSeason) {
    echo "<p>No active season found.</p>";
    exit;
}

$id = $activeSeason['id'];
$currentStage = $activeSeason['current_stage'];
echo "<h2>Current Season: " . htmlspecialchars($activeSeason['title']) . "</h2>";
echo "<p><strong>Current Stage:</strong> " . htmlspecialchars($currentStage) . "</p>";

// Determine simulation parameters
$limit = 0;
$nextStage = '';
switch ($currentStage) {
    case 'national':
        $nextStage = 'round_1';
        $limit = 32;
        break;
    case 'round_1':
        $nextStage = 'round_2';
        $limit = 16;
        break;
    case 'round_2':
        $nextStage = 'round_3';
        $limit = 8;
        break;
    case 'round_3':
        $nextStage = 'closed';
        break;
}

echo "<h3>Simulation: Moving to $nextStage</h3>";
if ($nextStage === 'closed') {
    echo "<p>Next step is to close the season.</p>";
} else {
    echo "<p>Selecting Top $limit from '$currentStage'...</p>";

    $results = $voteModel->getStageResults($id, $currentStage, $limit);

    echo "<table border='1' cellpadding='5' cellspacing='0' style='width:100%'>";
    echo "<thead><tr><th>Rank</th><th>ID</th><th>Name</th><th>Title</th><th>Country</th><th>Votes ($currentStage)</th><th>Qualifies?</th></tr></thead><tbody>";

    $rank = 1;
    foreach ($results as $row) {
        $qualifies = $rank <= $limit ? "✅ YES" : "❌ NO";
        echo "<tr>";
        echo "<td>$rank</td>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['aname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['country']) . "</td>";
        echo "<td>" . $row['vote_count'] . "</td>";
        echo "<td>$qualifies</td>";
        echo "</tr>";
        $rank++;
    }
    echo "</tbody></table>";

    echo "<p>Total Candidates Found: " . count($results) . "</p>";
}

// Action Button
echo "<br><hr>";
echo "<form method='post' action='" . URLROOT . "/admin/seasons/progress'>";
echo "<input type='hidden' name='id' value='$id'>";
echo "<button type='submit' style='background:red;color:white;padding:10px 20px;cursor:pointer;'>🔴 EXECUTE PROGRESSION (Irreversible)</button>";
echo "</form>";

echo "</body></html>";
