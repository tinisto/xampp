<?php
// Modern database connection with local/production detection

// Detect environment
$isLocal = (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] === 'localhost') || php_sapi_name() === 'cli';

if ($isLocal) {
    // Local configuration
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/database.local.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.local.php';
    } else {
        // Fallback local config
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', '11klassniki_local');
        define('DB_CHARSET', 'utf8mb4');
    }
} else {
    // Production configuration
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
}

// Create connection with error handling
try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }
    
    // Set charset
    $connection->set_charset(defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4');
    
    // Set timezone
    $connection->query("SET time_zone = '+03:00'");
    
} catch (Exception $e) {
    if ($isLocal) {
        // Show detailed error in development
        die("Database connection error: " . $e->getMessage() . "<br>
             Please ensure MySQL/MariaDB is running and database '" . DB_NAME . "' exists.<br>
             <pre>
             To create the database:
             1. Open terminal
             2. Run: mysql -u root
             3. Execute: CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
             </pre>");
    } else {
        // Generic error in production
        error_log("Database connection error: " . $e->getMessage());
        die("Database connection error. Please try again later.");
    }
}

// Helper function for prepared statements
function db_query($sql, $params = []) {
    global $connection;
    
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $connection->error);
    }
    
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            else $types .= 's';
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt;
}

// Helper function to fetch all results
function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to fetch single result
function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>