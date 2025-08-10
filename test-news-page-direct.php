<?php
// Test news page rendering directly
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct News Page Test</h1>";

// Check connection
if (!isset($connection)) {
    die("<p style='color: red;'>ERROR: Database connection not set!</p>");
}

echo "<p style='color: green;'>✓ Database connection OK</p>";

// Get current page
$page = 1;
$perPage = 16;
$offset = 0;

// Count total news
$countQuery = "SELECT COUNT(*) as total FROM news";
$countResult = mysqli_query($connection, $countQuery);
if (!$countResult) {
    die("<p style='color: red;'>Count query failed: " . mysqli_error($connection) . "</p>");
}
$totalNews = mysqli_fetch_assoc($countResult)['total'];
echo "<p>Total news in database: <strong>$totalNews</strong></p>";

// Get news items
$query = "SELECT id, title_news, url_slug, image_news, date_news, category_news 
          FROM news 
          ORDER BY date_news DESC 
          LIMIT $perPage OFFSET $offset";

echo "<p>Query: <code>" . htmlspecialchars($query) . "</code></p>";

$result = mysqli_query($connection, $query);
if (!$result) {
    die("<p style='color: red;'>News query failed: " . mysqli_error($connection) . "</p>");
}

$newsItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $newsItems[] = $row;
}

echo "<p>Fetched <strong>" . count($newsItems) . "</strong> news items</p>";

// Display news
if (count($newsItems) > 0) {
    echo "<h2>News Items:</h2>";
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">';
    
    foreach ($newsItems as $news) {
        $categoryTitle = 'Новости';
        switch ($news['category_news']) {
            case '1': $categoryTitle = 'Новости ВПО'; break;
            case '2': $categoryTitle = 'Новости СПО'; break;
            case '3': $categoryTitle = 'Новости школ'; break;
            case '4': $categoryTitle = 'Новости образования'; break;
        }
        
        echo '<div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">';
        echo '<span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;">' . $categoryTitle . '</span>';
        echo '<h3 style="margin: 10px 0; font-size: 18px;">' . htmlspecialchars($news['title_news'] ?? 'No title') . '</h3>';
        echo '<p style="color: #666; font-size: 14px;">ID: ' . $news['id'] . ' | ' . ($news['date_news'] ?? 'No date') . '</p>';
        echo '<p style="color: #666; font-size: 14px;">URL: ' . htmlspecialchars($news['url_slug'] ?? 'no-slug') . '</p>';
        echo '</div>';
    }
    
    echo '</div>';
} else {
    echo "<p>No news items found!</p>";
}

// Check what the news page is actually doing
echo "<h2>Checking news.php includes:</h2>";
$newsPageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($newsPageFile)) {
    echo "<p>✓ News page file exists</p>";
    
    // Check if cards-grid component exists
    $cardsGridFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
    if (file_exists($cardsGridFile)) {
        echo "<p>✓ Cards grid component exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Cards grid component missing!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ News page file not found!</p>";
}
?>