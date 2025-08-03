<?php
// loadEnv.php - Updated to work without Composer and use new database

// Function to parse .env file
function parseEnvFile($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }
    
    $envContent = file_get_contents($filePath);
    $lines = explode("\n", $envContent);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
        }
    }
    return true;
}

// Check for production environment file first
$envLoaded = false;
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.production')) {
    $envLoaded = parseEnvFile($_SERVER['DOCUMENT_ROOT'] . '/.env.production');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env')) {
    $envLoaded = parseEnvFile($_SERVER['DOCUMENT_ROOT'] . '/.env');
}

if (!$envLoaded) {
    // No environment file found
    error_log("Environment configuration file not found");
    die("Configuration error. Please contact the administrator.");
}

// Check if APP_ENV is set to 'under_construction'
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'under_construction') {
    // If under construction, we don't need to check for database variables
    return;
}

// Check if environment variables are loaded
if (!isset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'])) {
    error_log("Database configuration not found in environment variables");
    die("Database configuration error. Please contact the administrator.");
}

// Define the constants using the loaded environment variables
if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
}
