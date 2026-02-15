<?php

namespace App\Core;

class Config
{
    public static function load()
    {
        // Load environment variables
        self::loadEnv();

        // Set timezone
        date_default_timezone_set(getenv('TIMEZONE') ?: 'America/New_York');

        // Define constants if not already defined
        self::defineConstants();

        // Setup Session
        self::setupSession();
    }

    private static function loadEnv()
    {
        $envFile = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false)
            return;

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0)
                continue;

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                if (
                    (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
                ) {
                    $value = substr($value, 1, -1);
                }

                if (!getenv($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    private static function defineConstants()
    {
        // YemChain API
        if (!defined('YEMCHAIN_API_KEY')) {
            define('YEMCHAIN_API_KEY', getenv('YEMCHAIN_API_KEY'));
        }

        // SafeZone API
        if (!defined('SAFEZONE_API_KEY')) {
            define('SAFEZONE_API_KEY', getenv('SAFEZONE_API_KEY'));
        }

        // Database defaults from ENV only
        $dbServer = getenv('DB_SERVER');
        $dbUser = getenv('DB_USERNAME') ?: getenv('DB_USER');
        $dbPass = getenv('DB_PASSWORD') ?: getenv('DB_PASS');
        $dbName = getenv('DB_NAME');

        if (!defined('DB_SERVER'))
            define('DB_SERVER', $dbServer);
        if (!defined('DB_USERNAME'))
            define('DB_USERNAME', $dbUser);
        if (!defined('DB_PASSWORD'))
            define('DB_PASSWORD', $dbPass);
        if (!defined('DB_NAME'))
            define('DB_NAME', $dbName);

        // Compat constants
        if (!defined('DB_USER'))
            define('DB_USER', $dbUser);
        if (!defined('DB_PASS'))
            define('DB_PASS', $dbPass);

        // App Root
        if (!defined('APPROOT'))
            define('APPROOT', dirname(__DIR__));
        if (!defined('URLROOT')) {
            define('URLROOT', self::getUrlRoot());
        }
        if (!defined('BASE_URL')) {
            define('BASE_URL', URLROOT . '/');
        }
        if (!defined('LEGACY_ROOT')) {
            define('LEGACY_ROOT', str_replace('/public', '/legacy', URLROOT));
        }
        
        // SafeZone Registration API Key
        if (!defined('SAFEZONE_REGISTRATION_API_KEY')) {
            define('SAFEZONE_REGISTRATION_API_KEY', getenv('SAFEZONE_REGISTRATION_API_KEY') ?: SAFEZONE_API_KEY);
        }
        
        // Session Lifetime
        if (!defined('SESSION_LIFETIME')) {
            define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 86400 * 30); // 30 days default
        }
        
        // App Debug Mode
        if (!defined('APP_DEBUG')) {
            define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));
        }
    }

    private static function setupSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $isLocal = (
                $host === 'localhost' ||
                strpos($host, '127.0.0.1') !== false ||
                strpos($host, '192.168.') !== false ||
                strpos($host, '10.') !== false ||
                filter_var($host, FILTER_VALIDATE_IP) !== false
            );

            if (!$isLocal && !empty($host)) {
                $cookieDomain = getenv('SESSION_COOKIE_DOMAIN') ?: '.biggesttalent.africa';
                ini_set('session.cookie_domain', $cookieDomain);
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_samesite', 'Lax');
                session_set_cookie_params(0, '/', $cookieDomain, isset($_SERVER['HTTPS']), true);
            } else {
                ini_set('session.cookie_domain', '');
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_samesite', 'Lax');
                session_set_cookie_params(0, '/', '', false, true);
            }
            session_start();
        }
    }

    private static function getUrlRoot()
    {
        // Simple detection, can be improved or set via ENV
        // Dynamic URL Root detection
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /SUSTECAFRICA/shobbitNew/BiggestTalent/public/index.php
        $dir = dirname($scriptName); // Go up from public/index.php -> public

        // Remove backslashes if on Windows and convert to forward slashes for URL
        $dir = str_replace('\\', '/', $dir);

        // Ensure no trailing slash
        $dir = rtrim($dir, '/');

        // IF the script is running from the "public" directory, we usually want URLROOT 
        // to point to the PARENT (so we don't have /public in the URL),
        // assuming the root .htaccess rewrites requests to public/
        if (substr($dir, -7) === '/public') {
            $dir = substr($dir, 0, -7);
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $protocol . '://' . $host . $dir;
    }
}
