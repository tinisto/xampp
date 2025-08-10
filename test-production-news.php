<?php
// Test production database news query
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Production News Query Test</h2>";

// Check connection
if (!isset($connection)) {
    die("ERROR: Database connection not established!");
}

echo "<p>Connected to database: <strong>" . $connection->query("SELECT DATABASE()")->fetch_row()[0] . "</strong></p>";

// First, let's see what tables exist with 'news' or 'post' in the name
echo "<h3>Tables containing 'news' or 'post':</h3>";
$tables = $connection->query("SHOW TABLES");
$foundTables = [];
while ($row = $tables->fetch_array()) {
    $tableName = $row[0];
    if (stripos($tableName, 'news') !== false || stripos($tableName, 'post') !== false) {
        $foundTables[] = $tableName;
    }
}

if (empty($foundTables)) {
    echo "<p style='color: red;'>No tables found containing 'news' or 'post'!</p>";
    echo "<h4>All available tables:</h4><ul>";
    $tables = $connection->query("SHOW TABLES");
    while ($row = $tables->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    foreach ($foundTables as $table) {
        echo "<h4>Table: $table</h4>";
        
        // Get count
        $count = $connection->query("SELECT COUNT(*) FROM `$table`")->fetch_row()[0];
        echo "<p>Record count: <strong>$count</strong></p>";
        
        // Get columns
        $cols = $connection->query("SHOW COLUMNS FROM `$table`");
        $columns = [];
        while ($col = $cols->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        echo "<p>Columns: " . implode(', ', $columns) . "</p>";
        
        // If it looks like a news/posts table, show sample
        if ($count > 0 && (stripos($table, 'news') !== false || stripos($table, 'post') !== false)) {
            echo "<p>Sample record:</p>";
            $sample = $connection->query("SELECT * FROM `$table` LIMIT 1")->fetch_assoc();
            echo "<pre>" . print_r($sample, true) . "</pre>";
        }
    }
}

// Test the exact query from the news page
echo "<h3>Testing news page query:</h3>";
$query = "SELECT COUNT(*) as total FROM news WHERE 1=1";
$result = $connection->query($query);
if ($result) {
    $count = $result->fetch_assoc()['total'];
    echo "<p style='color: green;'>✓ Query successful: $count records found</p>";
} else {
    echo "<p style='color: red;'>✗ Query failed: " . $connection->error . "</p>";
    
    // Try alternative table names
    $alternatives = ['posts', 'post', 'content', 'articles'];
    foreach ($alternatives as $alt) {
        $testQuery = "SELECT COUNT(*) FROM `$alt`";
        $testResult = @$connection->query($testQuery);
        if ($testResult) {
            $count = $testResult->fetch_row()[0];
            echo "<p style='color: orange;'>Alternative found: Table '$alt' has $count records</p>";
        }
    }
}

// If news table exists but is empty, check if posts table has news data
$postsQuery = "SELECT * FROM posts WHERE category_post IN ('novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya') LIMIT 5";
$postsResult = @$connection->query($postsQuery);
if ($postsResult && $postsResult->num_rows > 0) {
    echo "<h3>Found news in posts table:</h3>";
    while ($row = $postsResult->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<strong>Title:</strong> " . htmlspecialchars($row['title_post'] ?? '') . "<br>";
        echo "<strong>Category:</strong> " . htmlspecialchars($row['category_post'] ?? '') . "<br>";
        echo "</div>";
    }
}
?>