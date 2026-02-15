<?php
session_start();
require_once '../legacy/config.php';

echo "<h2>Session Debug Info</h2>";

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
echo "Logged in: " . (isset($_SESSION['uid']) ? 'YES' : 'NO') . "\n";
if (isset($_SESSION['uid'])) {
    echo "User ID: " . $_SESSION['uid'] . "\n";
    echo "Username: " . ($_SESSION['username'] ?? 'NOT SET') . "\n";
    echo "Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
    echo "Email: " . ($_SESSION['email'] ?? 'NOT SET') . "\n";
}
echo "</pre>";

echo "<h3>Database Check:</h3>";
echo "<pre>";

// Check for user 1290033
$result1 = $GLOBALS['mysqli']->query("SELECT uid, username, email, role FROM pi_account WHERE uid = 1290033");
if ($result1 && $row = $result1->fetch_assoc()) {
    echo "User 1290033:\n";
    echo "  Username: {$row['username']}\n";
    echo "  Email: {$row['email']}\n";
    echo "  Role: " . ($row['role'] ?? 'NULL') . "\n\n";
}

// Check for user 100290033
$result2 = $GLOBALS['mysqli']->query("SELECT uid, username, email, role FROM pi_account WHERE uid = 100290033");
if ($result2 && $row = $result2->fetch_assoc()) {
    echo "User 100290033:\n";
    echo "  Username: {$row['username']}\n";
    echo "  Email: {$row['email']}\n";
    echo "  Role: " . ($row['role'] ?? 'NULL') . "\n\n";
} else {
    echo "User 100290033: NOT FOUND\n\n";
}

// If logged in, check current user
if (isset($_SESSION['uid'])) {
    $currentUid = (int)$_SESSION['uid'];
    $result3 = $GLOBALS['mysqli']->query("SELECT uid, username, email, role FROM pi_account WHERE uid = $currentUid");
    if ($result3 && $row = $result3->fetch_assoc()) {
        echo "Current logged in user ($currentUid):\n";
        echo "  Username: {$row['username']}\n";
        echo "  Email: {$row['email']}\n";
        echo "  Role in DB: " . ($row['role'] ?? 'NULL') . "\n";
        echo "  Role in Session: " . ($_SESSION['role'] ?? 'NOT SET') . "\n\n";
        
        if (($row['role'] ?? '') !== ($_SESSION['role'] ?? '')) {
            echo "⚠️ MISMATCH: Database role and session role are different!\n";
        }
    }
}

echo "</pre>";

echo "<h3>Action:</h3>";
if (isset($_SESSION['uid'])) {
    $uid = (int)$_SESSION['uid'];
    echo "<form method='POST'>";
    echo "<button type='submit' name='update_session'>Update My Session Role from Database</button>";
    echo "</form>";
    
    if (isset($_POST['update_session'])) {
        $result = $GLOBALS['mysqli']->query("SELECT role FROM pi_account WHERE uid = $uid");
        if ($result && $row = $result->fetch_assoc()) {
            $_SESSION['role'] = $row['role'];
            echo "<p style='color: green;'>✓ Session updated! Role is now: " . $_SESSION['role'] . "</p>";
            echo "<p><a href='../index.php?url=profile'>Go to Dashboard</a></p>";
        }
    }
} else {
    echo "<p>Please log in first.</p>";
}
