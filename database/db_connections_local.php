<?php
// Local development database connection
// This works without requiring a real database

// For local testing - use mock data
$connection = null;

// Check if we're in local development
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') {
    // Local development mode - don't require real database
    error_log("Running in local development mode - database connections mocked");
} else {
    // Production mode - use real database
    require_once __DIR__ . '/../config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        try {
            $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($connection->connect_error) {
                error_log("Database connection failed: " . $connection->connect_error);
                $connection = null;
            } else {
                // Set charset
                if (!@$connection->set_charset('utf8mb4')) {
                    @$connection->set_charset('utf8');
                }
            }
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            $connection = null;
        }
    }
}
?>