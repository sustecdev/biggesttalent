<?php
/**
 * Full Competition Test Script
 * Simulates: Season → Nominations → Voting (National → Round 1 → Round 2 → Round 3) → Winner
 *
 * Run: php public/test_full_competition.php
 * Or:  http://localhost/btanew/public/test_full_competition.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CLI or web output
$isCli = (php_sapi_name() === 'cli');
function out($msg) {
    global $isCli;
    echo $isCli ? $msg . "\n" : nl2br(htmlspecialchars($msg) . "\n");
}

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
use App\Core\Config;
use App\Core\Database;
Config::load();

$db = Database::getInstance();
$conn = $db->getConnection();
$GLOBALS['mysqli'] = $conn;

if (!$conn || $conn->connect_error) {
    die("Database connection failed.\n");
}

require_once __DIR__ . '/../app/helpers/functions.php';

out("=== Full Competition Test ===\n");

// 1. Ensure schema
out("1. Ensuring schema...");
$tables = ['bt_seasons', 'bt_nominations', 'bt_votes', 'bt_categories'];
foreach ($tables as $t) {
    $r = $conn->query("SHOW TABLES LIKE '$t'");
    if (!$r || $r->num_rows === 0) {
        die("Table $t missing. Run migrations first.\n");
    }
}
// Add stage column to bt_votes if missing
$col = $conn->query("SHOW COLUMNS FROM bt_votes LIKE 'stage'");
if (!$col || $col->num_rows === 0) {
    $conn->query("ALTER TABLE bt_votes ADD COLUMN stage VARCHAR(50) NOT NULL DEFAULT 'national' AFTER nomination_id");
    out("   Added stage column to bt_votes.");
}
// Ensure bt_contest_stage_qualifiers exists
$conn->query("CREATE TABLE IF NOT EXISTS bt_contest_stage_qualifiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    season_id INT NOT NULL,
    nomination_id INT NOT NULL,
    stage VARCHAR(50) NOT NULL,
    qualified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_qualifier (season_id, nomination_id, stage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
out("   Schema OK.\n");

// 2. Create season
out("2. Creating season...");
$seasonModel = new App\Models\Season();
$seasonNum = (int) ($conn->query("SELECT COALESCE(MAX(season_number), 0) + 1 as n FROM bt_seasons")->fetch_assoc()['n'] ?? 1);
$res = $seasonModel->save([
    'season_number' => $seasonNum,
    'title' => "Test Season $seasonNum: Full Flow",
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+60 days')),
    'description' => 'Automated test run'
]);
if (!$res['success']) {
    die("Failed to create season: " . ($res['message'] ?? 'unknown') . "\n");
}
$seasonId = $conn->insert_id;
out("   Season created (ID: $seasonId).\n");

// 3. Activate season & set nominations open, voting closed
out("3. Activating season...");
$seasonModel->setActiveSeason($seasonId);
$conn->query("UPDATE bt_seasons SET is_nominations_open = 1, is_voting_open = 0 WHERE id = $seasonId");
out("   Nominations: OPEN, Voting: CLOSED.\n");

// 4. Add nominations
out("4. Adding 12 nominations...");
$names = ['Alpha Star', 'Beta Voice', 'Gamma Dance', 'Delta Beat', 'Epsilon Song', 'Zeta Moves',
          'Eta Rhythm', 'Theta Flow', 'Iota Shine', 'Kappa Wave', 'Lambda Groove', 'Mu Beat'];
$countries = ['Nigeria', 'Zambia', 'Kenya', 'Ghana', 'South Africa', 'Tanzania'];
$catRes = $conn->query("SELECT id FROM bt_categories WHERE is_active = 1 LIMIT 1");
$categoryId = ($catRes && $catRes->num_rows > 0) ? (int) $catRes->fetch_assoc()['id'] : null;
$uid = 999001;
$stmt = $conn->prepare("INSERT INTO bt_nominations (uid, season_id, title, aname, description, country, category_id, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, 'approved', NOW())");
$cid = $categoryId ?: 1;
for ($i = 0; $i < 12; $i++) {
    $title = $names[$i];
    $aname = $names[$i];
    $desc = "Test performer $i";
    $country = $countries[$i % count($countries)];
    $stmt->bind_param("iissssi", $uid, $seasonId, $title, $aname, $desc, $country, $cid);
    $stmt->execute();
}
out("   12 nominations added.\n");

// 5. Close nominations, open voting
out("5. Closing nominations, opening voting...");
$conn->query("UPDATE bt_seasons SET is_nominations_open = 0, is_voting_open = 1 WHERE id = $seasonId");
out("   Voting: OPEN.\n");

// 6. Add national-stage votes (uneven: top 8 get more)
$nomIds = [];
$r = $conn->query("SELECT id FROM bt_nominations WHERE season_id = $seasonId AND status='approved' ORDER BY id");
while ($row = $r->fetch_assoc()) $nomIds[] = $row['id'];

out("6. Adding national-stage votes...");
$voteStmt = $conn->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), 'national')");
$voteCounts = [50, 45, 42, 38, 35, 32, 30, 28, 15, 12, 8, 5]; // Descending
for ($i = 0; $i < 12; $i++) {
    for ($v = 0; $v < $voteCounts[$i]; $v++) {
        $voteUid = 1000 + $v;
        $ip = "192.168.1." . ($v % 254 + 1);
        $voteStmt->bind_param("iis", $nomIds[$i], $voteUid, $ip);
        $voteStmt->execute();
    }
}
out("   National votes added.\n");

// 7. Progress to Round 1 (top 8)
out("7. Progressing to Round 1 (top 8)...");
$voteModel = new App\Models\Vote();
$results = $voteModel->getStageResults($seasonId, 'national', 8);
$qualifiers = array_column($results, 'id');
$seasonModel->addQualifiers($seasonId, 'round_1', $qualifiers);
$seasonModel->updateStage($seasonId, 'round_1');
out("   Qualifiers: " . implode(', ', $qualifiers) . "\n");

// 8. Add Round 1 votes
out("8. Adding Round 1 votes...");
$r1Stmt = $conn->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), 'round_1')");
$r1Counts = [80, 70, 60, 55, 50, 45, 40, 38];
for ($i = 0; $i < min(8, count($qualifiers)); $i++) {
    for ($v = 0; $v < $r1Counts[$i]; $v++) {
        $voteUid = 2000 + $v;
        $ip = "10.0.1." . ($v % 254 + 1);
        $r1Stmt->bind_param("iis", $qualifiers[$i], $voteUid, $ip);
        $r1Stmt->execute();
    }
}
out("   Round 1 votes added.\n");

// 9. Progress to Round 2 (top 4)
out("9. Progressing to Round 2 (top 4)...");
$results = $voteModel->getStageResults($seasonId, 'round_1', 4);
$qualifiers = array_column($results, 'id');
$seasonModel->addQualifiers($seasonId, 'round_2', $qualifiers);
$seasonModel->updateStage($seasonId, 'round_2');
out("   Qualifiers: " . implode(', ', $qualifiers) . "\n");

// 10. Add Round 2 votes
out("10. Adding Round 2 votes...");
$r2Stmt = $conn->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), 'round_2')");
$r2Counts = [120, 100, 90, 85];
for ($i = 0; $i < min(4, count($qualifiers)); $i++) {
    for ($v = 0; $v < $r2Counts[$i]; $v++) {
        $voteUid = 3000 + $v;
        $ip = "172.16.1." . ($v % 254 + 1);
        $r2Stmt->bind_param("iis", $qualifiers[$i], $voteUid, $ip);
        $r2Stmt->execute();
    }
}
out("   Round 2 votes added.\n");

// 11. Progress to Round 3 (top 2 for finale)
out("11. Progressing to Round 3 (final 2)...");
$results = $voteModel->getStageResults($seasonId, 'round_2', 2);
$qualifiers = array_column($results, 'id');
$seasonModel->addQualifiers($seasonId, 'round_3', $qualifiers);
$seasonModel->updateStage($seasonId, 'round_3');
out("   Finalists: " . implode(', ', $qualifiers) . "\n");

// 12. Add Round 3 votes
out("12. Adding Round 3 votes...");
$r3Stmt = $conn->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date, stage) VALUES (?, ?, ?, NOW(), 'round_3')");
$r3Counts = [200, 180];
for ($i = 0; $i < min(2, count($qualifiers)); $i++) {
    for ($v = 0; $v < $r3Counts[$i]; $v++) {
        $voteUid = 4000 + $v;
        $ip = "192.168.2." . ($v % 254 + 1);
        $r3Stmt->bind_param("iis", $qualifiers[$i], $voteUid, $ip);
        $r3Stmt->execute();
    }
}
out("   Round 3 votes added.\n");

// 13. Set winner & close
out("13. Declaring winner and closing season...");
$winnerId = $qualifiers[0];
$seasonModel->setWinner($seasonId, $winnerId);
$seasonModel->updateStage($seasonId, 'closed');
$conn->query("UPDATE bt_seasons SET is_voting_open = 0 WHERE id = $seasonId");

$winnerRow = $conn->query("SELECT aname, country FROM bt_nominations WHERE id = $winnerId")->fetch_assoc();
out("   WINNER: " . ($winnerRow['aname'] ?? 'ID ' . $winnerId) . " (" . ($winnerRow['country'] ?? '') . ")\n");

out("\n=== Test complete ===");
out("Season ID: $seasonId | Winner nomination ID: $winnerId");
out("Run clean_database.php to reset, or view at Admin > Seasons.");
