<?php
// Debug news categories
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug News Categories</h1>";

// Database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        
        $connection->set_charset("utf8mb4");
        echo "<p style='color: green;'>Database connected</p>";
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Check news table structure
echo "<h2>News Table Structure:</h2>";
$result = $connection->query("SHOW COLUMNS FROM news");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

// Check categories table
echo "<h2>Categories Table (first 10):</h2>";
$result = $connection->query("SELECT * FROM categories LIMIT 10");
if ($result) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id_category'] . "</td><td>" . $row['title_category'] . "</td><td>" . $row['url_category'] . "</td></tr>";
    }
    echo "</table>";
}

// Check news-category relationship
echo "<h2>Sample News with Categories:</h2>";
$result = $connection->query("
    SELECT n.id_news, n.title_news, n.category_news, c.title_category 
    FROM news n 
    LEFT JOIN categories c ON n.category_news = c.id_category 
    WHERE n.approved = 1 
    ORDER BY n.date_news DESC 
    LIMIT 10
");
if ($result) {
    echo "<table border='1'><tr><th>News ID</th><th>Title</th><th>Category ID</th><th>Category Title</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id_news'] . "</td><td>" . $row['title_news'] . "</td><td>" . $row['category_news'] . "</td><td>" . $row['title_category'] . "</td></tr>";
    }
    echo "</table>";
}

// Check if there's a news_categories table
echo "<h2>Checking for news_categories table:</h2>";
$result = $connection->query("SHOW TABLES LIKE 'news_categories'");
if ($result && $result->num_rows > 0) {
    echo "<p style='color: green;'>news_categories table exists</p>";
    
    // Show structure
    $result = $connection->query("SHOW COLUMNS FROM news_categories");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
        }
        echo "</table>";
    }
    
    // Show data
    $result = $connection->query("SELECT * FROM news_categories");
    if ($result) {
        echo "<h3>News Categories:</h3>";
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Slug</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>news_categories table does not exist</p>";
}

// Check news type fields
echo "<h2>News Type Fields:</h2>";
$result = $connection->query("
    SELECT id_news, title_news, 
           CASE 
               WHEN id_vpo > 0 THEN 'VPO'
               WHEN id_spo > 0 THEN 'SPO'
               WHEN id_school > 0 THEN 'School'
               ELSE 'General'
           END as news_type,
           id_vpo, id_spo, id_school
    FROM news 
    WHERE approved = 1 
    ORDER BY date_news DESC 
    LIMIT 10
");
if ($result) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Type</th><th>VPO ID</th><th>SPO ID</th><th>School ID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['id_news'] . "</td><td>" . $row['title_news'] . "</td><td>" . $row['news_type'] . "</td><td>" . $row['id_vpo'] . "</td><td>" . $row['id_spo'] . "</td><td>" . $row['id_school'] . "</td></tr>";
    }
    echo "</table>";
}

$connection->close();
?>