<?php
// Enhanced debug for news system
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Complete News System Debug</h2>";

// Check total news count
echo "<h3>1. Total news articles:</h3>";
$countQuery = "SELECT COUNT(*) as total FROM news";
$result = mysqli_query($connection, $countQuery);
$count = mysqli_fetch_assoc($result);
echo "<p><strong>Total articles: " . $count['total'] . "</strong></p>";

// Show recent news with URLs
echo "<h3>2. Recent news articles (last 10):</h3>";
$recentQuery = "SELECT id_news, title_news, url_news, created_at FROM news ORDER BY created_at DESC LIMIT 10";
$result = mysqli_query($connection, $recentQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Date</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $newsUrl = htmlspecialchars($row['url_news']);
        echo "<tr>";
        echo "<td>" . $row['id_news'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td><a href='/news/{$newsUrl}' target='_blank'>{$newsUrl}</a></td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No news articles found!</p>";
}

// Search for any RGGU-related articles
echo "<h3>3. Search for RGGU articles:</h3>";
$searchQuery = "SELECT id_news, title_news, url_news FROM news WHERE title_news LIKE '%ргу%' OR title_news LIKE '%РГPУ%' OR title_news LIKE '%rggu%' OR url_news LIKE '%rggu%' LIMIT 5";
$result = mysqli_query($connection, $searchQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_news'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_news']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No RGGU-related articles found</p>";
}

// Check table structure
echo "<h3>4. News table structure:</h3>";
$describeQuery = "DESCRIBE news";
$result = mysqli_query($connection, $describeQuery);
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if there are any status-related fields
echo "<h3>5. Status field values:</h3>";
$statusQuery = "SELECT DISTINCT status, COUNT(*) as count FROM news GROUP BY status";
$result = mysqli_query($connection, $statusQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Status</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . ($row['status'] ?: 'NULL') . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No status field or data</p>";
}

// Show URL patterns
echo "<h3>6. URL patterns in news:</h3>";
$urlQuery = "SELECT url_news FROM news WHERE url_news IS NOT NULL AND url_news != '' ORDER BY LENGTH(url_news) DESC LIMIT 5";
$result = mysqli_query($connection, $urlQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . htmlspecialchars($row['url_news']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No URL patterns found</p>";
}

mysqli_close($connection);
?>

<style>
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f0f0f0; }
</style>