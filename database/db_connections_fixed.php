<?php
require_once __DIR__ . '/../config/loadEnv.php';

// Initialize connection variable
$connection = null;

// Check if the constants are defined
if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    error_log("Database configuration not found in environment variables");
    // Don't redirect, just log the error
    return;
}

// Establish the database connection using .env values
try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check the connection
    if ($connection->connect_error) {
        error_log("Database connection failed: " . $connection->connect_error);
        $connection = null;
        return;
    }
    
    // Check if the server supports utf8mb4
    $result = @$connection->query("SHOW CHARACTER SET LIKE 'utf8mb4';");
    if ($result && $result->num_rows > 0) {
        // Set the charset if supported
        $connection->set_charset('utf8mb4');
    } else {
        // Fall back to utf8 if utf8mb4 is not supported
        $connection->set_charset('utf8');
    }
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $connection = null;
}

// If connection failed and we're not in CLI mode, we could redirect
// But for now, let's just ensure $connection is available (even if null)
// This allows pages to handle the error gracefully
?>