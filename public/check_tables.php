<?php
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();
use App\Core\Config;
use App\Core\Database;
Config::load();

$db = Database::getInstance();
$conn = $db->getConnection();

echo "<h2>bt_seasons structure:</h2><pre>";
$result = $conn->query("DESCRIBE bt_seasons");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

echo "<h2>bt_nominations structure:</h2><pre>";
$result = $conn->query("DESCRIBE bt_nominations");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";
?>