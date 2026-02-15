<?php
require_once '../app/core/Config.php';
Config::load();
require_once '../app/core/Database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$pernum = '1001294626';
$sql = "SELECT * FROM pi_account WHERE username = '$pernum'";
$res = $conn->query($sql);

echo "<pre>";
if ($res && $res->num_rows > 0) {
    print_r($res->fetch_assoc());
} else {
    echo "User not found";
}
echo "</pre>";
