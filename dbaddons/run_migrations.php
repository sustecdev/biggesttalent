<?php
/**
 * Database Migration Runner
 * 
 * This script runs all SQL migration files in the dbaddons directory
 * Run this file by navigating to: http://localhost/SustecAfrica/btagrace/dbaddons/run_migrations.php
 */

// Bootstrap the application properly
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

// Load configuration (this defines DB_SERVER, DB_USERNAME, etc.)
Config::load();

// Set execution time limit for large migrations
set_time_limit(300); // 5 minutes

// HTML header
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration Runner</title>
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

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .migration-item {
            background: #f8f9fa;
            border-left: 4px solid #dee2e6;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .migration-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }

        .migration-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }

        .migration-item.running {
            border-left-color: #ffc107;
            background: #fff3cd;
        }

        .migration-title {
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .migration-message {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .status-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
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

        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .warning strong {
            color: #856404;
        }

        .summary {
            background: #e7f3ff;
            border: 1px solid #2196F3;
            padding: 20px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .summary h3 {
            color: #1976D2;
            margin-bottom: 10px;
        }

        .stat {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .stat:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Migration Runner</h1>
            <p>Biggest Talent Africa - Database Setup</p>
        </div>
        <div class="content">
            <?php

            // Check if running via POST (confirmation)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {

                // Get database connection
                try {
                    $db = Database::getInstance();
                    $conn = $db->getConnection();

                    if (!$conn) {
                        throw new Exception("Failed to connect to database");
                    }

                    echo '<div class="warning"><strong>⚠️ Migration Started</strong><br>Running database migrations...</div>';

                    // Migration files in order
                    $migrations = [
                        '001_create_countries_table.sql',
                        '002_create_states_provinces_table.sql',
                        '003_populate_countries.sql',
                        '004_populate_states_provinces.sql'
                    ];

                    $successCount = 0;
                    $errorCount = 0;
                    $totalTime = 0;

                    foreach ($migrations as $file) {
                        $startTime = microtime(true);
                        $filePath = __DIR__ . '/' . $file;

                        echo '<div class="migration-item running">';
                        echo '<div class="migration-title">';
                        echo '<span class="status-icon">⏳</span>';
                        echo htmlspecialchars($file);
                        echo '</div>';

                        if (!file_exists($filePath)) {
                            echo '<div class="migration-message" style="color: #dc3545;">❌ File not found</div>';
                            echo '</div>';
                            $errorCount++;
                            continue;
                        }

                        // Read SQL file
                        $sql = file_get_contents($filePath);

                        if ($sql === false) {
                            echo '<div class="migration-message" style="color: #dc3545;">❌ Failed to read file</div>';
                            echo '</div>';
                            $errorCount++;
                            continue;
                        }

                        // Execute SQL
                        try {
                            // Split by semicolons but handle multi-line statements
                            $statements = array_filter(
                                array_map('trim', explode(';', $sql)),
                                function ($stmt) {
                                    return !empty($stmt) && !preg_match('/^--/', $stmt);
                                }
                            );

                            $stmtCount = 0;
                            foreach ($statements as $statement) {
                                if (!empty($statement)) {
                                    $result = $conn->query($statement);
                                    if ($result === false) {
                                        throw new Exception($conn->error);
                                    }
                                    $stmtCount++;
                                }
                            }

                            $endTime = microtime(true);
                            $duration = round(($endTime - $startTime) * 1000, 2);
                            $totalTime += $duration;

                            echo '</div>';
                            echo '<div class="migration-item success">';
                            echo '<div class="migration-title">';
                            echo '<span class="status-icon">✅</span>';
                            echo htmlspecialchars($file);
                            echo '</div>';
                            echo '<div class="migration-message" style="color: #28a745;">';
                            echo "Successfully executed {$stmtCount} statement(s) in {$duration}ms";
                            echo '</div>';
                            echo '</div>';

                            $successCount++;

                        } catch (Exception $e) {
                            $endTime = microtime(true);
                            $duration = round(($endTime - $startTime) * 1000, 2);
                            $totalTime += $duration;

                            echo '</div>';
                            echo '<div class="migration-item error">';
                            echo '<div class="migration-title">';
                            echo '<span class="status-icon">❌</span>';
                            echo htmlspecialchars($file);
                            echo '</div>';
                            echo '<div class="migration-message" style="color: #dc3545;">';
                            echo 'Error: ' . htmlspecialchars($e->getMessage());
                            echo '</div>';
                            echo '</div>';

                            $errorCount++;
                        }
                    }

                    // Summary
                    echo '<div class="summary">';
                    echo '<h3>📊 Migration Summary</h3>';
                    echo '<div class="stat"><span>Total Migrations:</span><strong>' . count($migrations) . '</strong></div>';
                    echo '<div class="stat"><span>Successful:</span><strong style="color: #28a745;">' . $successCount . '</strong></div>';
                    echo '<div class="stat"><span>Failed:</span><strong style="color: #dc3545;">' . $errorCount . '</strong></div>';
                    echo '<div class="stat"><span>Total Time:</span><strong>' . round($totalTime, 2) . 'ms</strong></div>';
                    echo '</div>';

                    if ($errorCount === 0) {
                        echo '<div style="margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #28a745; border-radius: 6px; color: #155724;">';
                        echo '<strong>🎉 All migrations completed successfully!</strong><br>';
                        echo 'Your database is now ready to use.';
                        echo '</div>';
                    }

                    echo '<a href="run_migrations.php" class="btn">Run Again</a>';

                } catch (Exception $e) {
                    echo '<div class="migration-item error">';
                    echo '<div class="migration-title">❌ Database Connection Error</div>';
                    echo '<div class="migration-message">' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '</div>';
                }

            } else {
                // Show confirmation form
                ?>
                <div class="warning">
                    <strong>⚠️ Important Notice</strong><br>
                    This will run database migrations that will create new tables and populate them with data.
                    <br><br>
                    <strong>Before proceeding:</strong>
                    <ul style="margin-left: 20px; margin-top: 10px;">
                        <li>Ensure you have a database backup</li>
                        <li>Verify your database connection settings in .env</li>
                        <li>These scripts are safe and won't delete existing data</li>
                    </ul>
                </div>

                <h3 style="margin-bottom: 15px;">📋 Migrations to Run:</h3>
                <div class="migration-item">
                    <div class="migration-title">1️⃣ 001_create_countries_table.sql</div>
                    <div class="migration-message">Creates the countries table</div>
                </div>
                <div class="migration-item">
                    <div class="migration-title">2️⃣ 002_create_states_provinces_table.sql</div>
                    <div class="migration-message">Creates the states/provinces table</div>
                </div>
                <div class="migration-item">
                    <div class="migration-title">3️⃣ 003_populate_countries.sql</div>
                    <div class="migration-message">Populates 195+ countries</div>
                </div>
                <div class="migration-item">
                    <div class="migration-title">4️⃣ 004_populate_states_provinces.sql</div>
                    <div class="migration-message">Populates states/provinces for major countries</div>
                </div>

                <form method="POST" style="margin-top: 30px;">
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" class="btn">▶️ Run Migrations Now</button>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</body>

</html>