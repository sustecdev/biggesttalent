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
    die("No active season found. Please create one first.");
}
$season = $seasonQuery->fetch_assoc();
$seasonId = $season['id'];
echo "Active Season: " . $season['title'] . " (ID: $seasonId)\n";

// Count current contestants
$countQuery = $mysqli->query("SELECT COUNT(*) as count FROM bt_nominations WHERE season_id = $seasonId AND status='approved'");
$currentCount = $countQuery->fetch_assoc()['count'];
echo "Current Contestants: $currentCount\n";

$target = 40;
$needed = $target - $currentCount;

if ($needed <= 0) {
    echo "Already have $currentCount contestants (>=$target). Skipping creation.\n";
} else {
    echo "Creating $needed dummy contestants...\n";
    $names = [
        'Liam',
        'Olivia',
        'Noah',
        'Emma',
        'Oliver',
        'Ava',
        'Elijah',
        'Charlotte',
        'William',
        'Sophia',
        'James',
        'Amelia',
        'Benjamin',
        'Isabella',
        'Lucas',
        'Mia',
        'Henry',
        'Evelyn',
        'Alexander',
        'Harper',
        'Mason',
        'Camila',
        'Michael',
        'Gianna',
        'Ethan',
        'Abigail',
        'Daniel',
        'Luna',
        'Jacob',
        'Ella',
        'Logan',
        'Elizabeth',
        'Jackson',
        'Sofia',
        'Levi',
        'Emily',
        'Sebastian',
        'Avery',
        'Mateo',
        'Mila',
        'David',
        'Aria',
        'Joseph',
        'Scarlett',
        'Samuel',
        'Victoria',
        'John',
        'Madison',
        'Gabriel',
        'Zoe'
    ];

    // Fetch valid country IDs
    $countryRes = $mysqli->query("SELECT id FROM countries LIMIT 20");
    $countries = [];
    while ($cRow = $countryRes->fetch_assoc()) {
        $countries[] = $cRow['id'];
    }
    if (empty($countries))
        $countries = [1];

    $stmt = $mysqli->prepare("INSERT INTO bt_nominations (season_id, title, aname, description, country, status, date) VALUES (?, ?, ?, ?, ?, 'approved', NOW())");

    for ($i = 0; $i < $needed; $i++) {
        $randName = $names[rand(0, count($names) - 1)] . " " . rand(100, 999);
        $title = "Performer " . $randName;
        $desc = "A talented performer from dummy data.";
        $country = $countries[array_rand($countries)];

        $stmt->bind_param("isssi", $seasonId, $title, $randName, $desc, $country);
        $stmt->execute();
    }
    echo "Added $needed contestants.\n";
}

// Now add random votes
echo "Adding random votes...\n";

// Get all contestants in season
$contestantsResult = $mysqli->query("SELECT id, aname FROM bt_nominations WHERE season_id = $seasonId AND status='approved'");
$contestants = [];
while ($row = $contestantsResult->fetch_assoc()) {
    $contestants[] = $row;
}

// Add votes
// We want a clear distribution. Some get many, some get few.
// Shuffle ids to randomize who gets lucky
shuffle($contestants);

$voteStmt = $mysqli->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), ?)");

// determine current stage
$stage = $season['current_stage'];
echo "Voting for stage: $stage\n";

foreach ($contestants as $index => $c) {
    if ($index < 5) {
        $voteCount = rand(50, 80); // Top tier
    } elseif ($index < 16) {
        $voteCount = rand(20, 40); // Mid tier (qualifiers)
    } elseif ($index < 32) {
        $voteCount = rand(5, 15); // Low tier (borderline)
    } else {
        $voteCount = rand(0, 3); // Bottom tier (eliminated)
    }

    echo "Vote seeding: " . $c['aname'] . " gets $voteCount votes.\n";

    for ($v = 0; $v < $voteCount; $v++) {
        $uid = rand(1000, 99999);
        $ip = rand(1, 255) . "." . rand(0, 255) . "." . rand(0, 255) . "." . rand(0, 255);
        $voteStmt->bind_param("iiss", $c['id'], $uid, $ip, $stage);
        $voteStmt->execute();
    }
}
echo "Seeding complete.\n";
