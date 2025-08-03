<?php
/**
 * Database Comparison Tool
 * Compares old and new database structures and data
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>📊 Database Comparison Tool</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .different { background-color: #ffe6e6; }
    .same { background-color: #e6ffe6; }
    .missing { background-color: #fff3cd; }
</style>";

// Database connections
$old_db_name = '11klassniki_ru';
$new_db_name = '11klassniki_new';
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';

try {
    // Connect to old database
    $old_db = $connection; // Uses existing connection
    
    // Connect to new database
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    
    // Set charset
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases</p>";

    // =====================================================
    // 1. Compare Tables
    // =====================================================
    echo "<h2>📋 Table Comparison</h2>";
    
    // Get tables from old database
    $old_tables = [];
    $result = $old_db->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $old_tables[] = $row[0];
    }
    
    // Get tables from new database
    $new_tables = [];
    $result = $new_db->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $new_tables[] = $row[0];
    }
    
    echo "<h3>Old Database Tables (" . count($old_tables) . "):</h3>";
    echo "<p>" . implode(", ", $old_tables) . "</p>";
    
    echo "<h3>New Database Tables (" . count($new_tables) . "):</h3>";
    echo "<p>" . implode(", ", $new_tables) . "</p>";
    
    // =====================================================
    // 2. Data Migration Status
    // =====================================================
    echo "<h2>🔄 Migration Status</h2>";
    echo "<table>";
    echo "<tr><th>Old Table</th><th>New Table</th><th>Old Count</th><th>New Count</th><th>Status</th></tr>";
    
    // Define mappings
    $table_mappings = [
        'regions' => 'regions',
        'areas' => 'areas',
        'towns' => 'towns',
        'categories' => 'categories',
        'users' => 'users',
        'vpo' => 'universities',
        'spo' => 'colleges',
        'schools' => 'schools',
        'news' => 'news',
        'posts' => 'posts',
        'comments' => 'comments'
    ];
    
    foreach ($table_mappings as $old_table => $new_table) {
        echo "<tr>";
        echo "<td><strong>$old_table</strong></td>";
        echo "<td><strong>$new_table</strong></td>";
        
        // Count old table
        $old_count = 0;
        if (in_array($old_table, $old_tables)) {
            $result = $old_db->query("SELECT COUNT(*) as count FROM `$old_table`");
            if ($result) {
                $old_count = $result->fetch_assoc()['count'];
            }
        }
        echo "<td>$old_count</td>";
        
        // Count new table
        $new_count = 0;
        if (in_array($new_table, $new_tables)) {
            $result = $new_db->query("SELECT COUNT(*) as count FROM `$new_table`");
            if ($result) {
                $new_count = $result->fetch_assoc()['count'];
            }
        }
        echo "<td>$new_count</td>";
        
        // Status
        if ($old_count == 0 && $new_count == 0) {
            echo "<td class='warning'>⚠️ Both empty</td>";
        } elseif ($new_count == 0) {
            echo "<td class='error'>❌ Not migrated</td>";
        } elseif ($old_count == $new_count) {
            echo "<td class='success'>✅ Fully migrated</td>";
        } elseif ($new_count < $old_count) {
            echo "<td class='warning'>⚠️ Partially migrated</td>";
        } else {
            echo "<td class='info'>ℹ️ Has more records</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
    
    // =====================================================
    // 3. Check for imported old tables in new database
    // =====================================================
    echo "<h2>🔍 Old Tables in New Database</h2>";
    $old_tables_in_new = ['vpo', 'spo', 'schools', 'news', 'posts'];
    echo "<table>";
    echo "<tr><th>Table</th><th>Exists in New DB?</th><th>Record Count</th></tr>";
    
    foreach ($old_tables_in_new as $table) {
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        
        if (in_array($table, $new_tables)) {
            echo "<td class='success'>✅ Yes</td>";
            $result = $new_db->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $result ? $result->fetch_assoc()['count'] : 0;
            echo "<td>$count records</td>";
        } else {
            echo "<td class='error'>❌ No</td>";
            echo "<td>-</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // =====================================================
    // 4. Schema Differences for Key Tables
    // =====================================================
    echo "<h2>📐 Schema Comparison (VPO → Universities)</h2>";
    
    if (in_array('vpo', $new_tables)) {
        echo "<h3>VPO Table Structure in New Database:</h3>";
        $result = $new_db->query("DESCRIBE vpo");
        echo "<table><tr><th>Column</th><th>Type</th></tr>";
        while ($col = $result->fetch_assoc()) {
            echo "<tr><td>" . $col['Field'] . "</td><td>" . $col['Type'] . "</td></tr>";
        }
        echo "</table>";
    }
    
    if (in_array('universities', $new_tables)) {
        echo "<h3>Universities Table Structure:</h3>";
        $result = $new_db->query("DESCRIBE universities");
        echo "<table><tr><th>Column</th><th>Type</th></tr>";
        while ($col = $result->fetch_assoc()) {
            echo "<tr><td>" . $col['Field'] . "</td><td>" . $col['Type'] . "</td></tr>";
        }
        echo "</table>";
    }
    
    // =====================================================
    // 5. Summary and Recommendations
    // =====================================================
    echo "<h2>📊 Summary</h2>";
    echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
    
    $need_migration = [];
    foreach ($table_mappings as $old => $new) {
        if (in_array($new, $new_tables)) {
            $result = $new_db->query("SELECT COUNT(*) as count FROM `$new`");
            $count = $result ? $result->fetch_assoc()['count'] : 0;
            if ($count == 0) {
                $need_migration[] = "$old → $new";
            }
        }
    }
    
    if (count($need_migration) > 0) {
        echo "<h3 class='error'>❌ Tables needing migration:</h3>";
        echo "<ul>";
        foreach ($need_migration as $migration) {
            echo "<li>$migration</li>";
        }
        echo "</ul>";
    } else {
        echo "<h3 class='success'>✅ All tables have been migrated!</h3>";
    }
    
    // Check if old tables exist in new DB
    $imported_old_tables = [];
    foreach (['vpo', 'spo', 'schools', 'news', 'posts'] as $table) {
        if (in_array($table, $new_tables)) {
            $imported_old_tables[] = $table;
        }
    }
    
    if (count($imported_old_tables) > 0) {
        echo "<h3 class='info'>ℹ️ Old tables found in new database:</h3>";
        echo "<p>These can be used for migration:</p>";
        echo "<ul>";
        foreach ($imported_old_tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>