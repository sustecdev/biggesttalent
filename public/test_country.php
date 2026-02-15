<?php
// Quick test to see countries and their IDs
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

Config::load();

$db = Database::getInstance();
$conn = $db->getConnection();

// Get Zambia's ID
$result = $conn->query("SELECT id, name, iso_code FROM countries WHERE name LIKE '%Zambia%' OR iso_code = 'ZM'");

echo "<h2>Zambia Country Info:</h2>";
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p>ID: {$row['id']}, Name: {$row['name']}, ISO: {$row['iso_code']}</p>";
    }
} else {
    echo "<p>Zambia not found!</p>";
}

// Get states for Zambia (assuming ID from above)
echo "<h2>Testing AJAX endpoint directly:</h2>";
$zambiaResult = $conn->query("SELECT id FROM countries WHERE iso_code = 'ZM'");
if ($zambiaResult && $row = $zambiaResult->fetch_assoc()) {
    $zambiaId = $row['id'];
    echo "<p>Zambia ID: $zambiaId</p>";

    $statesResult = $conn->query("SELECT * FROM states_provinces WHERE country_id = $zambiaId");
    echo "<h3>States/Provinces for Zambia:</h3>";
    if ($statesResult && $statesResult->num_rows > 0) {
        echo "<ul>";
        while ($state = $statesResult->fetch_assoc()) {
            echo "<li>{$state['name']} ({$state['state_code']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No states found for Zambia!</p>";
    }
}

// Test the AJAX endpoint
echo "<h2>Test AJAX Endpoint:</h2>";
echo "<p><a href='ajax.get_states.php?country_id=$zambiaId' target='_blank'>Click to test: ajax.get_states.php?country_id=$zambiaId</a></p>";
?>