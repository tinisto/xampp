<?php
/**
 * Final Database Comparison - Check if all data is migrated
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>📊 Final Database Migration Check</h1>";
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
    .close { background-color: #fff3cd; }
</style>";

// Database connections
$old_db_name = '11klassniki_ru';
$new_db_name = '11klassniki_new';

try {
    // Connect to old database
    $old_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        '11klone_user',
        'K8HqqBV3hTf4mha',
        '11klassniki_ru'
    );
    
    if ($old_db->connect_error) {
        die("<p class='error'>❌ Could not connect to old database: " . $old_db->connect_error . "</p>");
    }
    
    // Connect to new database
    $new_db = new mysqli(
        DB_HOST,
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_new'
    );
    
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    
    // Set charset
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases</p>";

    // =====================================================
    // Detailed Comparison
    // =====================================================
    
    $comparisons = [
        ['old' => 'regions', 'new' => 'regions', 'key' => 'direct'],
        ['old' => 'areas', 'new' => 'areas', 'key' => 'direct'],
        ['old' => 'towns', 'new' => 'towns', 'key' => 'direct'],
        ['old' => 'categories', 'new' => 'categories', 'key' => 'direct'],
        ['old' => 'users', 'new' => 'users', 'key' => 'direct'],
        ['old' => 'vpo', 'new' => 'universities', 'key' => 'migration'],
        ['old' => 'spo', 'new' => 'colleges', 'key' => 'migration'],
        ['old' => 'schools', 'new' => 'schools', 'key' => 'direct'],
        ['old' => 'news', 'new' => 'news', 'key' => 'direct'],
        ['old' => 'posts', 'new' => 'posts', 'key' => 'direct'],
        ['old' => 'comments', 'new' => 'comments', 'key' => 'direct']
    ];
    
    echo "<h2>📋 Complete Data Comparison</h2>";
    echo "<table>";
    echo "<tr><th>Data Type</th><th>Old DB (Table)</th><th>Old Count</th><th>New DB (Table)</th><th>New Count</th><th>Difference</th><th>Status</th></tr>";
    
    $total_old = 0;
    $total_new = 0;
    $all_good = true;
    
    foreach ($comparisons as $comp) {
        echo "<tr>";
        
        // Data type
        $data_type = ucfirst($comp['old']);
        if ($comp['old'] == 'vpo') $data_type = 'Universities';
        if ($comp['old'] == 'spo') $data_type = 'Colleges';
        echo "<td><strong>$data_type</strong></td>";
        
        // Old table
        echo "<td>{$comp['old']}</td>";
        
        // Count old
        $old_count = 0;
        $old_result = $old_db->query("SELECT COUNT(*) as count FROM `{$comp['old']}`");
        if ($old_result) {
            $old_count = $old_result->fetch_assoc()['count'];
            $total_old += $old_count;
        }
        echo "<td>$old_count</td>";
        
        // New table
        echo "<td>{$comp['new']}</td>";
        
        // Count new
        $new_count = 0;
        $new_result = $new_db->query("SELECT COUNT(*) as count FROM `{$comp['new']}`");
        if ($new_result) {
            $new_count = $new_result->fetch_assoc()['count'];
            $total_new += $new_count;
        }
        echo "<td>$new_count</td>";
        
        // Difference
        $diff = $new_count - $old_count;
        if ($diff > 0) {
            echo "<td class='info'>+$diff</td>";
        } elseif ($diff < 0) {
            echo "<td class='error'>$diff</td>";
            $all_good = false;
        } else {
            echo "<td>0</td>";
        }
        
        // Status
        if ($old_count == $new_count) {
            echo "<td class='match'>✅ Perfect match</td>";
        } elseif ($new_count >= $old_count * 0.95) {
            echo "<td class='close'>⚠️ Close (95%+)</td>";
        } else {
            echo "<td class='mismatch'>❌ Mismatch</td>";
            $all_good = false;
        }
        
        echo "</tr>";
    }
    
    // Totals
    echo "<tr style='font-weight: bold;'>";
    echo "<td>TOTAL</td>";
    echo "<td>-</td>";
    echo "<td>$total_old</td>";
    echo "<td>-</td>";
    echo "<td>$total_new</td>";
    echo "<td>" . ($total_new - $total_old) . "</td>";
    echo "<td>" . ($all_good ? "✅ Ready" : "❌ Issues") . "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    // =====================================================
    // Check for orphaned records
    // =====================================================
    echo "<h2>🔍 Orphaned Records Check</h2>";
    
    // Check universities with invalid foreign keys
    $orphan_checks = [
        [
            'table' => 'universities',
            'checks' => [
                ['field' => 'town_id', 'ref_table' => 'towns', 'ref_field' => 'id'],
                ['field' => 'area_id', 'ref_table' => 'areas', 'ref_field' => 'id'],
                ['field' => 'region_id', 'ref_table' => 'regions', 'ref_field' => 'id']
            ]
        ],
        [
            'table' => 'colleges',
            'checks' => [
                ['field' => 'town_id', 'ref_table' => 'towns', 'ref_field' => 'id'],
                ['field' => 'area_id', 'ref_table' => 'areas', 'ref_field' => 'id'],
                ['field' => 'region_id', 'ref_table' => 'regions', 'ref_field' => 'id']
            ]
        ]
    ];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Check</th><th>Orphaned Records</th><th>Status</th></tr>";
    
    foreach ($orphan_checks as $check) {
        foreach ($check['checks'] as $fk_check) {
            echo "<tr>";
            echo "<td>{$check['table']}</td>";
            echo "<td>{$fk_check['field']} → {$fk_check['ref_table']}.{$fk_check['ref_field']}</td>";
            
            $query = "SELECT COUNT(*) as count FROM {$check['table']} t 
                     WHERE t.{$fk_check['field']} NOT IN 
                     (SELECT {$fk_check['ref_field']} FROM {$fk_check['ref_table']})
                     AND t.{$fk_check['field']} IS NOT NULL";
            
            $result = $new_db->query($query);
            $orphaned = $result ? $result->fetch_assoc()['count'] : 'Error';
            
            echo "<td>$orphaned</td>";
            echo "<td>" . ($orphaned == 0 ? "✅ Clean" : "⚠️ Has orphans") . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    // =====================================================
    // Summary
    // =====================================================
    echo "<h2>📊 Migration Summary</h2>";
    echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
    
    if ($all_good) {
        echo "<h3 class='success'>✅ Migration Successful!</h3>";
        echo "<p>All core data has been migrated to the new database structure.</p>";
        echo "<p><strong>Ready for:</strong></p>";
        echo "<ul>";
        echo "<li>Code updates to use new table/column names</li>";
        echo "<li>Testing with new database</li>";
        echo "<li>Production deployment</li>";
        echo "</ul>";
    } else {
        echo "<h3 class='warning'>⚠️ Migration Has Issues</h3>";
        echo "<p>Some tables have mismatched counts. Review the differences above.</p>";
    }
    
    echo "</div>";
    
    // =====================================================
    // Tables NOT migrated
    // =====================================================
    echo "<h2>📝 Tables NOT Migrated (Optional)</h2>";
    $optional_tables = [
        'comment_reports' => 'Comment moderation data',
        'indeks' => 'Postal codes',
        'messages' => 'Contact form messages',
        'news_categories' => 'News category relationships',
        'schools_verification' => 'School verification data',
        'search_queries' => 'Search history',
        'spo_verification' => 'SPO verification data',
        'test_email' => 'Test data',
        'vpo_verification' => 'VPO verification data'
    ];
    
    echo "<ul>";
    foreach ($optional_tables as $table => $desc) {
        $count_result = $old_db->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
        echo "<li><strong>$table</strong> - $desc ($count records)</li>";
    }
    echo "</ul>";
    echo "<p class='info'>These tables contain auxiliary data and can be migrated later if needed.</p>";
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>