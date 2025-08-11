<?php
// Check database configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Configuration Check</h1>\n";

// Check for .env file
if (file_exists('.env')) {
    echo "<p>✅ .env file exists</p>\n";
    $env = parse_ini_file('.env');
    echo "<pre>Environment variables found:\n";
    foreach ($env as $key => $value) {
        if (strpos($key, 'PASS') !== false || strpos($key, 'PASSWORD') !== false) {
            echo "$key = ***hidden***\n";
        } else {
            echo "$key = $value\n";
        }
    }
    echo "</pre>\n";
} else {
    echo "<p>❌ .env file not found</p>\n";
}

// Check config directory
echo "<h2>Config files:</h2>\n";
if (is_dir('config')) {
    $files = scandir('config');
    echo "<ul>\n";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file (" . filesize("config/$file") . " bytes)</li>\n";
        }
    }
    echo "</ul>\n";
}

// Try to load config
echo "<h2>Loading config:</h2>\n";
if (file_exists('config/loadEnv.php')) {
    // Don't actually include it to avoid errors
    echo "<p>config/loadEnv.php exists and would be loaded</p>\n";
}

// Check what DB constants/env vars are set
echo "<h2>Database settings check:</h2>\n";
$db_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($db_vars as $var) {
    if (defined($var)) {
        if (strpos($var, 'PASS') !== false) {
            echo "<p>$var is defined (hidden)</p>\n";
        } else {
            echo "<p>$var = " . constant($var) . "</p>\n";
        }
    }
    if (isset($_ENV[$var])) {
        if (strpos($var, 'PASS') !== false) {
            echo "<p>\$_ENV['$var'] is set (hidden)</p>\n";
        } else {
            echo "<p>\$_ENV['$var'] = " . $_ENV[$var] . "</p>\n";
        }
    }
}
?>