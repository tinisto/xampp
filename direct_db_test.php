<?php
/**
 * Direct database test - bypasses all existing configuration
 */

echo "<h1>🔌 Direct Database Connection Test</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
</style>";

// DO NOT include any existing files - we're testing raw connection

// Hardcode the connection details from the .env file
$configs = [
    'old_database' => [
        'host' => '11klassnikiru67871.ipagemysql.com',
        'user' => '11klone_user',
        'pass' => 'K8HqqBV3hTf4mha',
        'name' => '11klassniki_ru'
    ],
    'new_database' => [
        'host' => '11klassnikiru67871.ipagemysql.com',
        'user' => 'admin_claude',
        'pass' => 'W4eZ!#9uwLmrMay',
        'name' => '11klassniki_claude'
    ]
];

foreach ($configs as $label => $config) {
    echo "<h2>Testing: $label</h2>";
    echo "<div class='code'>";
    echo "Host: {$config['host']}<br>";
    echo "User: {$config['user']}<br>";
    echo "Database: {$config['name']}<br>";
    echo "</div>";
    
    try {
        $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
        
        if ($conn->connect_error) {
            echo "<p class='error'>❌ Connection failed: " . $conn->connect_error . "</p>";
        } else {
            echo "<p class='success'>✅ Connected successfully!</p>";
            
            // Get table list
            $tables_result = $conn->query("SHOW TABLES");
            $tables = [];
            while ($row = $tables_result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            echo "<p>Total tables: " . count($tables) . "</p>";
            
            // Check for specific tables
            $check_tables = ['universities', 'colleges', 'vpo', 'spo', 'schools', 'news', 'posts'];
            echo "<table>";
            echo "<tr><th>Table</th><th>Exists</th><th>Row Count</th></tr>";
            
            foreach ($check_tables as $table) {
                echo "<tr>";
                echo "<td>$table</td>";
                
                if (in_array($table, $tables)) {
                    echo "<td class='success'>✅ Yes</td>";
                    
                    $count_result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
                    $count = $count_result->fetch_assoc()['count'];
                    echo "<td>$count</td>";
                } else {
                    echo "<td class='error'>❌ No</td>";
                    echo "<td>-</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h2>📋 What This Means</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p>This test bypasses all PHP configuration and tests direct connections.</p>";
echo "<p>If <strong>new_database</strong> shows universities/colleges tables:</p>";
echo "<ul>";
echo "<li>✅ The new database structure is ready</li>";
echo "<li>❌ Your application is using cached/old configuration</li>";
echo "<li>🔧 Solution: Restart PHP/Apache or clear PHP opcache</li>";
echo "</ul>";
echo "</div>";

echo "<h2>🚀 Quick Actions</h2>";
echo "<ul>";
echo "<li><a href='/test_new_structure.php'>Back to Structure Test</a></li>";
echo "<li><a href='/check_env_status.php'>Check Environment Status</a></li>";
echo "<li>Contact hosting support to restart PHP/Apache</li>";
echo "</ul>";
?>