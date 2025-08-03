<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Include database connection if not already included
if (!isset($connection)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
}

echo "<!DOCTYPE html><html><head><title>News Debug</title></head><body>";
echo "<h1>News Debug Page</h1><pre>";

// 1. Test basic news query
echo "1. Testing basic news query:\n";
$query1 = "SELECT id_news, title_news, category_news, approved FROM news LIMIT 5";
$result1 = mysqli_query($connection, $query1);
if ($result1) {
    echo "✓ Query successful\n";
    while ($row = mysqli_fetch_assoc($result1)) {
        echo "  - ID: {$row['id_news']}, Category: '{$row['category_news']}', Approved: {$row['approved']}, Title: " . substr($row['title_news'], 0, 50) . "...\n";
    }
} else {
    echo "✗ Query failed: " . mysqli_error($connection) . "\n";
}

// 2. Check what values are in category_news field
echo "\n2. Checking category_news values:\n";
$query2 = "SELECT DISTINCT category_news, COUNT(*) as count FROM news GROUP BY category_news";
$result2 = mysqli_query($connection, $query2);
if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        echo "  - Category value: '{$row['category_news']}' - Count: {$row['count']}\n";
    }
}

// 3. Test JOIN query
echo "\n3. Testing JOIN query:\n";
$query3 = "SELECT n.id_news, n.title_news, n.category_news, nc.id_category_news, nc.title_category_news 
           FROM news n
           LEFT JOIN news_categories nc ON n.category_news = nc.id_category_news
           WHERE n.approved = 1 
           LIMIT 5";
$result3 = mysqli_query($connection, $query3);
if ($result3) {
    echo "✓ JOIN query successful\n";
    while ($row = mysqli_fetch_assoc($result3)) {
        echo "  - News category: '{$row['category_news']}' vs Category ID: '{$row['id_category_news']}' - Match: " . ($row['category_news'] == $row['id_category_news'] ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "✗ JOIN query failed: " . mysqli_error($connection) . "\n";
}

// 4. Test CAST JOIN query
echo "\n4. Testing CAST JOIN query:\n";
$query4 = "SELECT n.id_news, n.title_news, n.category_news, nc.id_category_news, nc.title_category_news 
           FROM news n
           LEFT JOIN news_categories nc ON n.category_news = CAST(nc.id_category_news AS CHAR)
           WHERE n.approved = 1 
           LIMIT 5";
$result4 = mysqli_query($connection, $query4);
if ($result4) {
    echo "✓ CAST JOIN query successful\n";
    $count = 0;
    while ($row = mysqli_fetch_assoc($result4)) {
        $count++;
        echo "  - ID: {$row['id_news']}, Category: {$row['title_category_news']}\n";
    }
    echo "  Total rows: $count\n";
} else {
    echo "✗ CAST JOIN query failed: " . mysqli_error($connection) . "\n";
}

// 5. Test direct comparison
echo "\n5. Testing direct string comparison:\n";
$query5 = "SELECT n.*, nc.title_category_news 
           FROM news n, news_categories nc 
           WHERE n.category_news = '1' AND nc.id_category_news = 1 
           LIMIT 2";
$result5 = mysqli_query($connection, $query5);
if ($result5) {
    echo "✓ Direct comparison successful - Found " . mysqli_num_rows($result5) . " rows\n";
}

echo "</pre></body></html>";
?>