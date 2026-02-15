<?php
/**
 * Add User as Admin
 * Usage: http://localhost/btanew/dbaddons/add_admin.php?uid=1001290033
 *        php add_admin.php 1001290033
 *        php add_admin.php 1001290033 --create  (create account if missing)
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

// Get uid/pernum from query string or CLI arg
$id = isset($_GET['uid']) ? $_GET['uid'] : (isset($_GET['pernum']) ? $_GET['pernum'] : ($argv[1] ?? null));
$createIfMissing = isset($_GET['create']) || (isset($argv[2]) && $argv[2] === '--create');
if (!$id) {
    die("Usage: add_admin.php?uid=1001290033  or  php add_admin.php 1001290033 [--create]\n");
}

$uid = is_numeric($id) ? (int) $id : 0;
$idStr = (string) $id;

// Ensure role column exists
$check = $conn->query("SHOW COLUMNS FROM pi_account LIKE 'role'");
if (!$check || $check->num_rows == 0) {
    $conn->query("ALTER TABLE pi_account ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
}

// Try by uid first, then by username (pernum)
$stmt = $conn->prepare("SELECT uid, username, role FROM pi_account WHERE uid = ? OR username = ?");
$stmt->bind_param("is", $uid, $idStr);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    if ($createIfMissing && $uid > 0) {
        // Create account with placeholder password; user must log in via SafeZone to set real password
        $pwPlaceholder = bin2hex(random_bytes(16));
        $usernameEsc = $conn->real_escape_string($idStr);
        $pwEsc = $conn->real_escape_string($pwPlaceholder);
        $insert = "INSERT INTO pi_account (uid, username, password, email, role) VALUES ($uid, '$usernameEsc', '$pwEsc', '', 'admin')";
        if ($conn->query($insert)) {
            $checkProfile = $conn->query("SELECT uid FROM pi_profile WHERE uid = $uid");
            if (!$checkProfile || $checkProfile->num_rows == 0) {
                $conn->query("INSERT INTO pi_profile (uid, fname, lname) VALUES ($uid, 'Admin', 'User')");
            }
            echo "Success: Created user $uid ($idStr) as admin. They must log in via SafeZone to authenticate.\n";
            exit(0);
        }
        die("Failed to create user: " . $conn->error . "\n");
    }
    die("User '$id' not found. Log in via SafeZone first, or run with --create to create the account.\n");
}

$user = $result->fetch_assoc();

if (($user['role'] ?? '') === 'admin') {
    die("User $uid (" . ($user['username'] ?? '') . ") is already an admin.\n");
}

// Update to admin
$update = $conn->prepare("UPDATE pi_account SET role = 'admin' WHERE uid = ?");
$update->bind_param("i", $uid);

if ($update->execute() && $conn->affected_rows > 0) {
    echo "Success: User $uid (" . ($user['username'] ?? '') . ") has been set as admin.\n";
} else {
    die("Failed to update role. " . ($conn->error ?? '') . "\n");
}
