<?php
// Simple test file to bypass redirect issues
echo "<h1>Database Connection Test</h1>";

// Try to load config without redirects
$config_file = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($config_file)) {
    @include_once $config_file;
    echo "<p>✅ Config loaded</p>";
} else {
    echo "<p>❌ Config not found</p>";
}

// Test database connection
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    echo "<p>Database credentials found:</p>";
    echo "<ul>";
    echo "<li>Host: " . DB_HOST . "</li>";
    echo "<li>Database: " . DB_NAME . "</li>";
    echo "<li>User: " . DB_USER . "</li>";
    echo "</ul>";
    
    try {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($connection->connect_error) {
            echo "<p>❌ Connection failed: " . $connection->connect_error . "</p>";
        } else {
            echo "<p>✅ Database connected successfully!</p>";
            
            // Test query
            $result = $connection->query("SELECT COUNT(*) as count FROM regions");
            if ($result) {
                $count = $result->fetch_assoc()['count'];
                echo "<p>✅ Query successful: $count regions found</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p>❌ Exception: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Database credentials not defined</p>";
}

echo "<p><strong>No redirects from this page!</strong></p>";
?>