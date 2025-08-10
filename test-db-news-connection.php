<?php
// Test database connection and check news table
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Prevent redirects
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test for News</h2>";

// Check if connection exists
if (!isset($connection)) {
    die("ERROR: Database connection variable not set!");
}

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "<p style='color: green;'>✓ Database connection successful!</p>";

// Check database name
$db_name = $connection->query("SELECT DATABASE()")->fetch_row()[0];
echo "<p>Connected to database: <strong>" . htmlspecialchars($db_name) . "</strong></p>";

// Check if news table exists
echo "<h3>Checking for news-related tables:</h3>";
$tables = $connection->query("SHOW TABLES");
$newsTablesFound = [];
if ($tables) {
    while ($row = $tables->fetch_array()) {
        $tableName = $row[0];
        if (stripos($tableName, 'news') !== false || stripos($tableName, 'post') !== false) {
            $newsTablesFound[] = $tableName;
        }
    }
}

if (empty($newsTablesFound)) {
    echo "<p style='color: red;'>No news/post related tables found!</p>";
    echo "<p>All tables in database:</p><ul>";
    $tables = $connection->query("SHOW TABLES");
    while ($row = $tables->fetch_array()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green;'>Found news/post related tables:</p><ul>";
    foreach ($newsTablesFound as $table) {
        echo "<li><strong>" . htmlspecialchars($table) . "</strong>";
        
        // Count records
        $count = $connection->query("SELECT COUNT(*) FROM `$table`")->fetch_row()[0];
        echo " - $count records";
        
        // Show structure
        echo "<br>Columns: ";
        $cols = $connection->query("SHOW COLUMNS FROM `$table`");
        $colNames = [];
        while ($col = $cols->fetch_assoc()) {
            $colNames[] = $col['Field'];
        }
        echo implode(', ', $colNames);
        echo "</li>";
    }
    echo "</ul>";
}

// Try specific queries
echo "<h3>Testing specific queries:</h3>";

// Test news table query
$testQueries = [
    "SELECT COUNT(*) as total FROM news" => "Count from news table",
    "SELECT COUNT(*) as total FROM posts" => "Count from posts table",
    "SELECT COUNT(*) as total FROM post" => "Count from post table"
];

foreach ($testQueries as $query => $description) {
    echo "<p>Testing: $description<br>";
    $result = $connection->query($query);
    if ($result) {
        $count = $result->fetch_assoc()['total'];
        echo "<span style='color: green;'>✓ Success: $count records found</span></p>";
    } else {
        echo "<span style='color: red;'>✗ Error: " . $connection->error . "</span></p>";
    }
}

// Check specific news query from the page
echo "<h3>Testing exact query from news page:</h3>";
$query = "SELECT id, title_news, url_slug, image_news, date_news, category_news 
          FROM news 
          WHERE 1=1
          ORDER BY date_news DESC 
          LIMIT 10";
          
echo "<pre>" . htmlspecialchars($query) . "</pre>";
$result = $connection->query($query);
if ($result) {
    echo "<p style='color: green;'>✓ Query executed successfully</p>";
    if ($result->num_rows > 0) {
        echo "<p>Found " . $result->num_rows . " records:</p>";
        while ($row = $result->fetch_assoc()) {
            echo "<p>- " . htmlspecialchars($row['title_news'] ?? 'No title') . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>Query successful but returned 0 rows</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Query failed: " . $connection->error . "</p>";
}

// Close connection
$connection->close();
?>