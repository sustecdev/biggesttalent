<?php

// Test Season Model
require_once 'app/core/Autoloader.php';
require_once 'app/core/Config.php';
require_once 'app/core/Database.php';
require_once 'app/core/Controller.php';

// Manual require for Model if Autoloader relies on SplClassLoader which might need setup
// But app/core/Autoloader.php likely registers spl_autoload_register.
// Let's assume basic includes for core, then register autoloader.

// Load Config
\App\Core\Config::load();

// Load Model
require_once 'app/models/Season.php';

use App\Models\Season;

echo "Instantiating Season Model...\n";
$season = new Season();
echo "Season Model Instantiated.\n";

echo "Attempting to create a test season...\n";
$data = [
    'season_number' => 1,
    'title' => 'Season 1: Rising Stars',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+30 days')),
    'description' => 'The inaugural season of Biggest Talent Africa'
];

$result = $season->save($data);

echo "Save Result:\n";
print_r($result);

if ($result['success']) {
    echo "Season created successfully.\n";
} else {
    echo "Failed to create season: " . ($result['message'] ?? 'Unknown error') . "\n";
}

echo "Done.\n";
