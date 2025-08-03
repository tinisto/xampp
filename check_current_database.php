<?php
// Comprehensive database analysis
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Analysis Report</h1>";

// 1. Check environment configuration
echo "<h2>1. Environment Configuration</h2>";
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
echo "<p><strong>DB_HOST:</strong> " . DB_HOST . "</p>";
echo "<p><strong>DB_NAME:</strong> " . DB_NAME . "</p>";
echo "<p><strong>DB_USER:</strong> " . DB_USER . "</p>";

// 2. Check actual connection
echo "<h2>2. Actual Database Connection</h2>";
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
$db_name_query = $connection->query("SELECT DATABASE() as db_name");
$current_db = $db_name_query->fetch_assoc()['db_name'];
echo "<p><strong>Currently connected to:</strong> <span style='color: green; font-weight: bold;'>$current_db</span></p>";

// 3. Check force flag status
echo "<h2>3. Force Flag Status</h2>";
$force_flag_file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php');
if (strpos($force_flag_file, '$force_new_db = true;') !== false) {
    echo "<p style='color: orange;'>⚠️ <strong>Force flag is ENABLED</strong> - Database is hardcoded, not using .env</p>";
} else {
    echo "<p style='color: green;'>✅ <strong>Force flag is DISABLED</strong> - Using .env configuration</p>";
}

// 4. Check table structure
echo "<h2>4. Table Structure in Current Database ($current_db)</h2>";
$tables = ['vpo', 'spo', 'universities', 'colleges', 'schools', 'news', 'posts', 'regions'];
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Table</th><th>Exists</th><th>Row Count</th><th>Status</th></tr>";

foreach ($tables as $table) {
    $check = $connection->query("SHOW TABLES LIKE '$table'");
    $exists = $check->num_rows > 0;
    $count = 0;
    $status = '';
    
    if ($exists) {
        $count_result = $connection->query("SELECT COUNT(*) as count FROM $table");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
        }
        
        // Determine status
        if (in_array($table, ['vpo', 'spo']) && $current_db == '11klassniki_claude') {
            $status = '<span style="color: orange;">Legacy table in new DB</span>';
        } elseif (in_array($table, ['universities', 'colleges']) && $current_db == '11klassniki_ru') {
            $status = '<span style="color: orange;">New table in old DB</span>';
        } else {
            $status = '<span style="color: green;">OK</span>';
        }
    }
    
    echo "<tr>";
    echo "<td>$table</td>";
    echo "<td>" . ($exists ? '✅ Yes' : '❌ No') . "</td>";
    echo "<td>" . ($exists ? $count : '-') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";

// 5. Check for data consistency issues
echo "<h2>5. Data Consistency Check</h2>";

// Check VPO vs Universities
if ($current_db == '11klassniki_claude') {
    $vpo_count = $connection->query("SELECT COUNT(*) as count FROM vpo")->fetch_assoc()['count'];
    $uni_count = $connection->query("SELECT COUNT(*) as count FROM universities")->fetch_assoc()['count'];
    
    if ($vpo_count > $uni_count) {
        echo "<p style='color: orange;'>⚠️ VPO table has more records ($vpo_count) than Universities table ($uni_count)</p>";
    } else {
        echo "<p style='color: green;'>✅ VPO and Universities tables are in sync</p>";
    }
    
    // Check SPO vs Colleges
    $spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
    $col_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
    
    if ($spo_count > $col_count) {
        echo "<p style='color: orange;'>⚠️ SPO table has more records ($spo_count) than Colleges table ($col_count)</p>";
    } else {
        echo "<p style='color: green;'>✅ SPO and Colleges tables are in sync</p>";
    }
}

// 6. Check which tables the application is actually using
echo "<h2>6. Application Table Usage</h2>";
echo "<p>Based on the code analysis:</p>";
echo "<ul>";
echo "<li>Educational institutions pages are using: <strong>vpo, spo, schools</strong> tables</li>";
echo "<li>This is correct for database: <strong>11klassniki_ru</strong></li>";
echo "<li>For <strong>11klassniki_claude</strong>, should use: <strong>universities, colleges, schools</strong></li>";
echo "</ul>";

// 7. Recommendations
echo "<h2>7. Recommendations</h2>";
if ($current_db == '11klassniki_claude') {
    echo "<div style='border: 2px solid orange; padding: 10px; margin: 10px 0;'>";
    echo "<h3>⚠️ Action Required</h3>";
    echo "<p>You are using the new database (<strong>11klassniki_claude</strong>) but:</p>";
    echo "<ol>";
    echo "<li>The application code is still querying old table names (vpo, spo)</li>";
    echo "<li>The new tables (universities, colleges) have fewer records</li>";
    echo "<li>Database connection is hardcoded, not using .env</li>";
    echo "</ol>";
    echo "<p><strong>DO NOT DELETE THE OLD DATABASE YET!</strong></p>";
    echo "<p>First, you need to:</p>";
    echo "<ol>";
    echo "<li>Complete data migration from vpo→universities and spo→colleges</li>";
    echo "<li>Update all application code to use new table names</li>";
    echo "<li>Update .env file and remove the force flag</li>";
    echo "<li>Test thoroughly before deleting old database</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<p>Currently using the old database structure.</p>";
}

echo "<hr>";
echo "<p><a href='/'>← Back to Home</a></p>";
?>