<?php
// Bootstrap MVC App
require_once __DIR__ . '/../app/core/Autoloader.php';
App\Core\Autoloader::register();

use App\Core\Config;

// Load Config
Config::load();

// Load Helper
require_once __DIR__ . '/../app/helpers/yemchain_helper.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = __DIR__ . '/debug_output.txt';
$output = "";

$output .= "YemChain DBV Debug\n";
$output .= "==================\n";

// Check API Key
if (defined('YEMCHAIN_API_KEY')) {
    $output .= "API Key is defined: " . substr(YEMCHAIN_API_KEY, 0, 5) . "...\n";
} else {
    $output .= "API Key is NOT defined! Value: " . (getenv('YEMCHAIN_API_KEY') ?: 'False/Empty') . "\n";
}

// Test Data
$testUid = '123456'; 
$output .= "Testing yemchain_get_balance for UID: $testUid\n";
$balance = yemchain_get_balance($testUid, 'DBV');

$output .= "Result:\n" . print_r($balance, true) . "\n";

// Check cURL
if (function_exists('curl_init')) {
    $output .= "cURL is enabled.\n";
} else {
    $output .= "cURL is disabled.\n";
}

file_put_contents($logFile, $output);
echo "Debug output written to $logFile";
