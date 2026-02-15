<?php
require_once '../legacy/config.php';
require_once '../legacy/functions.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Vote System Diagnostic</h2>";
echo "<pre>";

// 1. Check database connection
echo "1. Database Connection: ";
if (isset($GLOBALS['mysqli']) && $GLOBALS['mysqli']) {
    echo "✓ Connected\n";
} else {
    echo "✗ NOT CONNECTED\n";
    die("Cannot proceed without database connection");
}

// 2. Check if bt_votes table exists
echo "\n2. Checking bt_votes table: ";
$checkTable = "SHOW TABLES LIKE 'bt_votes'";
$result = $GLOBALS['mysqli']->query($checkTable);

if ($result && $result->num_rows > 0) {
    echo "✓ Table exists\n";
} else {
    echo "✗ Table does NOT exist\n";
    echo "   Creating table...\n";

    $createTable = "CREATE TABLE IF NOT EXISTS `bt_votes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nomination_id` int(11) NOT NULL,
      `uid` int(11) DEFAULT NULL,
      `ip_address` varchar(45) NOT NULL,
      `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `nomination_id` (`nomination_id`),
      KEY `uid` (`uid`),
      KEY `ip_address` (`ip_address`),
      KEY `date` (`date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($GLOBALS['mysqli']->query($createTable)) {
        echo "   ✓ Table created successfully\n";
    } else {
        echo "   ✗ Failed to create table: " . $GLOBALS['mysqli']->error . "\n";
    }
}

// 3. Check table structure
echo "\n3. Table Structure:\n";
$structureQuery = "DESCRIBE bt_votes";
$structureResult = $GLOBALS['mysqli']->query($structureQuery);

if ($structureResult) {
    while ($row = $structureResult->fetch_assoc()) {
        echo "   - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "   ✗ Error: " . $GLOBALS['mysqli']->error . "\n";
}

// 4. Check if user is logged in
echo "\n4. User Session: ";
session_start();
if (isset($_SESSION['uid'])) {
    echo "✓ Logged in (UID: " . $_SESSION['uid'] . ")\n";
} else {
    echo "✗ NOT logged in\n";
}

// 5. Test insert
echo "\n5. Test Vote Insert: ";
if (isset($_SESSION['uid'])) {
    $testNominationId = 1; // Using nomination ID 1 for test
    $testUid = $_SESSION['uid'];
    $testIp = '127.0.0.1';

    $stmt = $GLOBALS['mysqli']->prepare("INSERT INTO bt_votes (nomination_id, uid, ip_address, date) VALUES (?, ?, ?, NOW())");

    if (!$stmt) {
        echo "✗ Prepare failed: " . $GLOBALS['mysqli']->error . "\n";
    } else {
        $stmt->bind_param("iis", $testNominationId, $testUid, $testIp);

        if ($stmt->execute()) {
            echo "✓ Test vote inserted successfully (ID: " . $stmt->insert_id . ")\n";

            // Delete the test vote
            $deleteId = $stmt->insert_id;
            $stmt->close();
            $GLOBALS['mysqli']->query("DELETE FROM bt_votes WHERE id = $deleteId");
            echo "   (Test vote removed)\n";
        } else {
            echo "✗ Execute failed: " . $stmt->error . "\n";
        }
    }
} else {
    echo "⚠ Skipped (not logged in)\n";
}

echo "\n==========================================\n";
echo "Diagnostic Complete\n";
echo "==========================================\n";

echo "</pre>";
?>