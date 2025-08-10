<?php
// Direct database connection test for production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct connection to iPage database
$host = '11klassnikiru67871.ipagemysql.com';
$user = 'admin_claude';
$pass = 'W4eZ!#9uwLmrMay';
$db = '11klassniki_claude';

echo "<h2>Production Database Connection Test</h2>";

try {
    $connection = new mysqli($host, $user, $pass, $db);
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<p style='color: green;'>✓ Connected to database: $db</p>";
    
    // Check tables
    echo "<h3>All tables in database:</h3>";
    $tables = $connection->query("SHOW TABLES");
    echo "<ul>";
    while ($row = $tables->fetch_array()) {
        $tableName = $row[0];
        $count = $connection->query("SELECT COUNT(*) FROM `$tableName`")->fetch_row()[0];
        echo "<li><strong>$tableName</strong> - $count records</li>";
    }
    echo "</ul>";
    
    // Check news table specifically
    echo "<h3>News Table Analysis:</h3>";
    $newsExists = $connection->query("SHOW TABLES LIKE 'news'")->num_rows > 0;
    
    if ($newsExists) {
        echo "<p style='color: green;'>✓ News table exists</p>";
        
        // Get structure
        echo "<h4>News table structure:</h4>";
        $cols = $connection->query("SHOW COLUMNS FROM news");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($col = $cols->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Count records
        $count = $connection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
        echo "<p>Total records in news table: <strong>$count</strong></p>";
        
        // Sample data
        if ($count > 0) {
            echo "<h4>Sample news records:</h4>";
            $sample = $connection->query("SELECT * FROM news LIMIT 5");
            while ($row = $sample->fetch_assoc()) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
                echo "<strong>ID:</strong> {$row['id']}<br>";
                echo "<strong>Title:</strong> " . htmlspecialchars($row['title_news'] ?? 'N/A') . "<br>";
                echo "<strong>Category:</strong> {$row['category_news'] ?? 'N/A'}<br>";
                echo "<strong>Date:</strong> {$row['date_news'] ?? 'N/A'}<br>";
                echo "</div>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ News table does not exist!</p>";
        
        // Check for posts table as alternative
        $postsExists = $connection->query("SHOW TABLES LIKE 'posts'")->num_rows > 0;
        if ($postsExists) {
            echo "<p style='color: orange;'>Found 'posts' table instead</p>";
            $count = $connection->query("SELECT COUNT(*) FROM posts")->fetch_row()[0];
            echo "<p>Posts table has $count records</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>