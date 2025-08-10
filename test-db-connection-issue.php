<?php
// Test why db_connections.php fails
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing db_connections.php Issue</h1>";

// Step 1: Check if environment is loaded
echo "<h2>Step 1: Environment Check</h2>";

// Check if constants are defined before including db_connections
echo "<p>Before including db_connections.php:</p>";
echo "<ul>";
echo "<li>DB_HOST defined: " . (defined('DB_HOST') ? 'YES - ' . DB_HOST : 'NO') . "</li>";
echo "<li>DB_USER defined: " . (defined('DB_USER') ? 'YES - ' . DB_USER : 'NO') . "</li>";
echo "<li>DB_NAME defined: " . (defined('DB_NAME') ? 'YES - ' . DB_NAME : 'NO') . "</li>";
echo "</ul>";

// Check if loadEnv.php works
echo "<h3>Testing loadEnv.php:</h3>";
$envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';
echo "<p>Looking for .env at: $envFile</p>";
echo "<p>.env exists: " . (file_exists($envFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($envFile)) {
    echo "<p>.env contents (first 5 lines):</p>";
    $lines = array_slice(file($envFile), 0, 5);
    echo "<pre>";
    foreach ($lines as $line) {
        // Hide password
        if (strpos($line, 'DB_PASS') !== false) {
            echo "DB_PASS=***hidden***\n";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
}

// Step 2: Test loadEnv.php
echo "<h2>Step 2: Testing loadEnv.php</h2>";
try {
    // Clear any existing definitions
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    echo "<p>After loadEnv.php:</p>";
    echo "<ul>";
    echo "<li>DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "</li>";
    echo "<li>DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "</li>";
    echo "<li>DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "</li>";
    echo "<li>DB_PASS: " . (defined('DB_PASS') ? '***hidden***' : 'NOT DEFINED') . "</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading environment: " . $e->getMessage() . "</p>";
}

// Step 3: Test db_connections.php
echo "<h2>Step 3: Testing db_connections.php</h2>";

// Capture any output/errors
ob_start();
$errorReporting = error_reporting();
error_reporting(E_ALL);

try {
    // Include db_connections
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "<p>Output from db_connections.php:</p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
    
    // Check if connection was created
    if (isset($connection)) {
        echo "<p style='color: green;'>✓ \$connection variable is set!</p>";
        
        // Test the connection
        $testQuery = $connection->query("SELECT DATABASE()");
        if ($testQuery) {
            $dbName = $testQuery->fetch_row()[0];
            echo "<p>Connected to database: <strong>$dbName</strong></p>";
            
            // Test news query
            $newsCount = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
            echo "<p>News table has <strong>$newsCount</strong> records</p>";
        } else {
            echo "<p style='color: red;'>Connection exists but query failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ \$connection variable NOT set!</p>";
        
        // Check for redirect
        $headers = headers_list();
        if (!empty($headers)) {
            echo "<p>Headers set by db_connections.php:</p>";
            echo "<pre>" . print_r($headers, true) . "</pre>";
        }
    }
} catch (Exception $e) {
    $output = ob_get_clean();
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
    if (!empty($output)) {
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
}

error_reporting($errorReporting);

// Step 4: Try manual connection with constants
echo "<h2>Step 4: Manual Connection Test</h2>";
if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    try {
        $testConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($testConn->connect_error) {
            echo "<p style='color: red;'>Manual connection failed: " . $testConn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Manual connection successful using constants!</p>";
            $testConn->close();
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Manual connection error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Cannot test manual connection - constants not defined</p>";
}
?>