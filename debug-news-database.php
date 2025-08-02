<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>News Database Debug</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
if (isset($connection)) {
    echo "✅ Database connection exists<br>";
    echo "Connection status: " . ($connection->ping() ? "Active" : "Failed") . "<br>";
} else {
    echo "❌ Database connection not found<br>";
    die("No database connection available");
}

// Test news table existence
echo "<h2>News Table Test</h2>";
$tableCheck = $connection->query("SHOW TABLES LIKE 'news'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "✅ News table exists<br>";
} else {
    echo "❌ News table does not exist<br>";
}

// Count total news
echo "<h2>News Count Test</h2>";
$countQuery = "SELECT COUNT(*) as total FROM news";
$countResult = $connection->query($countQuery);
if ($countResult) {
    $totalNews = $countResult->fetch_assoc()['total'];
    echo "Total news in database: <strong>$totalNews</strong><br>";
} else {
    echo "❌ Error counting news: " . $connection->error . "<br>";
}

// Count approved news
$approvedQuery = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$approvedResult = $connection->query($approvedQuery);
if ($approvedResult) {
    $approvedNews = $approvedResult->fetch_assoc()['total'];
    echo "Approved news: <strong>$approvedNews</strong><br>";
} else {
    echo "❌ Error counting approved news: " . $connection->error . "<br>";
}

// Show sample news
echo "<h2>Sample News Data</h2>";
$sampleQuery = "SELECT id_news, title_news, approved, date_news FROM news ORDER BY date_news DESC LIMIT 5";
$sampleResult = $connection->query($sampleQuery);
if ($sampleResult && $sampleResult->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Approved</th><th>Date</th></tr>";
    while ($row = $sampleResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id_news'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . ($row['approved'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No news found or error: " . $connection->error . "<br>";
}

// Test the exact query from news.php
echo "<h2>Exact Query Test</h2>";
$newsQuery = "SELECT n.* FROM news n WHERE n.approved = 1 ORDER BY n.date_news DESC LIMIT 12 OFFSET 0";
echo "Query: <code>$newsQuery</code><br>";
$newsResult = $connection->query($newsQuery);
if ($newsResult) {
    echo "Query executed successfully<br>";
    echo "Results found: <strong>" . $newsResult->num_rows . "</strong><br>";
    if ($newsResult->num_rows > 0) {
        echo "<h3>First result:</h3>";
        $firstNews = $newsResult->fetch_assoc();
        echo "<pre>";
        print_r($firstNews);
        echo "</pre>";
    }
} else {
    echo "❌ Query failed: " . $connection->error . "<br>";
}

// Show environment info
echo "<h2>Environment Info</h2>";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'Not set') . "<br>";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "<br>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "<br>";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'Not defined') . "<br>";
?>