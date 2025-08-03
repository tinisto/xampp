<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>News Debug Test</h1>";
echo "<pre>";

// Test 1: Check if we can include files
echo "Test 1: Including database connection...\n";
$db_paths = [
    '/includes/db_connect.php',
    '/database/db_connections.php',
    '/includes/db_connection.php',
    '/config/database.php'
];

$db_found = false;
foreach ($db_paths as $path) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
        require_once $_SERVER['DOCUMENT_ROOT'] . $path;
        echo "✓ Database file found and included: $path\n";
        $db_found = true;
        break;
    }
}

if (!$db_found) {
    echo "✗ Database file not found in any expected location\n";
}

// Test 2: Check database connection
echo "\nTest 2: Database connection...\n";
if (isset($connection)) {
    echo "✓ Connection object exists\n";
    
    // Test 3: Count news
    echo "\nTest 3: Counting news...\n";
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM news WHERE approved = 1");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "✓ Total approved news: " . $row['count'] . "\n";
    } else {
        echo "✗ Query failed: " . mysqli_error($connection) . "\n";
    }
    
    // Test 4: Get sample news
    echo "\nTest 4: Sample news data...\n";
    $result = mysqli_query($connection, "SELECT id_news, title_news, date_news, approved FROM news LIMIT 5");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: {$row['id_news']}, Title: {$row['title_news']}, Date: {$row['date_news']}, Approved: {$row['approved']}\n";
        }
    } else {
        echo "✗ Query failed: " . mysqli_error($connection) . "\n";
    }
    
    // Test 5: Check news categories
    echo "\nTest 5: News categories...\n";
    $result = mysqli_query($connection, "SELECT * FROM news_categories");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: {$row['id_category_news']}, URL: {$row['url_category_news']}, Title: {$row['title_category_news']}\n";
        }
    } else {
        echo "✗ Query failed: " . mysqli_error($connection) . "\n";
    }
    
} else {
    echo "✗ No database connection\n";
}

// Test 6: Check which news files exist
echo "\nTest 6: News files check...\n";
$files = [
    '/pages/news/news.php',
    '/pages/news/news-main.php',
    '/pages/news/news-main-working.php',
    '/pages/news/news-main-fixed.php',
    '/pages/category-news/category-news.php'
];

foreach ($files as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "✓ Found: $file\n";
    } else {
        echo "✗ Missing: $file\n";
    }
}

echo "</pre>";
?>