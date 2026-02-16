<?php
/**
 * Promote an existing admin to Super Admin.
 * Only Super Admins can add/remove admins. Run this once to create your first Super Admin.
 *
 * Usage (browser): http://localhost/btanew/dbaddons/make_super_admin.php?uid=1001290033
 * Usage (CLI):     php make_super_admin.php 1001290033
 *
 * Replace 1001290033 with the uid or pernum (username) of an existing admin.
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

$targetId = $_GET['uid'] ?? $_GET['pernum'] ?? ($argv[1] ?? null);
if (empty($targetId)) {
    $msg = 'Usage: make_super_admin.php?uid=USER_UID  or  php make_super_admin.php USER_UID';
    if (php_sapi_name() === 'cli') {
        die($msg . "\n");
    }
    die("<p style='font-family:sans-serif;padding:20px;color:#333'>$msg</p>");
}

$uid = is_numeric($targetId) ? (int) $targetId : null;
$pernum = is_numeric($targetId) ? (string) $targetId : trim($targetId);
$isCli = (php_sapi_name() === 'cli');

// Ensure role column exists
$check = $conn->query("SHOW COLUMNS FROM pi_account LIKE 'role'");
if (!$check || $check->num_rows == 0) {
    $conn->query("ALTER TABLE pi_account ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
}

// Find user by uid or username
$user = null;
if ($uid && $uid > 0) {
    $stmt = $conn->prepare("SELECT uid, username, role FROM pi_account WHERE uid = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
}
if (!$user && $pernum) {
    $pernumEsc = $conn->real_escape_string($pernum);
    $res = $conn->query("SELECT uid, username, role FROM pi_account WHERE username = '$pernumEsc' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
    }
}

if (!$user) {
    $msg = "User not found: $targetId. Ensure the user exists in pi_account (login at least once).";
    if ($isCli) die($msg . "\n");
    die("<p style='font-family:sans-serif;padding:20px;color:#c00'>$msg</p>");
}

$actualUid = (int) $user['uid'];
$update = $conn->prepare("UPDATE pi_account SET role = 'super_admin' WHERE uid = ?");
$update->bind_param("i", $actualUid);

if ($update->execute() && $conn->affected_rows > 0) {
    $msg = "Success: User {$user['username']} (uid: $actualUid) is now Super Admin. They can add more admins from Admin → Users & Roles.";
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo "<p style='font-family:sans-serif;padding:20px;color:#0a0'>$msg</p>";
    }
} else {
    $msg = "User is already Super Admin or update failed.";
    if ($isCli) die($msg . "\n");
    die("<p style='font-family:sans-serif;padding:20px;color:#c00'>$msg</p>");
}
