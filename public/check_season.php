<?php
/**
 * Quick debug: show active season and nominations status
 * Visit: http://localhost/btanew/public/check_season.php
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
echo "=== Active Season Check ===\n\n";

$active = getActiveSeasonSimple();
if ($active) {
    echo "Active season found:\n";
    foreach ($active as $k => $v) {
        echo "  $k = " . var_export($v, true) . "\n";
    }
    echo "\nis_nominations_open = " . var_export($active['is_nominations_open'] ?? 'NOT SET', true) . "\n";
    echo "Would show 'Nominations Closed'? " . (($active['is_nominations_open'] ?? 1) === 0 || ($active['is_nominations_open'] ?? 1) === '0' ? 'YES' : 'NO') . "\n";
} else {
    echo "No active season found.\n";
}
echo "</pre>";
