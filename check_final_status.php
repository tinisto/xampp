<?php
/**
 * Check final migration status
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>📊 Final Migration Status Check</h1>";
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
    // Connect to both databases
    $old_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        '11klone_user',
        'K8HqqBV3hTf4mha',
        '11klassniki_ru'
    );
    
    $new_db = new mysqli(
        DB_HOST,
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_new'
    );
    
    if ($old_db->connect_error || $new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to databases</p>");
    }
    
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases</p>";
    
    // Detailed comparison
    $comparisons = [
        ['old' => 'regions', 'new' => 'regions', 'name' => 'Regions'],
        ['old' => 'areas', 'new' => 'areas', 'name' => 'Areas'],
        ['old' => 'towns', 'new' => 'towns', 'name' => 'Towns'],
        ['old' => 'categories', 'new' => 'categories', 'name' => 'Categories'],
        ['old' => 'users', 'new' => 'users', 'name' => 'Users'],
        ['old' => 'vpo', 'new' => 'universities', 'name' => 'Universities (VPO)'],
        ['old' => 'spo', 'new' => 'colleges', 'name' => 'Colleges (SPO)'],
        ['old' => 'schools', 'new' => 'schools', 'name' => 'Schools'],
        ['old' => 'news', 'new' => 'news', 'name' => 'News'],
        ['old' => 'posts', 'new' => 'posts', 'name' => 'Posts'],
        ['old' => 'comments', 'new' => 'comments', 'name' => 'Comments']
    ];
    
    echo "<table>";
    echo "<tr><th>Data Type</th><th>Old DB Count</th><th>New DB Count</th><th>Difference</th><th>Status</th><th>Action Needed</th></tr>";
    
    $all_good = true;
    $actions_needed = [];
    
    foreach ($comparisons as $comp) {
        echo "<tr>";
        echo "<td><strong>{$comp['name']}</strong></td>";
        
        // Count old
        $old_count = 0;
        $old_result = $old_db->query("SELECT COUNT(*) as count FROM `{$comp['old']}`");
        if ($old_result) {
            $old_count = $old_result->fetch_assoc()['count'];
        }
        echo "<td>$old_count</td>";
        
        // Count new
        $new_count = 0;
        $new_result = $new_db->query("SELECT COUNT(*) as count FROM `{$comp['new']}`");
        if ($new_result) {
            $new_count = $new_result->fetch_assoc()['count'];
        }
        echo "<td>$new_count</td>";
        
        // Difference
        $diff = $new_count - $old_count;
        $percent = $old_count > 0 ? round(($new_count / $old_count) * 100, 1) : 0;
        
        if ($diff > 0) {
            echo "<td class='info'>+$diff</td>";
        } elseif ($diff < 0) {
            echo "<td class='error'>$diff ({$percent}%)</td>";
        } else {
            echo "<td>0 (100%)</td>";
        }
        
        // Status
        if ($old_count == $new_count) {
            echo "<td class='match'>✅ Complete</td>";
            echo "<td>-</td>";
        } elseif ($percent >= 95) {
            echo "<td class='warning'>⚠️ Nearly Complete ({$percent}%)</td>";
            echo "<td>Optional: Add remaining " . abs($diff) . " records</td>";
        } else {
            echo "<td class='mismatch'>❌ Incomplete ({$percent}%)</td>";
            echo "<td><strong>Required: Add " . abs($diff) . " records</strong></td>";
            $all_good = false;
            $actions_needed[] = "{$comp['name']}: Add " . abs($diff) . " records";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check for the specific missing records
    if ($new_db->query("SELECT COUNT(*) as count FROM areas")->fetch_assoc()['count'] < 
        $old_db->query("SELECT COUNT(*) as count FROM areas")->fetch_assoc()['count']) {
        
        echo "<h2>🔍 Missing Areas Analysis</h2>";
        $missing_areas = $old_db->query("
            SELECT a.id_area, a.name, r.name as region_name
            FROM areas a
            LEFT JOIN regions r ON a.id_region = r.id_region
            WHERE a.id_area NOT IN (SELECT id FROM 11klassniki_new.areas)
            LIMIT 10
        ");
        
        echo "<p>Sample of missing areas:</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Area Name</th><th>Region</th></tr>";
        while ($row = $missing_areas->fetch_assoc()) {
            echo "<tr><td>{$row['id_area']}</td><td>{$row['name']}</td><td>{$row['region_name']}</td></tr>";
        }
        echo "</table>";
    }
    
    // Summary
    echo "<h2>📋 Migration Summary</h2>";
    echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
    
    if ($all_good) {
        echo "<h3 class='success'>✅ Migration Complete!</h3>";
        echo "<p>All essential data has been successfully migrated to the new database.</p>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Update application code to use new table/column names</li>";
        echo "<li>Test the site with the new database</li>";
        echo "<li>Switch production to use the new database</li>";
        echo "</ol>";
    } else {
        echo "<h3 class='warning'>⚠️ Migration Nearly Complete</h3>";
        echo "<p><strong>Actions still needed:</strong></p>";
        echo "<ul>";
        foreach ($actions_needed as $action) {
            echo "<li>$action</li>";
        }
        echo "</ul>";
        echo "<p><a href='/fix_missing_data.php' class='button' style='background: blue; color: white; padding: 10px; text-decoration: none;'>Fix Missing Data</a></p>";
    }
    
    echo "</div>";
    
    // Database size comparison
    echo "<h2>💾 Database Size Comparison</h2>";
    $old_size = $old_db->query("
        SELECT 
            SUM(data_length + index_length) / 1024 / 1024 AS size_mb
        FROM information_schema.TABLES 
        WHERE table_schema = '11klassniki_ru'
    ")->fetch_assoc()['size_mb'];
    
    $new_size = $new_db->query("
        SELECT 
            SUM(data_length + index_length) / 1024 / 1024 AS size_mb
        FROM information_schema.TABLES 
        WHERE table_schema = '11klassniki_new'
    ")->fetch_assoc()['size_mb'];
    
    echo "<p>Old database size: <strong>" . round($old_size, 2) . " MB</strong></p>";
    echo "<p>New database size: <strong>" . round($new_size, 2) . " MB</strong></p>";
    echo "<p>Size reduction: <strong>" . round((($old_size - $new_size) / $old_size) * 100, 1) . "%</strong></p>";
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a> | <a href='/fix_missing_data.php'>Fix Missing Data</a></p>";
?>