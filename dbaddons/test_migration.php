<?php
/**
 * Simple Migration Test
 * Test running migrations and show detailed errors
 */

// Bootstrap
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Migration Test</title>
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
    </style>
</head>

<body>
    <h1>🧪 Migration Test Runner</h1>
    <?php
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        echo "<p class='success'>✅ Database connected: " . DB_NAME . "</p>";

        // Test running first migration
        $file = '001_create_countries_table.sql';
        $filePath = __DIR__ . '/' . $file;

        echo "<h2>Testing: $file</h2>";

        if (!file_exists($filePath)) {
            echo "<p class='error'>❌ File not found: $filePath</p>";
        } else {
            echo "<p class='success'>✅ File found</p>";

            $sql = file_get_contents($filePath);
            echo "<p class='info'>📄 SQL Content:</p>";
            echo "<pre>" . htmlspecialchars($sql) . "</pre>";

            // Try to execute
            echo "<h3>Executing SQL...</h3>";

            $result = $conn->query($sql);

            if ($result === false) {
                echo "<p class='error'>❌ Error: " . htmlspecialchars($conn->error) . "</p>";
                echo "<p class='error'>Error Code: " . $conn->errno . "</p>";
            } else {
                echo "<p class='success'>✅ SQL executed successfully!</p>";

                // Check if table exists
                $check = $conn->query("SHOW TABLES LIKE 'countries'");
                if ($check && $check->num_rows > 0) {
                    echo "<p class='success'>✅ Table 'countries' created!</p>";
                } else {
                    echo "<p class='error'>❌ Table 'countries' not found after execution</p>";
                }
            }
        }

    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    ?>
</body>

</html>