<?php
/**
 * Add User as Admin (with or without them signing in first)
 *
 * By username (pernum) - pre-add before first login:
 *   http://localhost/btanew/dbaddons/add_admin.php?pernum=THEIR_SAFEZONE_USERNAME
 *   php add_admin.php THEIR_SAFEZONE_USERNAME
 *
 * By uid - for users who have already logged in:
 *   http://localhost/btanew/dbaddons/add_admin.php?uid=1001290033
 *   php add_admin.php 1001290033
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();
require_once __DIR__ . '/../app/helpers/functions.php';

$db = Database::getInstance();
$conn = $db->getConnection();
$GLOBALS['mysqli'] = $conn;

if (!$conn || $conn->connect_error) {
    die('Database connection failed: ' . ($conn->error ?? 'Unknown'));
}

$id = isset($_GET['pernum']) ? $_GET['pernum'] : (isset($_GET['uid']) ? $_GET['uid'] : ($argv[1] ?? null));
if (!$id) {
    die("Usage: add_admin.php?pernum=SAFEZONE_USERNAME  (add before they sign in)\n" .
        "      add_admin.php?uid=1001290033  (user who already logged in)\n");
}

// If pernum/username (non-numeric), use addAdminByUsername - works before they sign in
if (!is_numeric($id) && function_exists('addAdminByUsername')) {
    if (addAdminByUsername(trim($id))) {
        echo "Success: Pre-added '$id' as admin. They will have admin access on their first SafeZone login.\n";
    } else {
        die("Failed to add admin for '$id'.\n");
    }
    exit(0);
}

// By uid - for existing users
$uid = (int) $id;

// Ensure role column exists
$check = $conn->query("SHOW COLUMNS FROM pi_account LIKE 'role'");
if (!$check || $check->num_rows == 0) {
    $conn->query("ALTER TABLE pi_account ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
}

$stmt = $conn->prepare("SELECT uid, username, role FROM pi_account WHERE uid = ? OR username = ?");
$stmt->bind_param("is", $uid, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User '$id' not found. Use pernum=USERNAME to pre-add before their first login.\n");
}

$user = $result->fetch_assoc();

if (in_array($user['role'] ?? '', ['admin', 'super_admin'], true)) {
    die("User " . ($user['username'] ?? $uid) . " is already an admin.\n");
}

$update = $conn->prepare("UPDATE pi_account SET role = 'admin' WHERE uid = ?");
$update->bind_param("i", $user['uid']);

if ($update->execute() && $conn->affected_rows > 0) {
    echo "Success: User " . ($user['username'] ?? $uid) . " has been set as admin.\n";
} else {
    die("Failed to update role. " . ($conn->error ?? '') . "\n");
}
