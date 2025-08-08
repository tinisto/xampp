<?php
// Debug news listing vs single news mismatch
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>News Listing vs Single News Debug</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

echo "<h3>1. Check what the news LISTING query uses:</h3>";

// First, let's see what the news listing page (pages/common/news/news.php) actually queries
echo "<p>Let's check what query the news listing uses vs what individual articles use...</p>";

// Test the listing query (what shows articles on /news page)
echo "<h4>A) News LISTING query (what shows on /news page):</h4>";
$listingQuery = "SELECT id, title_news, url_slug, text_news, date_news, view_news, category_news, approved 
                 FROM news 
                 WHERE approved = 1 
                 ORDER BY date_news DESC 
                 LIMIT 10";

$result = mysqli_query($connection, $listingQuery);
if ($result) {
    echo "<p>✅ Listing query works! Found " . mysqli_num_rows($result) . " articles</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Date</th><th>Approved</th><th>Test Link</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $urlSlug = htmlspecialchars($row['url_slug']);
        $title = htmlspecialchars($row['title_news']);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $title . "</td>";
        echo "<td>" . $urlSlug . "</td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "<td>" . $row['approved'] . "</td>";
        echo "<td><a href='/news/$urlSlug' target='_blank'>Test</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Listing query failed: " . mysqli_error($connection) . "</p>";
}

echo "<h4>B) Single news query (what news-single.php uses):</h4>";
$testUrl = 'miit-snova-smenil-imya';
echo "<p>Testing with URL: <strong>$testUrl</strong></p>";

$singleQuery = "SELECT n.*, c.title_category, c.url_category
                FROM news n
                LEFT JOIN categories c ON n.category_news = c.id_category
                WHERE n.url_slug = ? AND n.approved = 1";

$stmt = $connection->prepare($singleQuery);
$stmt->bind_param("s", $testUrl);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $news = $result->fetch_assoc();
    echo "<p>✅ Single news query found article!</p>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    foreach ($news as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Single news query found no results</p>";
    
    // Let's check if the URL exists at all
    echo "<h4>C) Check if URL exists regardless of approval status:</h4>";
    $checkQuery = "SELECT id, title_news, url_slug, approved FROM news WHERE url_slug = ?";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("s", $testUrl);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkNews = $checkResult->fetch_assoc();
        echo "<p>⚠️ URL exists but approved = " . $checkNews['approved'] . "</p>";
        echo "<table border='1'>";
        foreach ($checkNews as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ URL doesn't exist in database at all</p>";
    }
}

echo "<h3>2. Check what field the NEWS LISTING actually uses vs SINGLE:</h3>";

// Let's examine the news listing file to see what it's actually doing
$newsListingFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($newsListingFile)) {
    echo "<h4>D) Examining news listing file:</h4>";
    $content = file_get_contents($newsListingFile);
    
    // Look for the main query in the news listing
    if (preg_match('/SELECT.*?FROM\s+news.*?WHERE.*?;/s', $content, $matches)) {
        echo "<p><strong>Found query in news listing:</strong></p>";
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    } else {
        echo "<p>Could not find main query in news listing file</p>";
    }
    
    // Check what field is used for URLs in the listing
    if (preg_match_all('/href=[\'"]\/news\/([^\'"\s]+)/', $content, $matches)) {
        echo "<p><strong>URL pattern used in listing:</strong></p>";
        echo "<pre>" . implode("\n", array_unique($matches[0])) . "</pre>";
    }
} else {
    echo "<p>❌ News listing file not found: $newsListingFile</p>";
}

echo "<h3>3. Recommendation:</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0; border-left: 4px solid #0066cc;'>";
echo "<p><strong>The issue is likely:</strong></p>";
echo "<ul>";
echo "<li>News listing is showing articles that have <code>approved = 0</code></li>";
echo "<li>OR news listing is using different field references than single news</li>";
echo "<li>OR there's a field mismatch between what listing uses vs what single uses</li>";
echo "</ul>";
echo "<p><strong>Next step:</strong> Fix the news listing query to match the single news query requirements</p>";
echo "</div>";

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
</style>