<?php
/**
 * Open nominations for the active season.
 * Visit: http://localhost/btanew/public/open_nominations.php
 * Run once to enable the nomination form.
 */
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();
\App\Core\Config::load();
require_once __DIR__ . '/../app/helpers/functions.php';

$db = \App\Core\Database::getInstance();
$conn = $db->getConnection();
$GLOBALS['mysqli'] = $conn;

header('Content-Type: text/html; charset=utf-8');
echo "<pre>\n";

$active = getActiveSeasonSimple();
if (!$active) {
    echo "No active season found. Create and activate a season first.\n";
    echo "</pre>";
    exit;
}

$seasonId = (int) $active['id'];
$updated = $conn->query("UPDATE bt_seasons SET is_nominations_open = 1, is_voting_open = 0 WHERE id = $seasonId");

if ($updated && $conn->affected_rows >= 0) {
    echo "Nominations are now OPEN for: " . ($active['title'] ?? 'Season') . "\n";
    echo "Voting has been closed (only one phase at a time).\n";
    echo "\nRefresh the nomination page to see the form.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "</pre>";
