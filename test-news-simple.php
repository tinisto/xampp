<?php
// Simple test to check news rendering without including news.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Simple News Display Test</h1>";

// Check connection
if (!isset($connection)) {
    die("<p style='color: red;'>Database connection not set!</p>");
}

echo "<p style='color: green;'>✓ Database connection OK</p>";

// Get news items directly
$query = "SELECT id, title_news, url_slug, image_news, date_news, category_news 
          FROM news 
          ORDER BY date_news DESC 
          LIMIT 4";

$result = mysqli_query($connection, $query);
if (!$result) {
    die("<p style='color: red;'>Query failed: " . mysqli_error($connection) . "</p>");
}

$newsItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categoryTitle = 'Новости';
    switch ($row['category_news']) {
        case '1': $categoryTitle = 'Новости ВПО'; break;
        case '2': $categoryTitle = 'Новости СПО'; break;
        case '3': $categoryTitle = 'Новости школ'; break;
        case '4': $categoryTitle = 'Новости образования'; break;
    }
    
    $newsItems[] = [
        'id_news' => $row['id'],
        'title_news' => $row['title_news'],
        'url_news' => $row['url_slug'], 
        'image_news' => $row['image_news'],
        'created_at' => $row['date_news'],
        'category_title' => $categoryTitle,
        'category_url' => 'news'
    ];
}

echo "<p>Found " . count($newsItems) . " news items</p>";

// Test 1: Direct HTML output
echo "<h2>Test 1: Direct HTML Output</h2>";
echo '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">';
foreach ($newsItems as $item) {
    echo '<div style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">';
    echo '<span style="background: #007bff; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;">' . htmlspecialchars($item['category_title']) . '</span>';
    echo '<h3 style="margin: 10px 0;">' . htmlspecialchars($item['title_news']) . '</h3>';
    echo '<p>URL: ' . htmlspecialchars($item['url_news']) . '</p>';
    echo '</div>';
}
echo '</div>';

// Test 2: Check if cards-grid.php exists
echo "<h2>Test 2: Cards Grid Component</h2>";
$cardsGridPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
if (file_exists($cardsGridPath)) {
    echo "<p style='color: green;'>✓ cards-grid.php exists</p>";
    
    // Try to include it
    try {
        include_once $cardsGridPath;
        
        if (function_exists('renderCardsGrid')) {
            echo "<p style='color: green;'>✓ renderCardsGrid function exists</p>";
            
            // Try to render
            echo "<h3>Rendering with cards-grid:</h3>";
            ob_start();
            renderCardsGrid($newsItems, 'news', [
                'columns' => 2,
                'gap' => 20,
                'showBadge' => true
            ]);
            $output = ob_get_clean();
            
            echo "<p>Output length: " . strlen($output) . " characters</p>";
            echo $output;
        } else {
            echo "<p style='color: red;'>✗ renderCardsGrid function not found</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error including cards-grid: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ cards-grid.php not found at: $cardsGridPath</p>";
}

// Test 3: Check what might be causing the loading message
echo "<h2>Test 3: Looking for Loading Message</h2>";
$possibleFiles = [
    '/common-components/loading-placeholders.php',
    '/common-components/loading-placeholders-v2.php',
    '/js/lazy-content-loader.js'
];

foreach ($possibleFiles as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    echo "<p>$file: " . (file_exists($fullPath) ? "<span style='color: green;'>exists</span>" : "<span style='color: red;'>not found</span>") . "</p>";
}
?>