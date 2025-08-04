<?php
// Debug news data
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

if (!isset($_GET['url_news'])) {
    die("Please provide a url_news parameter");
}

$urlNews = mysqli_real_escape_string($connection, $_GET['url_news']);
$queryNews = "SELECT * FROM news WHERE url_news = '$urlNews'";
$resultNews = mysqli_query($connection, $queryNews);

echo "<!DOCTYPE html>";
echo "<html><head><title>News Debug</title></head><body>";
echo "<h1>News Debug for: " . htmlspecialchars($urlNews) . "</h1>";

if ($resultNews && mysqli_num_rows($resultNews) > 0) {
    $newsData = mysqli_fetch_assoc($resultNews);
    
    echo "<h2>News Data:</h2>";
    echo "<pre>";
    print_r($newsData);
    echo "</pre>";
    
    echo "<h2>Image Field Check:</h2>";
    echo "<p>image_news exists: " . (isset($newsData['image_news']) ? 'YES' : 'NO') . "</p>";
    echo "<p>image_news value: " . htmlspecialchars($newsData['image_news'] ?? 'NULL') . "</p>";
    echo "<p>image_news empty: " . (empty($newsData['image_news']) ? 'YES' : 'NO') . "</p>";
    
    if (!empty($newsData['image_news'])) {
        echo "<h2>Image Display:</h2>";
        echo "<img src='" . htmlspecialchars($newsData['image_news']) . "' alt='News image' style='max-width: 500px;'>";
    }
    
    // Check all columns
    echo "<h2>All Columns:</h2>";
    echo "<table border='1' cellpadding='5'>";
    foreach ($newsData as $key => $value) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
        echo "<td>" . htmlspecialchars(substr($value ?? '', 0, 100)) . (strlen($value ?? '') > 100 ? '...' : '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "<p style='color: red;'>News not found or query error</p>";
    echo "<p>Query: " . htmlspecialchars($queryNews) . "</p>";
    if ($connection->error) {
        echo "<p>Error: " . htmlspecialchars($connection->error) . "</p>";
    }
}

echo "</body></html>";
?>