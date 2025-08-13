<?php
// Database connection with security improvements
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security/security_config.php';

// Database configuration
$db_config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'passwords' => ['root', ''], // Try multiple passwords for local dev
    'database' => '11klassniki_ru',
    'charset' => 'utf8mb4'
];

// Initialize connection
$connection = null;

try {
    // Try each password configuration
    foreach ($db_config['passwords'] as $password) {
        $connection = new mysqli(
            $db_config['host'], 
            $db_config['username'], 
            $password, 
            $db_config['database']
        );
        
        if (!$connection->connect_error) {
            // Set charset and options
            $connection->set_charset($db_config['charset']);
            
            // Additional security settings
            $connection->query("SET SESSION sql_mode = 'TRADITIONAL,NO_AUTO_VALUE_ON_ZERO'");
            
            break; // Successfully connected
        }
    }
    
    // Check if all attempts failed
    if ($connection->connect_error) {
        error_log("Database connection failed: " . $connection->connect_error);
        $connection = null;
    }
    
} catch (Exception $e) {
    error_log("Database exception: " . $e->getMessage());
    $connection = null;
}
?>