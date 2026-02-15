<?php
// Test Autoloader
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;
use App\Controllers\HomeController;

try {
    echo "Loading Config...\n";
    // Config::load() might need constant definitions usually in index.php or config file? 
    // Config::load() loads environment variables.
    Config::load();
    echo "Config loaded.\n";

    echo "Loading Database...\n";
    $db = Database::getInstance();
    echo "Database loaded.\n";

    echo "Loading HomeController...\n";
    $controller = new HomeController();
    echo "HomeController loaded.\n";

    echo "SUCCESS: All classes loaded via Autoloader.\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
