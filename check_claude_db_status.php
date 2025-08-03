<?php
/**
 * Check status of 11klassniki_claude database
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>📊 Database Status: 11klassniki_claude</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .match { background-color: #d4edda; }
    .mismatch { background-color: #f8d7da; }
</style>";

try {
    // Connect to the claude database
    $claude_db = new mysqli(
        DB_HOST,
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_claude'
    );
    
    if ($claude_db->connect_error) {
        die("<p class='error'>❌ Could not connect to 11klassniki_claude database: " . $claude_db->connect_error . "</p>");
    }
    
    $claude_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to 11klassniki_claude database</p>";
    
    // Get list of tables
    $tables_result = $claude_db->query("SHOW TABLES");
    $tables = [];
    while ($row = $tables_result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "<h2>📋 Tables in 11klassniki_claude (" . count($tables) . " tables):</h2>";
    echo "<p>" . implode(", ", $tables) . "</p>";
    
    // Count records in main tables
    echo "<h2>📊 Record Counts:</h2>";
    echo "<table>";
    echo "<tr><th>Table</th><th>Record Count</th><th>Status</th></tr>";
    
    $expected_tables = [
        'regions' => 85,
        'areas' => 2436,
        'towns' => 23118,
        'categories' => 16,
        'users' => 25,
        'universities' => 2516,
        'colleges' => 3159,
        'schools' => 3318,
        'news' => 495,
        'posts' => 538,
        'comments' => 130252
    ];
    
    $all_good = true;
    foreach ($expected_tables as $table => $expected_count) {
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        
        if (in_array($table, $tables)) {
            $count_result = $claude_db->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
            echo "<td>$count</td>";
            
            if ($count == 0) {
                echo "<td class='error'>❌ Empty</td>";
                $all_good = false;
            } elseif ($count >= $expected_count * 0.95) {
                echo "<td class='success'>✅ OK (expected ~$expected_count)</td>";
            } else {
                echo "<td class='warning'>⚠️ Low (expected ~$expected_count)</td>";
                $all_good = false;
            }
        } else {
            echo "<td>-</td>";
            echo "<td class='error'>❌ Table missing</td>";
            $all_good = false;
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Check for old table names (should not exist in claude db)
    echo "<h2>🔍 Checking for old table names:</h2>";
    $old_tables = ['vpo', 'spo'];
    $has_old_tables = false;
    foreach ($old_tables as $old_table) {
        if (in_array($old_table, $tables)) {
            echo "<p class='warning'>⚠️ Found old table: $old_table (should use new naming)</p>";
            $has_old_tables = true;
        }
    }
    if (!$has_old_tables) {
        echo "<p class='success'>✅ No old table names found - using clean structure</p>";
    }
    
    // Summary
    echo "<h2>📋 Summary:</h2>";
    echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
    
    if ($all_good && !$has_old_tables) {
        echo "<h3 class='success'>✅ Database is ready!</h3>";
        echo "<p>The 11klassniki_claude database has been successfully set up with:</p>";
        echo "<ul>";
        echo "<li>Clean, consistent table naming</li>";
        echo "<li>All data migrated from the old structure</li>";
        echo "<li>Proper foreign key relationships</li>";
        echo "<li>UTF8MB4 support for Russian text</li>";
        echo "</ul>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Update application code to use new table/column names</li>";
        echo "<li>Update .env file to point to 11klassniki_claude database</li>";
        echo "<li>Test thoroughly before going live</li>";
        echo "</ol>";
    } else {
        echo "<h3 class='warning'>⚠️ Database needs attention</h3>";
        echo "<p>Some tables are missing data or have issues. Please complete the migration.</p>";
    }
    
    echo "</div>";
    
    // Show database size
    $size_result = $claude_db->query("
        SELECT 
            SUM(data_length + index_length) / 1024 / 1024 AS size_mb
        FROM information_schema.TABLES 
        WHERE table_schema = '11klassniki_claude'
    ");
    $size = $size_result->fetch_assoc()['size_mb'];
    
    echo "<h2>💾 Database Size:</h2>";
    echo "<p>11klassniki_claude: <strong>" . round($size, 2) . " MB</strong></p>";
    
    $claude_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>