<?php
/**
 * Database Connection with Automatic Environment Detection
 * 
 * This file automatically switches between development and production
 * database settings based on the current environment.
 */

// Detect environment based on hostname
$isLocal = false;
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Check if we're on localhost or development environment
if (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '::1') !== false ||
    strpos($host, '.local') !== false ||
    strpos($host, 'dev.') === 0 ||
    (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost')
) {
    $isLocal = true;
}

// Also check if we're running from command line
if (php_sapi_name() === 'cli') {
    $isLocal = true;
}

// Set database credentials based on environment
if ($isLocal) {
    // ========================================
    // DEVELOPMENT SETTINGS (your local XAMPP)
    // ========================================
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "11klassniki"; // Update this to your local database name
    
    // Enable error reporting for development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Optional: Show which environment we're using
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'development');
    }
    
} else {
    // ========================================
    // PRODUCTION SETTINGS (11klassniki.ru)
    // ========================================
    // IMPORTANT: Update these with your actual production credentials
    $servername = "localhost"; // Usually localhost even on shared hosting
    $username = "your_production_db_user"; // Replace with actual username
    $password = "your_production_db_pass"; // Replace with actual password
    $dbname = "your_production_db_name";   // Replace with actual database name
    
    // Disable error display for production
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'production');
    }
}

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    if ($isLocal) {
        // Show detailed error in development
        die("Connection failed: " . $connection->connect_error);
    } else {
        // Log error and redirect in production
        error_log("Database connection failed: " . $connection->connect_error);
        header("Location: /error");
        exit();
    }
}

// Set charset to utf8mb4
if (!$connection->set_charset("utf8mb4")) {
    if ($isLocal) {
        die("Error loading character set utf8mb4: " . $connection->error);
    } else {
        error_log("Character set error: " . $connection->error);
        header("Location: /error");
        exit();
    }
}

// Set timezone to Moscow time (adjust if needed)
$connection->query("SET time_zone = '+03:00'");

// Helper functions
function isDevelopment() {
    return ENVIRONMENT === 'development';
}

function isProduction() {
    return ENVIRONMENT === 'production';
}

// Optional: Debug output in development
if ($isLocal && isset($_GET['debug'])) {
    echo "<pre>";
    echo "Environment: " . ENVIRONMENT . "\n";
    echo "Host: " . $host . "\n";
    echo "Database: " . $dbname . "\n";
    echo "</pre>";
}
?>