<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Security check - only allow for database analysis
if (!isset($_GET['analyze']) || $_GET['analyze'] !== 'yes') {
    echo "<h1>Database Structure Analysis</h1>";
    echo "<p>This script will analyze your current database structure for migration planning.</p>";
    echo "<p><a href='?analyze=yes' style='background: blue; color: white; padding: 10px; text-decoration: none;'>START ANALYSIS</a></p>";
    exit;
}

echo "<h1>Database Structure Analysis</h1>";
echo "<style>
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .error { color: red; }
    .success { color: green; }
    .warning { color: orange; }
    .section { margin: 30px 0; }
</style>";

// Check database connection
if (!isset($connection)) {
    die("<p class='error'>❌ Database connection not available</p>");
}

echo "<p class='success'>✅ Database connection active</p>";
echo "<p><strong>Database Host:</strong> " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "</p>";
echo "<p><strong>Database Name:</strong> " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</p>";

// Get all tables
echo "<div class='section'><h2>📋 All Tables in Database</h2>";
$tables_query = "SHOW TABLES";
$tables_result = mysqli_query($connection, $tables_query);

if ($tables_result) {
    echo "<table><tr><th>Table Name</th><th>Row Count</th><th>Status</th></tr>";
    
    $all_tables = [];
    while ($table_row = mysqli_fetch_array($tables_result)) {
        $table_name = $table_row[0];
        $all_tables[] = $table_name;
        
        // Count rows
        $count_query = "SELECT COUNT(*) as count FROM `$table_name`";
        $count_result = mysqli_query($connection, $count_query);
        $count = $count_result ? mysqli_fetch_assoc($count_result)['count'] : 'Error';
        
        // Check if table is used in code
        $status = 'Unknown';
        if (in_array($table_name, ['news', 'vpo', 'spo', 'schools', 'users', 'towns', 'regions', 'categories'])) {
            $status = "<span class='success'>✅ Core table</span>";
        }
        
        echo "<tr><td><strong>$table_name</strong></td><td>$count</td><td>$status</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ Could not fetch tables: " . mysqli_error($connection) . "</p>";
}

// Analyze critical tables structure
$critical_tables = ['towns', 'regions', 'vpo', 'spo', 'schools', 'news', 'users', 'categories'];

foreach ($critical_tables as $table) {
    if (in_array($table, $all_tables)) {
        echo "<div class='section'><h2>🔍 Table: $table</h2>";
        
        // Get table structure
        $structure_query = "DESCRIBE `$table`";
        $structure_result = mysqli_query($connection, $structure_query);
        
        if ($structure_result) {
            echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($col = mysqli_fetch_assoc($structure_result)) {
                echo "<tr>";
                echo "<td><strong>" . $col['Field'] . "</strong></td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . $col['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show sample data
            $sample_query = "SELECT * FROM `$table` LIMIT 3";
            $sample_result = mysqli_query($connection, $sample_query);
            
            if ($sample_result && mysqli_num_rows($sample_result) > 0) {
                echo "<h4>Sample Data:</h4>";
                echo "<table>";
                
                // Header
                $first_row = mysqli_fetch_assoc($sample_result);
                mysqli_data_seek($sample_result, 0);
                
                echo "<tr>";
                foreach (array_keys($first_row) as $col_name) {
                    echo "<th>$col_name</th>";
                }
                echo "</tr>";
                
                // Data rows
                $row_count = 0;
                while ($row = mysqli_fetch_assoc($sample_result) && $row_count < 3) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        $display_value = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
                        echo "<td>" . htmlspecialchars($display_value) . "</td>";
                    }
                    echo "</tr>";
                    $row_count++;
                }
                echo "</table>";
            }
        } else {
            echo "<p class='error'>❌ Could not analyze table $table: " . mysqli_error($connection) . "</p>";
        }
    } else {
        echo "<div class='section'><h2>⚠️  Missing Table: $table</h2>";
        echo "<p class='warning'>This table is expected by the code but doesn't exist in the database.</p></div>";
    }
}

// Check for the specific error we found
echo "<div class='section'><h2>🔧 Known Issues Analysis</h2>";

// Check towns table columns
if (in_array('towns', $all_tables)) {
    $towns_cols_query = "SHOW COLUMNS FROM towns";
    $towns_cols_result = mysqli_query($connection, $towns_cols_query);
    
    echo "<h4>Towns Table Column Check:</h4>";
    $has_town_name = false;
    $has_town_name_en = false;
    $town_columns = [];
    
    while ($col = mysqli_fetch_assoc($towns_cols_result)) {
        $col_name = $col['Field'];
        $town_columns[] = $col_name;
        
        if ($col_name === 'town_name') $has_town_name = true;
        if ($col_name === 'town_name_en') $has_town_name_en = true;
    }
    
    echo "<p><strong>All columns:</strong> " . implode(', ', $town_columns) . "</p>";
    echo "<p>Has 'town_name': " . ($has_town_name ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "</p>";
    echo "<p>Has 'town_name_en': " . ($has_town_name_en ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "</p>";
    
    if (!$has_town_name || !$has_town_name_en) {
        echo "<p class='warning'>⚠️  This explains the 'Unknown column town_name' error!</p>";
    }
}

echo "</div>";

echo "<div class='section'><h2>💡 Next Steps</h2>";
echo "<ul>";
echo "<li>Review the table structures above</li>";
echo "<li>Identify missing columns and inconsistencies</li>";
echo "<li>Plan the new clean database schema</li>";
echo "<li>Create migration script to preserve your data</li>";
echo "</ul>";
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>