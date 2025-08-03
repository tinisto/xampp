<?php
// Emergency connection cleanup
echo "<h1>🚨 Database Connection Cleanup</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
</style>";

// Load config
@include_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<div style='border: 2px solid red; padding: 20px; margin: 20px 0;'>";
echo "<h2>⚠️ Connection Limit Exceeded</h2>";
echo "<p><strong>Problem:</strong> User 11klone_user has too many active connections</p>";
echo "<p><strong>Solution:</strong> Wait 1-2 minutes for connections to timeout, or contact iPage support</p>";
echo "</div>";

echo "<h2>Current Status:</h2>";
echo "<p><strong>Database:</strong> " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</p>";
echo "<p><strong>User:</strong> " . (defined('DB_USER') ? DB_USER : 'Not defined') . "</p>";

echo "<h2>What to do:</h2>";
echo "<ol>";
echo "<li><strong>Wait 1-2 minutes</strong> - Connections will timeout automatically</li>";
echo "<li><strong>Contact iPage Support</strong> - Ask to increase max_user_connections for database user '11klone_user'</li>";
echo "<li><strong>Temporary Fix</strong> - Use a different database user if available</li>";
echo "</ol>";

echo "<h2>Why this happened:</h2>";
echo "<ul>";
echo "<li>Multiple migration scripts running</li>";
echo "<li>Unclosed database connections</li>";
echo "<li>Low connection limit on shared hosting</li>";
echo "</ul>";

// Try a simple connection after delay
echo "<h2>Testing connection...</h2>";
sleep(2); // Wait 2 seconds

try {
    $test = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($test && !$test->connect_error) {
        echo "<p class='success'>✅ Connection successful! Site should work now.</p>";
        echo "<p><a href='/' style='background: green; color: white; padding: 10px; text-decoration: none;'>Go to Homepage</a></p>";
        $test->close();
    } else {
        echo "<p class='error'>❌ Still too many connections. Wait a bit longer.</p>";
        echo "<p><a href='javascript:location.reload()' style='background: blue; color: white; padding: 10px; text-decoration: none;'>Retry</a></p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Emergency contacts:</strong></p>";
echo "<ul>";
echo "<li>iPage Support: Request to increase 'max_user_connections' for database</li>";
echo "<li>Database: 11klassniki_ru</li>";
echo "<li>User: 11klone_user</li>";
echo "</ul>";
?>