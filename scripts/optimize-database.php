<?php
// Set document root for CLI
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

require_once dirname(__DIR__) . '/database/db_connections.php';
require_once dirname(__DIR__) . '/includes/Database.php';

$db = new Database($connection);

echo "Database Optimization Script\n";
echo "===========================\n\n";

// Get all tables
$tables = $db->queryAll("SHOW TABLES");
$dbName = $db->queryOne("SELECT DATABASE()")['DATABASE()'];

foreach ($tables as $table) {
    $tableName = $table["Tables_in_$dbName"];
    echo "Analyzing table: $tableName\n";
    
    // Get table structure
    $columns = $db->queryAll("SHOW COLUMNS FROM `$tableName`");
    
    // Get existing indexes
    $indexes = $db->queryAll("SHOW INDEX FROM `$tableName`");
    
    // Analyze missing indexes
    $recommendations = [];
    
    // Check for foreign key columns without indexes
    foreach ($columns as $column) {
        $columnName = $column['Field'];
        
        // Check if it's likely a foreign key (ends with _id)
        if (preg_match('/_id$/', $columnName) || preg_match('/^id_/', $columnName)) {
            $hasIndex = false;
            foreach ($indexes as $index) {
                if ($index['Column_name'] == $columnName) {
                    $hasIndex = true;
                    break;
                }
            }
            
            if (!$hasIndex) {
                $recommendations[] = "CREATE INDEX idx_{$tableName}_{$columnName} ON `$tableName` (`$columnName`);";
            }
        }
    }
    
    // Analyze slow queries (would need query log in real scenario)
    // For now, we'll add indexes for common search fields
    $searchableFields = ['name', 'title', 'email', 'url', 'status'];
    
    foreach ($columns as $column) {
        $columnName = $column['Field'];
        $columnType = $column['Type'];
        
        foreach ($searchableFields as $searchField) {
            if (stripos($columnName, $searchField) !== false && 
                (stripos($columnType, 'varchar') !== false || stripos($columnType, 'text') !== false)) {
                
                $hasIndex = false;
                foreach ($indexes as $index) {
                    if ($index['Column_name'] == $columnName) {
                        $hasIndex = true;
                        break;
                    }
                }
                
                if (!$hasIndex) {
                    $recommendations[] = "CREATE INDEX idx_{$tableName}_{$columnName} ON `$tableName` (`$columnName`(50));";
                }
            }
        }
    }
    
    if (!empty($recommendations)) {
        echo "  Recommendations:\n";
        foreach ($recommendations as $rec) {
            echo "    $rec\n";
        }
    } else {
        echo "  No index recommendations.\n";
    }
    
    // Optimize table
    $db->execute("OPTIMIZE TABLE `$tableName`");
    echo "  Table optimized.\n";
    
    echo "\n";
}

// Additional optimizations
echo "General Recommendations:\n";
echo "=======================\n";

// Check for missing primary keys
$tablesWithoutPK = [];
foreach ($tables as $table) {
    $tableName = $table["Tables_in_$dbName"];
    $hasPK = false;
    
    $indexes = $db->queryAll("SHOW INDEX FROM `$tableName` WHERE Key_name = 'PRIMARY'");
    if (empty($indexes)) {
        $tablesWithoutPK[] = $tableName;
    }
}

if (!empty($tablesWithoutPK)) {
    echo "- Tables without primary keys: " . implode(', ', $tablesWithoutPK) . "\n";
    echo "  Consider adding primary keys to these tables.\n";
}

// Check MyISAM vs InnoDB
$myisamTables = [];
foreach ($tables as $table) {
    $tableName = $table["Tables_in_$dbName"];
    $status = $db->queryOne("SHOW TABLE STATUS WHERE Name = '$tableName'");
    
    if ($status['Engine'] == 'MyISAM') {
        $myisamTables[] = $tableName;
    }
}

if (!empty($myisamTables)) {
    echo "\n- MyISAM tables found: " . implode(', ', $myisamTables) . "\n";
    echo "  Consider converting to InnoDB for better performance and features:\n";
    foreach ($myisamTables as $table) {
        echo "    ALTER TABLE `$table` ENGINE=InnoDB;\n";
    }
}

// Create indexes SQL file
$sqlFile = dirname(__DIR__) . '/scripts/recommended_indexes.sql';
$sqlContent = "-- Recommended indexes for $dbName\n";
$sqlContent .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";

foreach ($tables as $table) {
    $tableName = $table["Tables_in_$dbName"];
    $columns = $db->queryAll("SHOW COLUMNS FROM `$tableName`");
    $indexes = $db->queryAll("SHOW INDEX FROM `$tableName`");
    
    foreach ($columns as $column) {
        $columnName = $column['Field'];
        
        if (preg_match('/_id$/', $columnName) || preg_match('/^id_/', $columnName)) {
            $hasIndex = false;
            foreach ($indexes as $index) {
                if ($index['Column_name'] == $columnName) {
                    $hasIndex = true;
                    break;
                }
            }
            
            if (!$hasIndex) {
                $sqlContent .= "CREATE INDEX idx_{$tableName}_{$columnName} ON `$tableName` (`$columnName`);\n";
            }
        }
    }
}

file_put_contents($sqlFile, $sqlContent);
echo "\n- Index recommendations saved to: recommended_indexes.sql\n";

echo "\nOptimization complete!\n";