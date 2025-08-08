<?php
// Test cards-grid component directly

echo "<h1>Testing Cards Grid Component</h1>";

// Test data
$testPosts = [
    [
        'id_news' => 413,
        'title_news' => 'Хочу поблагодарить',
        'url_news' => 'hochu-poblagodarit',
        'image_news' => '/images/default-news.jpg',
        'created_at' => '2015-08-06',
        'category_title' => 'А напоследок я скажу',
        'category_url' => 'a-naposledok-ya-skazhu'
    ],
    [
        'id_news' => 222,
        'title_news' => 'Спасибо',
        'url_news' => 'spasibo',
        'image_news' => '/images/default-news.jpg',
        'created_at' => '2011-12-25',
        'category_title' => 'А напоследок я скажу',
        'category_url' => 'a-naposledok-ya-skazhu'
    ]
];

// Check if component exists
$cardsGridPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/cards-grid.php';
echo "<p>Checking for cards-grid.php at: " . htmlspecialchars($cardsGridPath) . "</p>";

if (file_exists($cardsGridPath)) {
    echo "<p>✓ File exists, size: " . filesize($cardsGridPath) . " bytes</p>";
    
    // Include it
    echo "<p>Including file...</p>";
    include_once $cardsGridPath;
    
    // Check if function exists
    if (function_exists('renderCardsGrid')) {
        echo "<p>✓ renderCardsGrid function exists</p>";
        echo "<p>Calling renderCardsGrid with " . count($testPosts) . " posts...</p>";
        
        echo "<div style='border: 2px solid #333; padding: 20px; margin: 20px 0;'>";
        echo "<h3>Output from renderCardsGrid:</h3>";
        renderCardsGrid($testPosts, 'post', [
            'columns' => 4,
            'gap' => 20,
            'showBadge' => true
        ]);
        echo "</div>";
        
    } else {
        echo "<p style='color:red;'>✗ renderCardsGrid function NOT found!</p>";
        echo "<p>Functions available after include:</p>";
        $functions = get_defined_functions()['user'];
        $filtered = array_filter($functions, function($f) {
            return strpos($f, 'render') !== false;
        });
        echo "<pre>";
        print_r($filtered);
        echo "</pre>";
    }
} else {
    echo "<p style='color:red;'>✗ cards-grid.php NOT found!</p>";
}

// Also check the real_title component
echo "<h2>Testing real_title Component</h2>";
$realTitlePath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
if (file_exists($realTitlePath)) {
    echo "<p>✓ real_title.php exists</p>";
    include_once $realTitlePath;
    if (function_exists('renderRealTitle')) {
        echo "<p>✓ renderRealTitle function exists</p>";
    }
}

// List all files in common-components
echo "<h2>Files in common-components directory:</h2>";
$componentsDir = $_SERVER['DOCUMENT_ROOT'] . '/common-components/';
if (is_dir($componentsDir)) {
    $files = scandir($componentsDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file (" . filesize($componentsDir . $file) . " bytes)</li>";
        }
    }
    echo "</ul>";
}
?>