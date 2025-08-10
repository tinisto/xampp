<?php
// Database connection test page
echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo "<head>\n";
echo "<title>Database Connection Test</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".info { color: blue; }\n";
echo "pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }\n";
echo "</style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<h1>Database Connection Test</h1>\n";

echo "<h2>Test 1: Direct Connection (Hardcoded)</h2>\n";
try {
    $direct_conn = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        'admin_claude',
        'W4eZ!#9uwLmrMay',
        '11klassniki_claude'
    );
    
    if ($direct_conn->connect_error) {
        echo "<p class='error'>Direct connection failed: " . htmlspecialchars($direct_conn->connect_error) . "</p>\n";
    } else {
        echo "<p class='success'>Direct connection successful!</p>\n";
        
        // Test charset
        if ($direct_conn->set_charset('utf8mb4')) {
            echo "<p class='success'>UTF8MB4 charset set successfully</p>\n";
        } else {
            if ($direct_conn->set_charset('utf8')) {
                echo "<p class='info'>UTF8 charset set (UTF8MB4 not available)</p>\n";
            } else {
                echo "<p class='error'>Failed to set charset</p>\n";
            }
        }
        
        // Test query
        $result = $direct_conn->query("SELECT COUNT(*) as count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p class='success'>Query successful! Users table has " . $row['count'] . " records</p>\n";
        } else {
            echo "<p class='error'>Query failed: " . htmlspecialchars($direct_conn->error) . "</p>\n";
        }
        
        $direct_conn->close();
    }
} catch (Exception $e) {
    echo "<p class='error'>Exception: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h2>Test 2: Using db_connections.php</h2>\n";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
        
        if (isset($connection) && $connection && !$connection->connect_error) {
            echo "<p class='success'>db_connections.php loaded successfully!</p>\n";
            
            // Test query
            $result = $connection->query("SELECT COUNT(*) as count FROM posts");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p class='success'>Query successful! Posts table has " . $row['count'] . " records</p>\n";
            } else {
                echo "<p class='error'>Query failed: " . htmlspecialchars($connection->error) . "</p>\n";
            }
        } else {
            echo "<p class='error'>db_connections.php loaded but connection failed</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Exception loading db_connections.php: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
} else {
    echo "<p class='error'>db_connections.php not found</p>\n";
}

echo "<h2>Test 3: Environment Variables</h2>\n";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env')) {
    echo "<p class='success'>.env file exists</p>\n";
    
    // Check if loadEnv.php exists
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php')) {
        echo "<p class='success'>loadEnv.php exists</p>\n";
        
        // Try to load env
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
        
        if (defined('DB_HOST')) {
            echo "<p class='success'>Environment variables loaded:</p>\n";
            echo "<pre>\n";
            echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT SET') . "\n";
            echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT SET') . "\n";
            echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT SET') . "\n";
            echo "DB_PASS: " . (defined('DB_PASS') ? '***HIDDEN***' : 'NOT SET') . "\n";
            echo "</pre>\n";
        } else {
            echo "<p class='error'>Environment variables not loaded</p>\n";
        }
    } else {
        echo "<p class='error'>loadEnv.php not found</p>\n";
    }
} else {
    echo "<p class='error'>.env file not found</p>\n";
}

echo "<h2>Server Information</h2>\n";
echo "<pre>\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Path: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "</pre>\n";

echo "</body>\n";
echo "</html>\n";