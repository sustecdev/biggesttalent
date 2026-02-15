<?php
/**
 * Make user 1001290033 admin (by uid or username/pernum)
 * Run: http://localhost/btanew/dbaddons/make_1001290033_admin.php
 *  or: php make_1001290033_admin.php
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

$targetId = '1001290033';
$uid = (int) $targetId;
// SafeZone may use uid without leading "1" (e.g. 1290033)
$uidAlt = (int) ltrim(ltrim($targetId, '1'), '0');
$uidAlt = ($uidAlt > 0) ? $uidAlt : null;
$isCli = (php_sapi_name() === 'cli');

// Ensure role column exists
$check = $conn->query("SHOW COLUMNS FROM pi_account LIKE 'role'");
if (!$check || $check->num_rows == 0) {
    $conn->query("ALTER TABLE pi_account ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
}

// Find user by uid, alternate uid, or username (pernum) - SafeZone may use 1290033 vs 1001290033
$stmt = $conn->prepare("SELECT uid, username, role FROM pi_account WHERE uid = ? OR uid = ? OR username = ?");
$uidAltParam = $uidAlt ?? 0;
$stmt->bind_param("iis", $uid, $uidAltParam, $targetId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create account if not found (user may not have logged in via SafeZone yet)
    $usernameEsc = $conn->real_escape_string($targetId);
    $pwPlaceholder = bin2hex(random_bytes(16));
    $pwEsc = $conn->real_escape_string($pwPlaceholder);

    $insert = "INSERT INTO pi_account (uid, username, password, email, role) VALUES ($uid, '$usernameEsc', '$pwEsc', '', 'admin')";
    if (!$conn->query($insert)) {
        $msg = "User $targetId not found and failed to create: " . $conn->error;
        if ($isCli) die($msg . "\n");
        die("<p style='font-family:sans-serif;padding:20px;color:#c00'>$msg</p>");
    }

    $checkProfile = $conn->query("SELECT uid FROM pi_profile WHERE uid = $uid");
    if (!$checkProfile || $checkProfile->num_rows === 0) {
        $conn->query("INSERT INTO pi_profile (uid, fname, lname) VALUES ($uid, 'Admin', 'User')");
    }

    $msg = "Success: Created user $targetId as admin. They must log in via SafeZone first to set their real password; the admin role is already assigned.";
    if ($isCli) {
        echo $msg . "\n";
        exit(0);
    }
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Admin Created</title></head><body>";
    echo "<p style='font-family:sans-serif;padding:20px;color:#4caf50;font-weight:bold'>$msg</p>";
    echo "<p style='font-family:sans-serif;padding:0 20px;color:#666'>After logging in via SafeZone, they will have admin access.</p>";
    echo "</body></html>";
    exit(0);
}

$user = $result->fetch_assoc();
$actualUid = (int) $user['uid'];

// Update ALL matching accounts to admin (in case both uid formats exist)
$targetEsc = $conn->real_escape_string($targetId);
$where = "username = '$targetEsc'";
if ($uid > 0) $where .= " OR uid = $uid";
if ($uidAlt && $uidAlt > 0) $where .= " OR uid = $uidAlt";
$conn->query("UPDATE pi_account SET role = 'admin' WHERE $where");

if (($user['role'] ?? '') === 'admin') {
    $msg = "User $targetId (" . ($user['username'] ?? '') . ") is already an admin.";
    if ($isCli) {
        die($msg . "\n");
    }
    die("<p style='font-family:sans-serif;padding:20px;color:#4caf50'>$msg</p>");
}

// Update to admin (use actual uid from DB)
$update = $conn->prepare("UPDATE pi_account SET role = 'admin' WHERE uid = ?");
$update->bind_param("i", $actualUid);

if ($update->execute() && $conn->affected_rows > 0) {
    $msg = "Success: User $targetId (" . ($user['username'] ?? '') . ") has been set as admin. They should log out and log back in for changes to take effect.";
    if ($isCli) {
        echo $msg . "\n";
        exit(0);
    }
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Admin Granted</title></head><body>";
    echo "<p style='font-family:sans-serif;padding:20px;color:#4caf50;font-weight:bold'>$msg</p>";
    echo "<p style='font-family:sans-serif;padding:0 20px;color:#666'>User should log out and log back in for the admin role to appear in the session.</p>";
    echo "</body></html>";
} else {
    $msg = "Failed to update role. " . ($conn->error ?? 'Unknown error');
    if ($isCli) {
        die($msg . "\n");
    }
    die("<p style='font-family:sans-serif;padding:20px;color:#c00'>$msg</p>");
}
