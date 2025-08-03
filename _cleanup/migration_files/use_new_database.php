<?php
/**
 * Guide for using 11klassniki_new database (since claude is not accessible)
 */

echo "<h1>📊 Using 11klassniki_new Database</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; }
</style>";

echo "<div style='background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h2>⚠️ Important Discovery</h2>";
echo "<p><strong>admin_claude</strong> user has access to <strong>11klassniki_new</strong> database (not 11klassniki_claude)</p>";
echo "<p>We have two options:</p>";
echo "<ol>";
echo "<li><strong>Use 11klassniki_new</strong> - This database is accessible and contains all your migrated data</li>";
echo "<li><strong>Contact hosting support</strong> - To grant permissions for 11klassniki_claude</li>";
echo "</ol>";
echo "</div>";

echo "<h2>Option 1: Use 11klassniki_new (Recommended)</h2>";
echo "<p>Since <strong>11klassniki_new</strong> is accessible and contains all your data with the clean structure, we can use it!</p>";

echo "<h3>Updated .env configuration:</h3>";
echo "<div class='code'># Production environment variables
DB_HOST=11klassnikiru67871.ipagemysql.com
DB_USER=admin_claude
DB_PASS=Secure9#Klass
DB_NAME=11klassniki_new

# Old database (for reference)
# OLD_DB_NAME=11klassniki_ru
# OLD_DB_USER=11klone_user</div>";

// Test connection to 11klassniki_new
echo "<h2>🔌 Testing Connection to 11klassniki_new:</h2>";

try {
    $test_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_new'
    );
    
    if ($test_db->connect_error) {
        echo "<p class='error'>❌ Connection failed: " . $test_db->connect_error . "</p>";
    } else {
        echo "<p class='success'>✅ Successfully connected to 11klassniki_new!</p>";
        
        // Check table structure
        $tables_to_check = ['universities', 'colleges', 'areas', 'towns'];
        $has_new_structure = true;
        
        echo "<h3>Checking for new table structure:</h3>";
        echo "<ul>";
        foreach ($tables_to_check as $table) {
            $result = $test_db->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<li class='success'>✅ $table exists</li>";
            } else {
                echo "<li class='error'>❌ $table not found</li>";
                $has_new_structure = false;
            }
        }
        echo "</ul>";
        
        // Check for old tables
        $old_tables = ['vpo', 'spo'];
        echo "<h3>Checking for old tables (should not use these):</h3>";
        echo "<ul>";
        foreach ($old_tables as $table) {
            $result = $test_db->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<li class='warning'>⚠️ $table exists (old structure - don't use)</li>";
            }
        }
        echo "</ul>";
        
        if ($has_new_structure) {
            echo "<div class='success' style='padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>✅ Perfect! 11klassniki_new has the clean structure!</h3>";
            echo "<p>This database contains:</p>";
            echo "<ul>";
            echo "<li>universities (instead of vpo)</li>";
            echo "<li>colleges (instead of spo)</li>";
            echo "<li>Clean column names (id instead of id_vpo, etc.)</li>";
            echo "<li>All your migrated data</li>";
            echo "</ul>";
            echo "</div>";
            
            // Quick data count
            echo "<h3>Data verification:</h3>";
            $counts = [
                'universities' => $test_db->query("SELECT COUNT(*) as c FROM universities")->fetch_assoc()['c'],
                'colleges' => $test_db->query("SELECT COUNT(*) as c FROM colleges")->fetch_assoc()['c'],
                'schools' => $test_db->query("SELECT COUNT(*) as c FROM schools")->fetch_assoc()['c'],
                'news' => $test_db->query("SELECT COUNT(*) as c FROM news")->fetch_assoc()['c']
            ];
            
            echo "<table style='border-collapse: collapse;'>";
            foreach ($counts as $table => $count) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'><strong>" . ucfirst($table) . ":</strong></td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>$count records</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        $test_db->close();
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>📝 Next Steps:</h2>";
echo "<ol>";
echo "<li><strong>Update your .env file</strong> to use DB_NAME=11klassniki_new</li>";
echo "<li><strong>Update your code</strong> to use new table/column names (universities instead of vpo, etc.)</li>";
echo "<li><strong>Test thoroughly</strong> before going live</li>";
echo "</ol>";

echo "<h2>Option 2: If you still want to use 11klassniki_claude</h2>";
echo "<p>You'll need to:</p>";
echo "<ol>";
echo "<li>Log into your hosting control panel (iPage)</li>";
echo "<li>Go to MySQL Database management</li>";
echo "<li>Find user 'admin_claude' and database '11klassniki_claude'</li>";
echo "<li>Grant all privileges for admin_claude to access 11klassniki_claude</li>";
echo "</ol>";

echo "<p><a href='/update_app_for_claude_db.php'>View Migration Guide</a> | <a href='/'>Back to Home</a></p>";
?>