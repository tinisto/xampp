<?php
/**
 * Check .env file status and database configuration
 */

echo "<h1>🔍 Environment Configuration Check</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }
</style>";

// Check .env file
echo "<h2>1️⃣ .env File Check</h2>";
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';

if (file_exists($env_path)) {
    echo "<p class='success'>✅ .env file exists</p>";
    
    $env_content = file_get_contents($env_path);
    echo "<h3>Current .env content:</h3>";
    echo "<div class='code'>" . htmlspecialchars($env_content) . "</div>";
    
    // Check which database is configured
    if (strpos($env_content, 'DB_NAME=11klassniki_claude') !== false) {
        echo "<p class='success'>✅ .env is configured for 11klassniki_claude</p>";
    } elseif (strpos($env_content, 'DB_NAME=11klassniki_ru') !== false) {
        echo "<p class='error'>❌ .env is still configured for old database (11klassniki_ru)</p>";
    } else {
        echo "<p class='warning'>⚠️ DB_NAME not found in .env</p>";
    }
} else {
    echo "<p class='error'>❌ .env file not found</p>";
}

// Check current PHP constants
echo "<h2>2️⃣ PHP Constants Check</h2>";
echo "<div class='code'>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
echo "</div>";

// Check active database connection
echo "<h2>3️⃣ Active Database Connection</h2>";
if (isset($connection) && !$connection->connect_error) {
    $db_result = $connection->query("SELECT DATABASE() as db");
    $current_db = $db_result->fetch_assoc()['db'];
    echo "<p>Current database in use: <strong>$current_db</strong></p>";
    
    if ($current_db === '11klassniki_claude') {
        echo "<p class='success'>✅ Using the new database</p>";
    } else {
        echo "<p class='error'>❌ Still using old database</p>";
    }
}

// Check loadEnv.php
echo "<h2>4️⃣ loadEnv.php Check</h2>";
$loadenv_path = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($loadenv_path)) {
    echo "<p class='success'>✅ loadEnv.php exists</p>";
    
    // Show first 50 lines
    $loadenv_content = file_get_contents($loadenv_path);
    $lines = explode("\n", $loadenv_content);
    $preview = implode("\n", array_slice($lines, 0, 50));
    echo "<h3>loadEnv.php preview (first 50 lines):</h3>";
    echo "<div class='code'>" . htmlspecialchars($preview) . "</div>";
} else {
    echo "<p class='error'>❌ loadEnv.php not found</p>";
}

// Recommendations
echo "<h2>📋 Recommendations</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";

if (defined('DB_NAME') && DB_NAME === '11klassniki_ru') {
    echo "<p class='error'><strong>Issue:</strong> The application is still using the old database.</p>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ol>";
    echo "<li>The .env file may have been updated but PHP is caching the old values</li>";
    echo "<li>Try restarting PHP/Apache to reload the configuration</li>";
    echo "<li>Check if loadEnv.php is properly reading the .env file</li>";
    echo "<li>There might be a hardcoded database name somewhere</li>";
    echo "</ol>";
}

echo "</div>";

// Quick fix button
echo "<h2>🔧 Quick Fix</h2>";
echo "<p>If the .env file needs updating:</p>";
echo "<p><a href='/update_env_correct_pass.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Update .env File</a></p>";

echo "<p style='margin-top: 30px;'><a href='/test_new_structure.php'>← Back to Structure Test</a></p>";
?>