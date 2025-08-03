<?php
/**
 * Check database access and available databases
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🔍 Database Access Check</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

echo "<h2>1️⃣ Testing with current connection (from .env):</h2>";

// Show current connection details (without password)
echo "<div class='code'>";
echo "Host: " . DB_HOST . "<br>";
echo "User: " . DB_USER . "<br>";
echo "Database: " . DB_NAME . "<br>";
echo "</div>";

// Use existing connection
global $connection;
if ($connection && !$connection->connect_error) {
    echo "<p class='success'>✅ Current connection is working</p>";
    
    // Show current database
    $current_db = $connection->query("SELECT DATABASE() as db")->fetch_assoc()['db'];
    echo "<p>Current database: <strong>$current_db</strong></p>";
    
    // List all accessible databases
    echo "<h3>Accessible databases:</h3>";
    $dbs_result = $connection->query("SHOW DATABASES");
    echo "<ul>";
    while ($row = $dbs_result->fetch_array()) {
        $db_name = $row[0];
        if ($db_name == '11klassniki_claude') {
            echo "<li class='success'><strong>$db_name</strong> ✅ (Target database found!)</li>";
        } else {
            echo "<li>$db_name</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ Current connection failed</p>";
}

echo "<h2>2️⃣ Testing admin_claude user:</h2>";

// Try different connection methods
$test_configs = [
    [
        'host' => '11klassnikiru67871.ipagemysql.com',
        'user' => 'admin_claude',
        'pass' => 'Secure9#Klass',
        'db' => '11klassniki_claude'
    ],
    [
        'host' => DB_HOST,
        'user' => 'admin_claude',
        'pass' => 'Secure9#Klass',
        'db' => '11klassniki_claude'
    ],
    [
        'host' => 'localhost',
        'user' => 'admin_claude',
        'pass' => 'Secure9#Klass',
        'db' => '11klassniki_claude'
    ]
];

foreach ($test_configs as $i => $config) {
    echo "<h3>Test " . ($i + 1) . ": {$config['host']}</h3>";
    
    try {
        @$test_conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        
        if ($test_conn->connect_error) {
            echo "<p class='error'>❌ Failed: " . $test_conn->connect_error . "</p>";
            
            // Try without selecting database
            @$test_conn2 = new mysqli($config['host'], $config['user'], $config['pass']);
            if (!$test_conn2->connect_error) {
                echo "<p class='warning'>⚠️ Can connect without database selection</p>";
                
                // List databases this user can see
                $dbs = $test_conn2->query("SHOW DATABASES");
                if ($dbs) {
                    echo "<p>Databases visible to admin_claude:</p><ul>";
                    while ($row = $dbs->fetch_array()) {
                        echo "<li>{$row[0]}</li>";
                    }
                    echo "</ul>";
                }
                $test_conn2->close();
            }
        } else {
            echo "<p class='success'>✅ Connection successful!</p>";
            $test_conn->close();
            break;
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>3️⃣ Checking with original user:</h2>";

try {
    @$old_conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if (!$old_conn->connect_error) {
        echo "<p class='success'>✅ Connected with user: " . DB_USER . "</p>";
        
        // Check if can access claude database
        if ($old_conn->select_db('11klassniki_claude')) {
            echo "<p class='success'>✅ Original user CAN access 11klassniki_claude database!</p>";
            echo "<p class='info'>ℹ️ You can use the original database credentials with the new database.</p>";
            
            // Show some stats
            $tables = $old_conn->query("SHOW TABLES");
            echo "<p>Tables in 11klassniki_claude: <strong>" . $tables->num_rows . "</strong></p>";
        } else {
            echo "<p class='error'>❌ Original user cannot access 11klassniki_claude database</p>";
        }
        
        $old_conn->close();
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>📋 Summary & Recommendations:</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p>Based on the tests above:</p>";
echo "<ol>";
echo "<li>Check if <strong>11klassniki_claude</strong> database exists in your hosting panel</li>";
echo "<li>Verify that the user has proper permissions to access it</li>";
echo "<li>You may need to grant permissions in phpMyAdmin or hosting control panel</li>";
echo "<li>Alternative: Use the original database user if it has access to the claude database</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>