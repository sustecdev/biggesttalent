<?php
/**
 * Database Table Checker
 * Quick script to verify if migration tables exist
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
    <title>Database Table Checker</title>
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
            max-width: 900px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status.exists {
            background: #d4edda;
            color: #155724;
        }

        .status.missing {
            background: #f8d7da;
            color: #721c24;
        }

        .info-box {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
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
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Database Table Checker</h1>
            <p>Verify Migration Tables</p>
        </div>
        <div class="content">
            <?php
            try {
                $db = Database::getInstance();
                $conn = $db->getConnection();

                if (!$conn) {
                    throw new Exception("Failed to connect to database");
                }

                echo '<div class="info-box">';
                echo '<strong>📊 Database:</strong> ' . DB_NAME . '<br>';
                echo '<strong>🖥️ Server:</strong> ' . DB_SERVER;
                echo '</div>';

                // Check for migration tables
                $requiredTables = ['countries', 'states_provinces'];

                echo '<h2 style="margin-top: 30px; margin-bottom: 15px;">Migration Tables Status</h2>';
                echo '<table>';
                echo '<thead><tr><th>Table Name</th><th>Status</th><th>Row Count</th></tr></thead>';
                echo '<tbody>';

                $allExist = true;
                foreach ($requiredTables as $table) {
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    $exists = $result && $result->num_rows > 0;

                    echo '<tr>';
                    echo '<td><strong>' . htmlspecialchars($table) . '</strong></td>';

                    if ($exists) {
                        echo '<td><span class="status exists">✅ EXISTS</span></td>';

                        // Get row count
                        $countResult = $conn->query("SELECT COUNT(*) as count FROM $table");
                        $count = $countResult ? $countResult->fetch_assoc()['count'] : 0;
                        echo '<td>' . number_format($count) . ' rows</td>';
                    } else {
                        echo '<td><span class="status missing">❌ MISSING</span></td>';
                        echo '<td>-</td>';
                        $allExist = false;
                    }

                    echo '</tr>';
                }

                echo '</tbody></table>';

                // Show all tables in database
                echo '<h2 style="margin-top: 30px; margin-bottom: 15px;">All Tables in Database</h2>';
                $allTablesResult = $conn->query("SHOW TABLES");

                if ($allTablesResult && $allTablesResult->num_rows > 0) {
                    echo '<table>';
                    echo '<thead><tr><th>#</th><th>Table Name</th><th>Row Count</th></tr></thead>';
                    echo '<tbody>';

                    $index = 1;
                    while ($row = $allTablesResult->fetch_array()) {
                        $tableName = $row[0];
                        $countResult = $conn->query("SELECT COUNT(*) as count FROM `$tableName`");
                        $count = $countResult ? $countResult->fetch_assoc()['count'] : 0;

                        echo '<tr>';
                        echo '<td>' . $index++ . '</td>';
                        echo '<td><strong>' . htmlspecialchars($tableName) . '</strong></td>';
                        echo '<td>' . number_format($count) . ' rows</td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';
                } else {
                    echo '<div class="error-box">⚠️ No tables found in database!</div>';
                }

                // Show recommendation
                if (!$allExist) {
                    echo '<div class="error-box">';
                    echo '<strong>⚠️ Migration tables are missing!</strong><br><br>';
                    echo 'Please run the migration script to create the required tables.';
                    echo '</div>';
                    echo '<a href="run_migrations.php" class="btn">▶️ Run Migrations Now</a>';
                } else {
                    echo '<div class="info-box" style="background: #d4edda; border-color: #28a745; color: #155724;">';
                    echo '<strong>✅ All migration tables exist!</strong><br>';
                    echo 'Your database is properly configured.';
                    echo '</div>';
                }

                echo '<a href="check_database.php" class="btn" style="background: #6c757d; margin-left: 10px;">🔄 Refresh</a>';

            } catch (Exception $e) {
                echo '<div class="error-box">';
                echo '<strong>❌ Database Error:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>

</html>