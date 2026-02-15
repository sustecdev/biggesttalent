<?php
/**
 * AJAX Endpoint: Get States/Provinces by Country
 * Returns states/provinces for a selected country
 */

// Bootstrap the application properly
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;

// Load configuration (this defines DB_SERVER, DB_USERNAME, etc.)
Config::load();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['country_id'])) {
    $country_id = intval($_GET['country_id']);

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("
            SELECT id, name, state_code 
            FROM states_provinces 
            WHERE country_id = ? 
            ORDER BY name ASC
        ");

        $stmt->bind_param("i", $country_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $states = [];
        while ($row = $result->fetch_assoc()) {
            $states[] = $row;
        }

        echo json_encode([
            'success' => true,
            'states' => $states
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to fetch states/provinces'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request'
    ]);
}
