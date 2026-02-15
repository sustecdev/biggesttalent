<?php
// Bootstrap MVC App
$vendorAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}
require_once '../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;
use App\Core\Database;
use App\Core\Router;

// Load Config
Config::load();

// App Constants/Helpers
require_once '../app/helpers/functions.php';
require_once '../app/helpers/yemchain_helper.php';

// Init DB connection
$db = Database::getInstance();
$GLOBALS['mysqli'] = $db->getConnection(); // For legacy compatibility with helpers

// Sync session role from DB when logged in (ensures admin upgrades show immediately)
if (isset($_SESSION['uid']) && $_SESSION['uid'] && function_exists('getUserRoleByUid')) {
    $_SESSION['role'] = getUserRoleByUid((int) $_SESSION['uid'], $_SESSION['pernum'] ?? null);
}

// Init Router
$init = new Router();
