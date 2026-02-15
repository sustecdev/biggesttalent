<?php
/**
 * Database Cleanup Script
 * Clears contest/nomination/voting data. Keeps categories, countries, settings.
 * Run: http://localhost/btanew/dbaddons/clean_database.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

$db = Database::getInstance();
$conn = $db->getConnection();

if (!$conn || $conn->connect_error) {
    die('Database connection failed: ' . ($conn->error ?? 'Unknown'));
}

// Tables to clear (order matters for FKs - children first)
$tablesToClear = [
    'bt_votes',
    'bt_reports',
    'bt_contest_stage_qualifiers',
    'bt_video_uploads',
    'bt_leaderboard_cache',
    'bt_nominations',
    'bt_seasons',
    'bt_judges',
    'bt_contests',
    'bt_banned_ips',
];

$results = [];
$confirmed = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === '1');

if ($confirmed) {
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    foreach ($tablesToClear as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check && $check->num_rows > 0) {
            if ($conn->query("TRUNCATE TABLE `$table`")) {
                $results[$table] = 'Cleared';
            } else {
                $results[$table] = 'Error: ' . $conn->error;
            }
        } else {
            $results[$table] = 'Skipped (table not found)';
        }
    }

    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Cleanup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #1a1a1a; color: #e0e0e0; min-height: 100vh; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #cd217d; margin-bottom: 20px; }
        .warning { background: rgba(205,33,125,0.2); border: 1px solid #cd217d; padding: 20px; border-radius: 8px; margin-bottom: 24px; }
        .warning strong { color: #f48fb1; }
        ul { margin: 12px 0 0 20px; }
        .result { background: #252525; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .result-item { padding: 8px 0; border-bottom: 1px solid #333; font-family: monospace; display: flex; justify-content: space-between; }
        .result-item:last-child { border-bottom: none; }
        .ok { color: #4caf50; }
        .err { color: #f44336; }
        .skip { color: #888; }
        .btn { display: inline-block; padding: 12px 24px; background: #cd217d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #a51a64; }
        .btn-secondary { background: #444; }
        .btn-secondary:hover { background: #555; }
        form { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Cleanup</h1>

        <?php if ($confirmed): ?>
            <div class="result">
                <h3>Results</h3>
                <?php foreach ($results as $table => $status): ?>
                    <div class="result-item">
                        <span><?= htmlspecialchars($table) ?></span>
                        <span class="<?= strpos($status, 'Error') !== false ? 'err' : (strpos($status, 'Skipped') !== false ? 'skip' : 'ok') ?>"><?= htmlspecialchars($status) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <p style="color: #4caf50; margin-top: 16px;">Database cleaned. Kept: bt_categories, countries, states_provinces, bt_settings, pi_account, pi_profile</p>
        <?php else: ?>
            <div class="warning">
                <strong>This will permanently delete:</strong>
                <ul>
                    <li>All votes</li>
                    <li>All nominations</li>
                    <li>All seasons</li>
                    <li>All judges</li>
                    <li>All contests</li>
                    <li>Reports, banned IPs, video uploads, cache</li>
                </ul>
                <p style="margin-top: 12px;">Kept: categories, countries, states/provinces, settings, user accounts</p>
            </div>
            <form method="POST">
                <input type="hidden" name="confirm" value="1">
                <button type="submit" class="btn">Clean Database</button>
                <a href="../" class="btn btn-secondary" style="margin-left: 12px; text-decoration: none;">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
