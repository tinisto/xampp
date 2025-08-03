<?php
// Unified loadEnv.php that handles both production and development

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
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.production')) {
    parseEnvFile($_SERVER['DOCUMENT_ROOT'] . '/.env.production');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env')) {
    parseEnvFile($_SERVER['DOCUMENT_ROOT'] . '/.env');
} else {
    // Fallback to new database credentials
    $_ENV['APP_ENV'] = 'production';
    $_ENV['DB_HOST'] = '11klassnikiru67871.ipagemysql.com';
    $_ENV['DB_USER'] = 'admin_claude';
    $_ENV['DB_PASS'] = 'W4eZ!#9uwLmrMay';
    $_ENV['DB_NAME'] = '11klassniki_claude';
}

// Check if APP_ENV is set to 'under_construction'
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'under_construction') {
    return;
}

// Check if environment variables are loaded
if (!isset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME'])) {
    // Use new database fallback values
    $_ENV['DB_HOST'] = '11klassnikiru67871.ipagemysql.com';
    $_ENV['DB_USER'] = 'admin_claude';
    $_ENV['DB_PASS'] = 'W4eZ!#9uwLmrMay';
    $_ENV['DB_NAME'] = '11klassniki_claude';
}

// Define the constants using the loaded environment variables
if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
}
?>