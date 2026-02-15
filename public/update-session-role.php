<?php
session_start();
require_once 'legacy/config.php';

echo "=== Updating Session Role ===\n\n";

if (!isset($_SESSION['uid'])) {
    echo "✗ No user logged in. Please log in first.\n";
    exit;
}

$userId = (int)$_SESSION['uid'];

// Get user's role from database
$result = $GLOBALS['mysqli']->query("SELECT role FROM pi_account WHERE uid = $userId");

if ($result && $row = $result->fetch_assoc()) {
    $_SESSION['role'] = $row['role'];
    echo "✓ Session updated successfully!\n\n";
    echo "User ID: $userId\n";
    echo "New Role: " . $_SESSION['role'] . "\n\n";
    
    if ($_SESSION['role'] === 'admin') {
        echo "✓ You now have admin access!\n";
        echo "Refresh your dashboard to see the Admin Panel link.\n";
    } else {
        echo "Note: Your role is '{$_SESSION['role']}', not 'admin'.\n";
    }
} else {
    echo "✗ Could not find user in database.\n";
}
