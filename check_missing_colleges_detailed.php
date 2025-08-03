<?php
/**
 * Check missing colleges in detail
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🔍 Detailed Missing Colleges Check</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
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
    
    // Count totals
    $old_spo_count = $old_db->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
    $new_colleges_count = $new_db->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
    
    echo "<h2>📊 Summary:</h2>";
    echo "<p>Old database SPO records: <strong>$old_spo_count</strong></p>";
    echo "<p>New database colleges records: <strong>$new_colleges_count</strong></p>";
    echo "<p>Missing: <strong>" . ($old_spo_count - $new_colleges_count) . "</strong></p>";
    
    // Check if we imported the spo table to new database
    $has_spo_in_new = $new_db->query("SHOW TABLES LIKE 'spo'")->num_rows > 0;
    
    if ($has_spo_in_new) {
        echo "<h2>✅ SPO table exists in new database</h2>";
        
        // Count SPO in new database
        $new_spo_count = $new_db->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
        echo "<p>SPO records in new database: <strong>$new_spo_count</strong></p>";
        
        // Find missing colleges
        echo "<h2>🔍 Finding missing colleges...</h2>";
        $missing_query = "
            SELECT COUNT(*) as count 
            FROM spo s
            WHERE s.id_spo NOT IN (SELECT id FROM colleges)
        ";
        
        $missing_result = $new_db->query($missing_query);
        $missing_count = $missing_result->fetch_assoc()['count'];
        
        echo "<p>Colleges not yet migrated from SPO table: <strong>$missing_count</strong></p>";
        
        if ($missing_count > 0) {
            // Show sample of missing
            echo "<h3>Sample of missing colleges:</h3>";
            $sample_query = "
                SELECT s.id_spo, s.spo_name, s.id_town, s.id_region
                FROM spo s
                WHERE s.id_spo NOT IN (SELECT id FROM colleges)
                LIMIT 10
            ";
            
            $sample_result = $new_db->query($sample_query);
            echo "<table>";
            echo "<tr><th>ID</th><th>College Name</th><th>Town ID</th><th>Region ID</th></tr>";
            while ($row = $sample_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id_spo']}</td>";
                echo "<td>{$row['spo_name']}</td>";
                echo "<td>{$row['id_town']}</td>";
                echo "<td>{$row['id_region']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h3>🔧 Ready to migrate missing colleges</h3>";
            echo "<p>Click the button below to migrate the remaining $missing_count colleges:</p>";
            echo "<form method='post' action='migrate_remaining_colleges.php'>";
            echo "<button type='submit' style='background: green; color: white; padding: 10px; border: none; cursor: pointer;'>Migrate Remaining Colleges</button>";
            echo "</form>";
        } else {
            echo "<p class='success'>✅ All colleges have been migrated!</p>";
        }
        
    } else {
        echo "<h2 class='error'>❌ SPO table not found in new database</h2>";
        echo "<p>The SPO table needs to be imported to the new database first.</p>";
    }
    
    // Check for potential issues
    echo "<h2>🔍 Checking for potential issues...</h2>";
    
    // Check for colleges with invalid foreign keys
    $invalid_fk_query = "
        SELECT COUNT(*) as count
        FROM colleges c
        WHERE c.town_id NOT IN (SELECT id FROM towns)
        OR c.area_id NOT IN (SELECT id FROM areas)
        OR c.region_id NOT IN (SELECT id FROM regions)
    ";
    
    $invalid_result = $new_db->query($invalid_fk_query);
    $invalid_count = $invalid_result->fetch_assoc()['count'];
    
    if ($invalid_count > 0) {
        echo "<p class='warning'>⚠️ Found $invalid_count colleges with invalid location references</p>";
    } else {
        echo "<p class='success'>✅ All colleges have valid location references</p>";
    }
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/fix_missing_data.php'>← Back to Fix Missing Data</a></p>";
?>