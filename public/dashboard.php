<?php
// Redirect public/dashboard.php to public/dashboard route
require_once '../app/core/Config.php';
// Load config to access URLROOT if possible, or relative
$url = 'dashboard';
header("Location: $url");
exit;
