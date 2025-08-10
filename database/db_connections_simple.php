<?php
// Simple database connection without redirects
require_once dirname(__DIR__) . '/config/loadEnv.php';

// Initialize connection variable
$connection = null;

// Check if constants are defined
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    try {
        // Create connection
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check for connection error
        if ($connection->connect_error) {
            error_log("Database connection failed: " . $connection->connect_error);
            $connection = null;
        } else {
            // Connection successful - set charset
            // Try utf8mb4 first, fall back to utf8
            if (!@$connection->set_charset('utf8mb4')) {
                @$connection->set_charset('utf8');
            }
        }
    } catch (Exception $e) {
        error_log("Database connection exception: " . $e->getMessage());
        $connection = null;
    }
} else {
    error_log("Database configuration constants not defined");
}

// At this point, $connection is either a valid mysqli object or null
// Pages using this file should check if ($connection) before using it
?>