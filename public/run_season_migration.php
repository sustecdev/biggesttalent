<?php
/**
 * Run Season Activation Migration
 * This script runs the 005_add_season_activation.sql migration
 */

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
    <title>Run Season Activation Migration</title>
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
            max-width: 800px;
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

        .info-box {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
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

        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Season Activation Migration</h1>
            <p>Add season activation and nomination linking</p>
        </div>
        <div class="content">
            <?php
            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();

                echo '<div class="info-box">';
                echo '<strong>📋 Migration Steps:</strong><br>';
                echo '1. Add <code>is_active</code> column to <code>bt_seasons</code><br>';
                echo '2. Add <code>season_id</code> column to <code>bt_nominations</code><br>';
                echo '3. Add indexes for performance<br>';
                echo '4. Set most recent season as active';
                echo '</div>';

                $errors = [];
                $success = [];

                // Step 1: Add is_active column to bt_seasons
                echo '<h3>Step 1: Adding is_active column to bt_seasons...</h3>';
                $sql1 = "ALTER TABLE bt_seasons 
                         ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Only one season can be active at a time'";
                if ($conn->query($sql1)) {
                    $success[] = "✅ Added is_active column to bt_seasons";
                } else {
                    if (strpos($conn->error, 'Duplicate column') !== false) {
                        $success[] = "ℹ️ is_active column already exists in bt_seasons";
                    } else {
                        $errors[] = "❌ Error adding is_active column: " . $conn->error;
                    }
                }

                // Step 2: Add index on is_active
                echo '<h3>Step 2: Adding index on is_active...</h3>';
                $sql2 = "ALTER TABLE bt_seasons ADD INDEX IF NOT EXISTS idx_is_active (is_active)";
                if ($conn->query($sql2)) {
                    $success[] = "✅ Added index on is_active";
                } else {
                    if (strpos($conn->error, 'Duplicate key') !== false) {
                        $success[] = "ℹ️ Index on is_active already exists";
                    } else {
                        $errors[] = "❌ Error adding index: " . $conn->error;
                    }
                }

                // Step 3: Add season_id column to bt_nominations
                echo '<h3>Step 3: Adding season_id column to bt_nominations...</h3>';
                $sql3 = "ALTER TABLE bt_nominations 
                         ADD COLUMN IF NOT EXISTS season_id INT NULL COMMENT 'Links nomination to a specific season'";
                if ($conn->query($sql3)) {
                    $success[] = "✅ Added season_id column to bt_nominations";
                } else {
                    if (strpos($conn->error, 'Duplicate column') !== false) {
                        $success[] = "ℹ️ season_id column already exists in bt_nominations";
                    } else {
                        $errors[] = "❌ Error adding season_id column: " . $conn->error;
                    }
                }

                // Step 4: Add index on season_id
                echo '<h3>Step 4: Adding index on season_id...</h3>';
                $sql4 = "ALTER TABLE bt_nominations ADD INDEX IF NOT EXISTS idx_season_id (season_id)";
                if ($conn->query($sql4)) {
                    $success[] = "✅ Added index on season_id";
                } else {
                    if (strpos($conn->error, 'Duplicate key') !== false) {
                        $success[] = "ℹ️ Index on season_id already exists";
                    } else {
                        $errors[] = "❌ Error adding index: " . $conn->error;
                    }
                }

                // Step 5: Set most recent season as active if none is active
                echo '<h3>Step 5: Setting most recent season as active...</h3>';
                $checkActive = $conn->query("SELECT COUNT(*) as count FROM bt_seasons WHERE is_active = 1");
                $row = $checkActive->fetch_assoc();

                if ($row['count'] == 0) {
                    $sql5 = "UPDATE bt_seasons 
                             SET is_active = 1 
                             WHERE id = (SELECT id FROM (SELECT id FROM bt_seasons ORDER BY start_date DESC LIMIT 1) AS temp)";
                    if ($conn->query($sql5)) {
                        $success[] = "✅ Set most recent season as active";
                    } else {
                        $errors[] = "❌ Error setting active season: " . $conn->error;
                    }
                } else {
                    $success[] = "ℹ️ An active season already exists";
                }

                // Display results
                if (!empty($success)) {
                    echo '<div class="success-box">';
                    echo '<strong>✅ Migration Results:</strong><br><br>';
                    foreach ($success as $msg) {
                        echo $msg . '<br>';
                    }
                    echo '</div>';
                }

                if (!empty($errors)) {
                    echo '<div class="error-box">';
                    echo '<strong>❌ Errors:</strong><br><br>';
                    foreach ($errors as $msg) {
                        echo $msg . '<br>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="success-box">';
                    echo '<strong>🎉 Migration Completed Successfully!</strong><br><br>';
                    echo 'You can now:<br>';
                    echo '• Go to the admin seasons page to activate/deactivate seasons<br>';
                    echo '• Submit nominations that will be linked to the active season<br>';
                    echo '• Only one season can be active at a time';
                    echo '</div>';

                    echo '<a href="' . URLROOT . '/admin/seasons" class="btn">Go to Seasons Management</a>';
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