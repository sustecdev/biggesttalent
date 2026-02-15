<?php
/**
 * Debug Session and Find User
 */

session_start();

// Bootstrap the application
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Debug User Session</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
        }

        .success {
            color: #4ec9b0;
        }

        .error {
            color: #f48771;
        }

        .info {
            color: #569cd6;
        }

        pre {
            background: #2d2d2d;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }

        table {
            background: #2d2d2d;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #444;
            text-align: left;
        }

        th {
            background: #3d3d3d;
            color: #569cd6;
        }
    </style>
</head>

<body>
    <h1>🔍 Debug User Session</h1>

    <h2>Session Data:</h2>
    <pre><?php print_r($_SESSION); ?></pre>

    <?php
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        echo "<h2>Looking for User...</h2>";

        // Try to find user by session UID
        if (isset($_SESSION['uid'])) {
            $uid = $_SESSION['uid'];
            echo "<p class='info'>Session UID: $uid</p>";

            $stmt = $conn->prepare("SELECT * FROM pi_account WHERE uid = ?");
            $stmt->bind_param("s", $uid); // Try string first
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p class='success'>✅ User found by UID!</p>";
                echo "<table>";
                echo "<tr><th>Field</th><th>Value</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    foreach ($row as $key => $value) {
                        echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p class='error'>❌ No user found with UID: $uid</p>";
            }
        } else {
            echo "<p class='error'>❌ No UID in session</p>";
        }

        // Try to find by username
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            echo "<h2>Searching by Username: $username</h2>";

            $stmt = $conn->prepare("SELECT * FROM pi_account WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p class='success'>✅ User found by username!</p>";
                echo "<table>";
                echo "<tr><th>Field</th><th>Value</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    foreach ($row as $key => $value) {
                        echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
                    }
                }
                echo "</table>";
            } else {
                echo "<p class='error'>❌ No user found with username: $username</p>";
            }
        }

        // Show all users in pi_account
        echo "<h2>All Users in pi_account:</h2>";
        $allUsers = $conn->query("SELECT uid, username, email, role FROM pi_account LIMIT 10");
        if ($allUsers && $allUsers->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>UID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
            while ($row = $allUsers->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['uid']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>No users found in pi_account table</p>";
        }

    } catch (Exception $e) {
        echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>

</html>