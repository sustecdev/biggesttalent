<?php
/**
 * Debug Admin Access
 * Visit: http://localhost/btanew/public/debug_admin.php
 * Shows your session uid/role and DB role. Use to troubleshoot admin access.
 */
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();
require_once __DIR__ . '/../app/helpers/functions.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$GLOBALS['mysqli'] = $conn;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><title>Admin Debug</title>
<style>
body{font-family:sans-serif;padding:20px;max-width:600px;margin:0 auto;background:#f5f5f5;}
.box{background:#fff;padding:20px;margin:10px 0;border-radius:8px;border:1px solid #ddd;}
.ok{color:green;} .err{color:red;} .warn{color:orange;}
h2{margin-top:0;} code{background:#eee;padding:2px 6px;border-radius:4px;}
</style>
</head>
<body>
<h1>Admin Access Debug</h1>

<?php if (!isset($_SESSION['uid']) || !$_SESSION['uid']): ?>
<div class="box err">
    <strong>Not logged in.</strong> Log in via SafeZone first, then revisit this page.
</div>
<?php exit; endif;

$uid = (int) $_SESSION['uid'];
$roleFromDb = function_exists('getUserRoleByUid') ? getUserRoleByUid($uid, $_SESSION['pernum'] ?? null) : '?';
?>
<div class="box">
    <h2>Your Session</h2>
    <p><strong>UID:</strong> <code><?= htmlspecialchars($_SESSION['uid']) ?></code></p>
    <p><strong>Role in session:</strong> <code><?= htmlspecialchars($_SESSION['role'] ?? 'NOT SET') ?></code></p>
    <p><strong>Role in DB (pi_account):</strong> <code><?= htmlspecialchars($roleFromDb) ?></code></p>
    <?php if (($roleFromDb ?? '') === 'admin' && ($_SESSION['role'] ?? '') !== 'admin'): ?>
    <p class="warn"><strong>Mismatch:</strong> DB says admin but session does not. <a href="?fix=1">Click to fix session</a></p>
    <?php elseif (($roleFromDb ?? '') !== 'admin'): ?>
    <p class="err"><strong>Not admin in DB.</strong> Run: <code>php dbaddons/add_admin.php <?= $uid ?> --create</code></p>
    <?php elseif ($_SESSION['role'] === 'admin'): ?>
    <p class="ok"><strong>OK:</strong> You should see the Admin Dashboard link. <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php">Go to home</a></p>
    <?php endif; ?>
</div>
<?php
if (isset($_GET['fix']) && $_GET['fix'] === '1') {
    $_SESSION['role'] = $roleFromDb;
    echo '<div class="box ok">Session updated. Refresh the page or go to <a href="' . (defined('URLROOT') ? URLROOT : '') . '/index.php">home</a>.</div>';
}
?>
</body>
</html>
