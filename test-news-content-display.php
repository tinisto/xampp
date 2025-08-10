<?php
// Test what's actually in greyContent5
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Include the news page to get its content
ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
$output = ob_get_clean();

// The news.php file should have set all the greyContent variables
echo "<h1>News Page Content Variables Test</h1>";

echo "<h2>greyContent1 (Title):</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo $greyContent1 ?? 'NOT SET';
echo "</div>";

echo "<h2>greyContent2 (Navigation):</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo $greyContent2 ?? 'NOT SET';
echo "</div>";

echo "<h2>greyContent5 (Main Content - should have news cards):</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
if (isset($greyContent5)) {
    echo "<p>Length: " . strlen($greyContent5) . " characters</p>";
    echo "<p>First 500 characters:</p>";
    echo "<pre>" . htmlspecialchars(substr($greyContent5, 0, 500)) . "...</pre>";
    echo "<hr>";
    echo "<p>Full content:</p>";
    echo $greyContent5;
} else {
    echo "NOT SET";
}
echo "</div>";

echo "<h2>greyContent6 (Pagination):</h2>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
echo $greyContent6 ?? 'NOT SET';
echo "</div>";

// Test direct rendering
echo "<h2>Testing Direct Rendering:</h2>";
$newsItems = [
    [
        'id_news' => 1,
        'title_news' => 'Test News Item',
        'url_news' => 'test-news-1',
        'image_news' => '/images/default-news.jpg',
        'created_at' => date('Y-m-d H:i:s'),
        'category_title' => 'Test Category',
        'category_url' => 'test'
    ]
];

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
renderCardsGrid($newsItems, 'news', [
    'columns' => 4,
    'gap' => 20,
    'showBadge' => true
]);
?>