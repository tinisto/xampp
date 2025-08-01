<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Individual News Page Test</h1>";

// Simulate URL parameter
$_GET['url_news'] = 'prodleniye-novogodnikh-kanikul-v-shkolakh-belgorodskoy-oblasti';

echo "<p>Testing URL: " . htmlspecialchars($_GET['url_news']) . "</p>";

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection) {
    echo "<p>✓ Database connection successful</p>";
} else {
    echo "<p class='error'>✗ Database connection failed</p>";
    exit;
}

// Test the data fetch
include $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news-data-fetch.php';

if (isset($newsData) && $newsData) {
    echo "<h2>✓ News data found:</h2>";
    echo "<ul>";
    echo "<li><strong>Title:</strong> " . htmlspecialchars($newsData['title_news']) . "</li>";
    echo "<li><strong>Description:</strong> " . htmlspecialchars($newsData['description_news']) . "</li>";
    echo "<li><strong>URL:</strong> " . htmlspecialchars($newsData['url_news']) . "</li>";
    echo "<li><strong>Page Title:</strong> " . htmlspecialchars($pageTitle) . "</li>";
    echo "<li><strong>Meta Description:</strong> " . htmlspecialchars($metaD) . "</li>";
    echo "</ul>";
    
    echo "<h2>Testing Template Rendering:</h2>";
    
    // Test template rendering
    $templateConfig = [
        'layoutType' => 'default',
        'cssFramework' => 'bootstrap',
        'headerType' => 'modern',
        'footerType' => 'modern',
        'darkMode' => true,
        'metaD' => $metaD,
        'metaK' => $metaK,
        'newsData' => $newsData,
        'urlNews' => $urlNews,
    ];
    
    echo "<p>✓ Template config created</p>";
    echo "<p>Now testing template rendering...</p>";
    
    // Include template engine
    include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
    renderTemplate($pageTitle, 'pages/common/news/news-content.php', $templateConfig);
    
} else {
    echo "<p class='error'>✗ News data not found for URL: " . htmlspecialchars($_GET['url_news']) . "</p>";
    
    // Let's see what's in the database
    echo "<h2>Available news URLs in database:</h2>";
    $query = "SELECT url_news, title_news FROM news LIMIT 10";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . htmlspecialchars($row['url_news']) . " - " . htmlspecialchars($row['title_news']) . "</li>";
        }
        echo "</ul>";
    }
}

echo "<style>
.error { color: red; font-weight: bold; }
h1, h2 { color: #333; }
ul { background: #f5f5f5; padding: 10px; }
li { margin: 5px 0; }
</style>";
?>