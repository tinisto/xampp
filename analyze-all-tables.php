<?php
// Comprehensive database table analysis
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Complete Database Table Analysis</h1>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    // Get all tables
    $tables_result = $connection->query("SHOW TABLES");
    if (!$tables_result) {
        echo "<p>❌ Error getting tables: " . $connection->error . "</p>";
        exit;
    }
    
    $issues = [];
    $table_count = 0;
    
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        .table-info { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .issue { background: #fee; color: #c00; padding: 5px; margin: 5px 0; }
        .good { background: #efe; color: #080; padding: 5px; margin: 5px 0; }
        .warning { background: #ffe; color: #880; padding: 5px; margin: 5px 0; }
        .summary { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .recommendation { background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0; }
    </style>";
    
    echo "<div class='container'>";
    
    while ($table_row = $tables_result->fetch_array()) {
        $table_name = $table_row[0];
        $table_count++;
        
        echo "<div class='table-info'>";
        echo "<h2>📊 Table: $table_name</h2>";
        
        // Get columns for this table
        $columns_result = $connection->query("SHOW COLUMNS FROM `$table_name`");
        if ($columns_result) {
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th><th>Issues</th></tr>";
            
            $primary_key = null;
            $foreign_keys = [];
            $url_fields = [];
            
            while ($col = $columns_result->fetch_assoc()) {
                $field = $col['Field'];
                $key = $col['Key'];
                $field_issues = [];
                
                echo "<tr>";
                echo "<td>$field</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "<td>{$col['Default']}</td>";
                echo "<td>{$col['Extra']}</td>";
                
                // Check primary key naming
                if ($key === 'PRI') {
                    $primary_key = $field;
                    if ($field !== 'id') {
                        $field_issues[] = "Primary key should be 'id', not '$field'";
                        $issues[$table_name][] = "Primary key naming: '$field' should be 'id'";
                    }
                }
                
                // Check foreign key naming patterns
                if (preg_match('/^id_(.+)$/', $field, $matches)) {
                    $field_issues[] = "Foreign key uses old pattern 'id_$matches[1]' (should be '{$matches[1]}_id')";
                    $issues[$table_name][] = "Foreign key pattern: '$field' should be '{$matches[1]}_id'";
                    $foreign_keys[] = $field;
                } elseif (preg_match('/^(.+)_id$/', $field)) {
                    // Good pattern
                    $foreign_keys[] = $field;
                }
                
                // Check URL field naming
                if (preg_match('/^url_(.+)$/', $field) && $field !== 'url_slug') {
                    $field_issues[] = "URL field should be 'url_slug', not '$field'";
                    $issues[$table_name][] = "URL field naming: '$field' should be 'url_slug'";
                    $url_fields[] = $field;
                } elseif ($field === 'url_slug') {
                    $url_fields[] = $field;
                }
                
                // Check for other naming inconsistencies
                if (strpos($field, 'name_') !== false && !in_array($field, ['name_en', 'region_name_en', 'town_name_en'])) {
                    $field_issues[] = "Consider standardizing name fields";
                }
                
                echo "<td>";
                if (empty($field_issues)) {
                    echo "<span class='good'>✓</span>";
                } else {
                    foreach ($field_issues as $issue) {
                        echo "<span class='issue'>$issue</span><br>";
                    }
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Table summary
            echo "<div class='summary'>";
            echo "<strong>Table Summary:</strong><br>";
            echo "Primary Key: " . ($primary_key ?: "None") . "<br>";
            echo "Foreign Keys: " . (empty($foreign_keys) ? "None" : implode(", ", $foreign_keys)) . "<br>";
            echo "URL Fields: " . (empty($url_fields) ? "None" : implode(", ", $url_fields)) . "<br>";
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    // Overall summary
    echo "<div class='table-info'>";
    echo "<h2>🔍 Overall Database Analysis</h2>";
    echo "<p>Total tables analyzed: $table_count</p>";
    
    if (!empty($issues)) {
        echo "<h3>Issues Found by Table:</h3>";
        foreach ($issues as $table => $table_issues) {
            echo "<h4>$table:</h4>";
            echo "<ul>";
            foreach ($table_issues as $issue) {
                echo "<li class='warning'>$issue</li>";
            }
            echo "</ul>";
        }
        
        echo "<div class='recommendation'>";
        echo "<h3>📋 Recommendations:</h3>";
        echo "<ol>";
        echo "<li><strong>Primary Keys:</strong> Standardize all primary keys to use 'id'</li>";
        echo "<li><strong>Foreign Keys:</strong> Use pattern 'table_id' (e.g., region_id, user_id)</li>";
        echo "<li><strong>URL Fields:</strong> Standardize all URL fields to 'url_slug'</li>";
        echo "<li><strong>Naming Convention:</strong> Use snake_case consistently</li>";
        echo "<li><strong>Migration Strategy:</strong> Create a comprehensive migration plan</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<p class='good'>✅ No major naming inconsistencies found!</p>";
    }
    
    // Show tables that follow best practices
    echo "<h3>Tables Following Best Practices:</h3>";
    echo "<ul>";
    foreach ($issues as $table => $table_issues) {
        if (empty($table_issues)) {
            echo "<li class='good'>✓ $table</li>";
        }
    }
    echo "</ul>";
    
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>