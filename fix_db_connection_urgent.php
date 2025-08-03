<?php
// Check and fix database connection
echo "<h1>Database Connection Fix</h1>";

// Check current .env
echo "<h2>1. Checking .env file</h2>";
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (file_exists($env_path)) {
    $env_content = file_get_contents($env_path);
    echo "<pre>" . htmlspecialchars(substr($env_content, 0, 500)) . "...</pre>";
    
    if (strpos($env_content, 'DB_NAME=11klassniki_claude') !== false) {
        echo "<p style='color: green;'>✅ .env file has correct database name</p>";
    } else {
        echo "<p style='color: red;'>❌ .env file has WRONG database name</p>";
    }
}

// Check loadEnv.php
echo "<h2>2. Checking loadEnv.php</h2>";
$loadenv_path = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($loadenv_path)) {
    require_once $loadenv_path;
    echo "<p>DB_NAME constant: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "</p>";
}

// Check db_connections.php
echo "<h2>3. Checking db_connections.php</h2>";
$db_conn_path = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
if (file_exists($db_conn_path)) {
    $db_content = file_get_contents($db_conn_path);
    echo "<pre>" . htmlspecialchars(substr($db_content, 0, 500)) . "...</pre>";
    
    if (strpos($db_content, '$force_new_db = true') !== false) {
        echo "<p style='color: red;'>⚠️ Force flag is still ENABLED!</p>";
    } else {
        echo "<p style='color: green;'>✅ Force flag is disabled</p>";
    }
}

// Test actual connection
echo "<h2>4. Testing Database Connection</h2>";
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
$current_db = $connection->query("SELECT DATABASE() as db")->fetch_assoc()['db'];
echo "<p>Currently connected to: <strong style='color: " . ($current_db == '11klassniki_claude' ? 'green' : 'red') . ";'>$current_db</strong></p>";

if ($current_db != '11klassniki_claude') {
    echo "<h2 style='color: red;'>🚨 URGENT: Database connection is wrong!</h2>";
    echo "<p>The site is connecting to the OLD database instead of the new one.</p>";
    
    // Try to fix
    if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
        echo "<h3>Attempting to fix...</h3>";
        
        // Update .env file
        if (file_exists($env_path)) {
            $new_env = str_replace('DB_NAME=11klassniki_ru', 'DB_NAME=11klassniki_claude', $env_content);
            if (file_put_contents($env_path, $new_env)) {
                echo "<p>✅ Updated .env file</p>";
            } else {
                echo "<p>❌ Could not update .env file</p>";
            }
        }
        
        echo "<p style='color: orange;'>⚠️ You may need to restart PHP or clear opcache for changes to take effect.</p>";
        echo "<p><a href='?'>Refresh to check again</a></p>";
    } else {
        echo "<p><a href='?fix=yes' style='background: red; color: white; padding: 10px 20px; text-decoration: none;'>FIX DATABASE CONNECTION NOW</a></p>";
    }
} else {
    echo "<p style='color: green;'>✅ Database connection is correct!</p>";
}

echo "<hr>";
echo "<p><a href='/site_review.php'>← Back to Site Review</a></p>";
?>