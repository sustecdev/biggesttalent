<?php

namespace App\Core;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            // Project-specific namespace prefix
            $prefix = 'App\\';

            // Base directory for the namespace prefix
            $base_dir = dirname(__DIR__) . '/';

            // Does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // Get the relative class name
            $relative_class = substr($class, $len);

            // Replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $parts = explode('\\', $relative_class);
            $className = array_pop($parts); // Keep class name CaseSensitive (Config.php)
            
            // Path Variation 1: Lowercase directories (app/core/Config.php)
            $namespacePathLower = strtolower(implode(DIRECTORY_SEPARATOR, $parts));
            $fileLower = $base_dir . $namespacePathLower . DIRECTORY_SEPARATOR . $className . '.php';

            // Path Variation 2: Original Casing (App/Core/Config.php) - Fallback
            $namespacePathOriginal = implode(DIRECTORY_SEPARATOR, $parts);
            $fileOriginal = $base_dir . $namespacePathOriginal . DIRECTORY_SEPARATOR . $className . '.php';

            // Variation 3: Lowercase Base + Mixed Subdirs (app/Core/Config.php) - Common quirk
            // Assume first part (App) is always lowercase (app), others keep case
            $partsMixed = $parts;
            if (!empty($partsMixed)) $partsMixed[0] = strtolower($partsMixed[0]);
            $namespacePathMixed = implode(DIRECTORY_SEPARATOR, $partsMixed);
            $fileMixed = $base_dir . $namespacePathMixed . DIRECTORY_SEPARATOR . $className . '.php';

            if (file_exists($fileLower)) {
                require $fileLower;
            } elseif (file_exists($fileMixed)) {
                require $fileMixed;
            } elseif (file_exists($fileOriginal)) {
                require $fileOriginal;
            }
        });
    }
}
