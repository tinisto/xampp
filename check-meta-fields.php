<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Database Meta Fields Analysis</h1>";
echo "<pre>";

// Tables to check
$tables = ['posts', 'news', 'vpo', 'spo', 'schools', 'categories', 'pages', 'comments'];

foreach ($tables as $table) {
    echo "\n<strong>Table: $table</strong>\n";
    
    // Check if table exists
    $tableExists = mysqli_query($connection, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($tableExists) == 0) {
        echo "  - Table does not exist\n";
        continue;
    }
    
    // Get columns
    $columns = mysqli_query($connection, "SHOW COLUMNS FROM $table");
    if ($columns) {
        $metaFields = [];
        while ($col = mysqli_fetch_assoc($columns)) {
            $colName = $col['Field'];
            // Look for meta fields
            if (stripos($colName, 'meta') !== false || 
                stripos($colName, 'description') !== false || 
                stripos($colName, 'keyword') !== false) {
                $metaFields[] = $colName . " (" . $col['Type'] . ")";
            }
        }
        
        if (empty($metaFields)) {
            echo "  - No meta fields found\n";
        } else {
            foreach ($metaFields as $field) {
                echo "  - $field\n";
            }
        }
    }
}

echo "</pre>";
?>