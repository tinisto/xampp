<?php
// Comprehensive database and table check
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection and Table Check</h1>";

// Step 1: Test direct connection first
echo "<h2>Step 1: Testing Direct Database Connection</h2>";

$host = '11klassnikiru67871.ipagemysql.com';
$user = 'admin_claude';
$pass = 'W4eZ!#9uwLmrMay';
$db = '11klassniki_claude';

try {
    $directConnection = new mysqli($host, $user, $pass, $db);
    
    if ($directConnection->connect_error) {
        echo "<p style='color: red;'>✗ Direct connection failed: " . $directConnection->connect_error . "</p>";
        die();
    }
    
    echo "<p style='color: green;'>✓ Direct connection successful!</p>";
    echo "<p>Connected to host: <strong>$host</strong></p>";
    echo "<p>Database: <strong>$db</strong></p>";
    echo "<p>User: <strong>$user</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Connection error: " . $e->getMessage() . "</p>";
    die();
}

// Step 2: Test connection via db_connections.php
echo "<h2>Step 2: Testing Connection via db_connections.php</h2>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (!isset($connection)) {
        echo "<p style='color: red;'>✗ \$connection variable not set by db_connections.php!</p>";
    } else {
        $dbName = $connection->query("SELECT DATABASE()")->fetch_row()[0];
        echo "<p style='color: green;'>✓ db_connections.php connection successful!</p>";
        echo "<p>Connected to database: <strong>$dbName</strong></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading db_connections.php: " . $e->getMessage() . "</p>";
}

// Step 3: Check all tables
echo "<h2>Step 3: All Tables in Database</h2>";

$tables = $directConnection->query("SHOW TABLES");
$tableList = [];
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Table Name</th><th>Record Count</th></tr>";

while ($row = $tables->fetch_array()) {
    $tableName = $row[0];
    $tableList[] = $tableName;
    $count = $directConnection->query("SELECT COUNT(*) FROM `$tableName`")->fetch_row()[0];
    echo "<tr>";
    echo "<td><strong>$tableName</strong></td>";
    echo "<td>$count records</td>";
    echo "</tr>";
}
echo "</table>";

// Step 4: Check specific tables
echo "<h2>Step 4: Checking Posts and News Tables</h2>";

// Check posts table
if (in_array('posts', $tableList)) {
    echo "<h3 style='color: green;'>✓ Posts Table Found</h3>";
    
    // Get structure
    $cols = $directConnection->query("SHOW COLUMNS FROM posts");
    $postColumns = [];
    while ($col = $cols->fetch_assoc()) {
        $postColumns[] = $col['Field'];
    }
    echo "<p><strong>Columns:</strong> " . implode(', ', $postColumns) . "</p>";
    
    // Check for news-related posts
    $newsCategories = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
    $categoryString = "'" . implode("','", $newsCategories) . "'";
    
    // Check if category_post column exists
    if (in_array('category_post', $postColumns)) {
        $newsInPosts = $directConnection->query("SELECT COUNT(*) FROM posts WHERE category_post IN ($categoryString)")->fetch_row()[0];
        echo "<p>News-related posts (by category_post): <strong>$newsInPosts</strong></p>";
        
        if ($newsInPosts > 0) {
            echo "<p>Sample news posts:</p>";
            $sample = $directConnection->query("SELECT id, title_post, category_post, date_post FROM posts WHERE category_post IN ($categoryString) LIMIT 3");
            while ($row = $sample->fetch_assoc()) {
                echo "<div style='margin-left: 20px; border-left: 3px solid #007bff; padding-left: 10px;'>";
                echo "ID: {$row['id']}, Title: " . htmlspecialchars($row['title_post']) . ", Category: {$row['category_post']}<br>";
                echo "</div>";
            }
        }
    } else {
        echo "<p style='color: orange;'>Note: category_post column not found in posts table</p>";
    }
    
    // Also check numeric categories
    $totalPosts = $directConnection->query("SELECT COUNT(*) FROM posts")->fetch_row()[0];
    echo "<p>Total posts in table: <strong>$totalPosts</strong></p>";
    
} else {
    echo "<h3 style='color: red;'>✗ Posts Table NOT Found</h3>";
}

// Check news table
if (in_array('news', $tableList)) {
    echo "<h3 style='color: green;'>✓ News Table Found</h3>";
    
    // Get structure
    $cols = $directConnection->query("SHOW COLUMNS FROM news");
    $newsColumns = [];
    while ($col = $cols->fetch_assoc()) {
        $newsColumns[] = $col['Field'];
    }
    echo "<p><strong>Columns:</strong> " . implode(', ', $newsColumns) . "</p>";
    
    // Count records
    $newsCount = $directConnection->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
    echo "<p>Total news records: <strong>$newsCount</strong></p>";
    
    if ($newsCount > 0) {
        // Show sample
        echo "<p>Sample news records:</p>";
        $sample = $directConnection->query("SELECT id, title_news, category_news, date_news FROM news LIMIT 3");
        while ($row = $sample->fetch_assoc()) {
            echo "<div style='margin-left: 20px; border-left: 3px solid #28a745; padding-left: 10px;'>";
            echo "ID: {$row['id']}, Title: " . htmlspecialchars($row['title_news'] ?? 'N/A') . ", Category: {$row['category_news']}<br>";
            echo "</div>";
        }
        
        // Check categories distribution
        echo "<p>News by category:</p>";
        $catDist = $directConnection->query("SELECT category_news, COUNT(*) as cnt FROM news GROUP BY category_news");
        while ($row = $catDist->fetch_assoc()) {
            echo "<div style='margin-left: 20px;'>Category '{$row['category_news']}': {$row['cnt']} items</div>";
        }
    }
} else {
    echo "<h3 style='color: red;'>✗ News Table NOT Found</h3>";
    echo "<p>The news table needs to be created!</p>";
}

// Step 5: Test the exact query from news page
echo "<h2>Step 5: Testing News Page Query</h2>";

$testQuery = "SELECT id, title_news, url_slug, image_news, date_news, category_news 
              FROM news 
              WHERE 1=1
              ORDER BY date_news DESC 
              LIMIT 5";

echo "<p>Query: <code>" . htmlspecialchars($testQuery) . "</code></p>";

$result = @$directConnection->query($testQuery);
if ($result) {
    echo "<p style='color: green;'>✓ Query executed successfully</p>";
    echo "<p>Found " . $result->num_rows . " records</p>";
} else {
    echo "<p style='color: red;'>✗ Query failed: " . $directConnection->error . "</p>";
}

// Close connection
$directConnection->close();

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<ul>";
echo "<li>Database connection: <strong style='color: green;'>Working</strong></li>";
echo "<li>Posts table: <strong style='color: " . (in_array('posts', $tableList) ? "green'>Exists" : "red'>Missing") . "</strong></li>";
echo "<li>News table: <strong style='color: " . (in_array('news', $tableList) ? "green'>Exists" : "red'>Missing") . "</strong></li>";
echo "</ul>";
?>