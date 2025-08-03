<?php
/**
 * Test connection with correct admin_claude credentials
 */

echo "<h1>🔌 Testing Connection with Correct Credentials</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Test with correct credentials
$configs = [
    [
        'name' => '11klassniki_new',
        'host' => '11klassnikiru67871.ipagemysql.com',
        'user' => 'admin_claude',
        'pass' => 'W4eZ!#9uwLmrMay',
        'db' => '11klassniki_new'
    ],
    [
        'name' => '11klassniki_claude',
        'host' => '11klassnikiru67871.ipagemysql.com',
        'user' => 'admin_claude',
        'pass' => 'W4eZ!#9uwLmrMay',
        'db' => '11klassniki_claude'
    ]
];

foreach ($configs as $config) {
    echo "<h2>Testing: {$config['name']}</h2>";
    
    try {
        $test_db = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        
        if ($test_db->connect_error) {
            echo "<p class='error'>❌ Connection failed: " . $test_db->connect_error . "</p>";
        } else {
            echo "<p class='success'>✅ Successfully connected to {$config['name']}!</p>";
            
            // List tables
            $tables_result = $test_db->query("SHOW TABLES");
            $tables = [];
            while ($row = $tables_result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            echo "<p>Found " . count($tables) . " tables</p>";
            
            // Check for new structure
            $new_structure_tables = ['universities', 'colleges', 'areas', 'towns'];
            $has_new_structure = true;
            
            echo "<h3>Checking for clean structure:</h3>";
            echo "<ul>";
            foreach ($new_structure_tables as $table) {
                if (in_array($table, $tables)) {
                    echo "<li class='success'>✅ $table exists</li>";
                } else {
                    echo "<li class='error'>❌ $table not found</li>";
                    $has_new_structure = false;
                }
            }
            echo "</ul>";
            
            if ($has_new_structure) {
                // Quick data count
                echo "<h3>Data counts:</h3>";
                echo "<table>";
                echo "<tr><th>Table</th><th>Records</th></tr>";
                
                $count_tables = ['universities', 'colleges', 'schools', 'areas', 'towns', 'news', 'posts'];
                foreach ($count_tables as $table) {
                    if (in_array($table, $tables)) {
                        $count_result = $test_db->query("SELECT COUNT(*) as count FROM `$table`");
                        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
                        echo "<tr><td>$table</td><td>$count</td></tr>";
                    }
                }
                echo "</table>";
                
                echo "<div style='background: #d4edda; padding: 20px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
                echo "<p class='success'><strong>✅ This database is ready to use!</strong></p>";
                echo "</div>";
            }
            
            $test_db->close();
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h2>📋 Recommended .env Configuration:</h2>";
echo "<div style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; white-space: pre;'>";
echo "# Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=W4eZ!#9uwLmrMay
DB_NAME=11klassniki_claude  # or 11klassniki_new (whichever works)

# Site settings
SITE_URL=https://11klassniki.ru
SITE_NAME=11 Классники</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>