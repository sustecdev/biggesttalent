<?php
require_once 'app/core/Config.php';
App\Core\Config::load();
$m = new mysqli(getenv('DB_SERVER'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
$res = $m->query('SELECT * FROM bt_seasons');
if ($res) {
    echo "Seasons in database:\n";
    while($row = $res->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Number: " . $row['season_number'] . " | Title: " . $row['title'] . "\n";
    }
} else {
    echo "Query failed: " . $m->error . "\n";
}
?>
