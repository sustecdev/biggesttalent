<?php
/**
 * Make Current User an Admin
 * This script updates the current logged-in user's role to 'admin'
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #cd217d, #9a288d);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .success-box {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #155724;
        }

        .error-box {
            background: #f8d7da;
            border: 1px solid #dc3545;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #cd217d, #9a288d);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>👑 Make User Admin</h1>
            <p>Update User Role to Administrator</p>
        </div>
        <div class="content">
            <?php
            // Check if user is logged in
            if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
                echo '<div class="error-box">';
                echo '<strong>❌ Not Logged In</strong><br>';
                echo 'You must be logged in to use this tool.';
                echo '</div>';
                echo '<a href="' . URLROOT . '/auth" class="btn">Go to Login</a>';
                exit;
            }

            $uid = (int) $_SESSION['uid'];

            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();

                // Get current user info
                $stmt = $conn->prepare("SELECT uid, username, email, role FROM pi_account WHERE uid = ?");
                $stmt->bind_param("i", $uid);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("User not found in database");
                }

                $user = $result->fetch_assoc();

                echo '<div class="info-box">';
                echo '<strong>📋 Current User Information:</strong>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Value</th></tr>';
                echo '<tr><td>User ID (UID)</td><td>' . htmlspecialchars($user['uid']) . '</td></tr>';
                echo '<tr><td>Username</td><td>' . htmlspecialchars($user['username']) . '</td></tr>';
                echo '<tr><td>Email</td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
                echo '<tr><td>Current Role</td><td><strong>' . htmlspecialchars($user['role']) . '</strong></td></tr>';
                echo '</table>';
                echo '</div>';

                // Check if already admin
                if ($user['role'] === 'admin') {
                    echo '<div class="success-box">';
                    echo '<strong>✅ Already an Admin!</strong><br>';
                    echo 'This user already has administrator privileges.';
                    echo '</div>';
                } else {
                    // Check if form submitted
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
                        // Update role to admin
                        $updateStmt = $conn->prepare("UPDATE pi_account SET role = 'admin' WHERE uid = ?");
                        $updateStmt->bind_param("i", $uid);

                        if ($updateStmt->execute()) {
                            // Update session
                            $_SESSION['role'] = 'admin';

                            echo '<div class="success-box">';
                            echo '<strong>🎉 Success!</strong><br>';
                            echo 'User role has been updated to <strong>admin</strong>.<br>';
                            echo 'Session has been updated. You now have administrator privileges!';
                            echo '</div>';

                            echo '<a href="' . URLROOT . '" class="btn">Go to Dashboard</a>';
                        } else {
                            throw new Exception("Failed to update user role");
                        }
                    } else {
                        // Show confirmation form
                        echo '<form method="POST">';
                        echo '<div class="info-box" style="background: #fff3cd; border-color: #ffc107;">';
                        echo '<strong>⚠️ Confirm Action</strong><br>';
                        echo 'Are you sure you want to make this user an administrator?<br><br>';
                        echo '<strong>This will grant full access to:</strong>';
                        echo '<ul style="margin: 10px 0 10px 20px;">';
                        echo '<li>Admin dashboard</li>';
                        echo '<li>User management</li>';
                        echo '<li>Content moderation</li>';
                        echo '<li>System settings</li>';
                        echo '</ul>';
                        echo '</div>';
                        echo '<input type="hidden" name="confirm" value="1">';
                        echo '<button type="submit" class="btn">✅ Confirm - Make Admin</button>';
                        echo '</form>';
                    }
                }

            } catch (Exception $e) {
                echo '<div class="error-box">';
                echo '<strong>❌ Error:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>