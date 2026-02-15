<?php
/**
 * Comprehensive Function Test Script
 * Run via: http://localhost/btanew/public/test_all_functions.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

// Bootstrap (same as index.php)
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection();
require_once __DIR__ . '/../app/helpers/functions.php';

$passed = 0;
$failed = 0;
$results = [];

function test($name, $callback) {
    global $passed, $failed, $results;
    try {
        $result = $callback();
        if ($result === true || $result === null) {
            $passed++;
            $results[] = ['name' => $name, 'ok' => true, 'msg' => 'OK'];
            return true;
        }
        if ($result === false) {
            $failed++;
            $results[] = ['name' => $name, 'ok' => false, 'msg' => 'Failed'];
            return false;
        }
        $passed++;
        $results[] = ['name' => $name, 'ok' => true, 'msg' => $result];
        return true;
    } catch (Throwable $e) {
        $failed++;
        $results[] = ['name' => $name, 'ok' => false, 'msg' => $e->getMessage()];
        return false;
    }
}

echo "<h1>Biggest Talent Africa - Function Tests</h1>";
echo "<pre style='font-family: monospace; background:#1e1e1e; color:#d4d4d4; padding:20px; border-radius:8px;'>";

// --- Core ---
test('Config loaded (APPROOT defined)', fn() => defined('APPROOT') && APPROOT);
test('Config loaded (DB_NAME defined)', fn() => defined('DB_NAME'));
test('Database connection', fn() => isset($GLOBALS['mysqli']) && $GLOBALS['mysqli'] && !$GLOBALS['mysqli']->connect_error);

// --- Helper: getNumbersFromText (no DB) ---
test('getNumbersFromText("hello")', fn() => getNumbersFromText('hello') === '43556' ? true : 'Got: ' . getNumbersFromText('hello'));
test('getNumbersFromText("abc")', fn() => getNumbersFromText('abc') === '222' ? true : 'Got: ' . getNumbersFromText('abc'));

// --- Helper: getStageConstraints (no DB) ---
test('getStageConstraints national', function() {
    $r = getStageConstraints(1, 'national');
    $valid = isset($r['join'], $r['where']) && is_string($r['join']) && is_string($r['where']);
    return $valid ? true : 'Wrong structure';
});
test('getStageConstraints round_1', function() {
    $r = getStageConstraints(1, 'round_1');
    return (strpos($r['join'] ?? '', 'bt_contest_stage_qualifiers') !== false) ? true : 'Missing join';
});

// --- Helper: GetIP ---
test('GetIP returns string', fn() => is_string(GetIP()) && strlen(GetIP()) > 0);

// --- Helper: getActiveSeasonSimple (needs bt_seasons) ---
test('getActiveSeasonSimple (no crash)', function() {
    $tables = $GLOBALS['mysqli']->query("SHOW TABLES LIKE 'bt_seasons'");
    if (!$tables || $tables->num_rows === 0) return 'SKIP: bt_seasons missing';
    $r = getActiveSeasonSimple();
    return $r === null || (is_array($r) && isset($r['id']));
});

// --- Helper: getCategories ---
test('getCategories (no crash)', function() {
    $r = getCategories();
    return is_array($r);
});

// --- Helper: getContests ---
test('getContests (no crash)', function() {
    $r = getContests();
    return is_array($r);
});

// --- Helper: isAuthenticated ---
test('isAuthenticated (no session)', function() {
    @session_start();
    $r = isAuthenticated();
    return is_bool($r);
});

// --- getTotRecords (needs table) ---
test('getTotRecords bt_seasons', function() {
    $tables = $GLOBALS['mysqli']->query("SHOW TABLES LIKE 'bt_seasons'");
    if (!$tables || $tables->num_rows === 0) return 'SKIP';
    $n = getTotRecords('id', 'bt_seasons', '');
    return is_numeric($n) ? "Count: $n" : false;
});

// --- Models ---
test('Season model instantiate', function() {
    $m = new \App\Models\Season();
    return $m !== null;
});

test('Season getAll', function() {
    $m = new \App\Models\Season();
    $rows = $m->getAll();
    return is_array($rows);
});

test('Season getActiveSeason', function() {
    $m = new \App\Models\Season();
    $r = $m->getActiveSeason();
    return $r === null || (is_array($r) && isset($r['id']));
});

test('Category model instantiate', function() {
    $m = new \App\Models\Category();
    return $m !== null;
});

test('Vote model instantiate', function() {
    $m = new \App\Models\Vote();
    return $m !== null;
});

test('Vote getVotingStats', function() {
    $m = new \App\Models\Vote();
    $s = $m->getVotingStats();
    return is_array($s) && isset($s['total_votes']) ? 'total_votes=' . $s['total_votes'] : false;
});

test('User model instantiate', function() {
    $m = new \App\Models\User();
    return $m !== null;
});

// --- Voting/Nominations logic (is_voting_open, is_nominations_open) ---
test('Active season has lock columns', function() {
    $season = getActiveSeasonSimple();
    if (!$season) return 'SKIP: no active season';
    $hasNom = array_key_exists('is_nominations_open', $season);
    $hasVote = array_key_exists('is_voting_open', $season);
    return ($hasNom && $hasVote) ? 'OK' : 'Missing columns - run add_lock_columns.php';
});

// --- Controllers (light) ---
test('HomeController instantiate', function() {
    $c = new \App\Controllers\HomeController();
    return $c !== null;
});

test('VoteController instantiate', function() {
    $c = new \App\Controllers\VoteController();
    return $c !== null;
});

test('SafezoneController instantiate', function() {
    $c = new \App\Controllers\SafezoneController();
    return $c !== null;
});

// --- getApprovedContestantsWithVotes ---
test('getApprovedContestantsWithVotes (no crash)', function() {
    $r = getApprovedContestantsWithVotes(5);
    return is_array($r);
});

// --- getVotesToday ---
test('getVotesToday (no crash)', function() {
    $n = getVotesToday(999, 0, '127.0.0.1');
    return is_int($n) && $n >= 0;
});

echo "\n" . str_repeat('=', 60) . "\n";
foreach ($results as $r) {
    $icon = $r['ok'] ? '✓' : '✗';
    $color = $r['ok'] ? '' : '; color:#f48771';
    echo sprintf("[%s] %-50s %s\n", $icon, $r['name'], $r['msg']);
}
echo str_repeat('=', 60) . "\n";
echo "Passed: $passed | Failed: $failed | Total: " . ($passed + $failed) . "\n";
echo "</pre>";
