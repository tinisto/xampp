<?php
// Debug navigation active state issue - detailed version

echo "<h3>Debug Navigation Active State - Detailed</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>url_news GET param:</strong> " . ($_GET['url_news'] ?? 'NOT SET') . "</p>";

$currentPath = $_SERVER['REQUEST_URI'];
$cleanPath = parse_url($currentPath, PHP_URL_PATH);
$urlNews = $_GET['url_news'] ?? '';

echo "<p><strong>Clean Path:</strong> {$cleanPath}</p>";
echo "<p><strong>URL News:</strong> {$urlNews}</p>";

// Test the navigation logic exactly as in component
$newsNavItems = [
    ['title' => 'Все новости', 'url' => '/news'],
    ['title' => 'Новости ВПО', 'url' => '/news/novosti-vuzov'],
    ['title' => 'Новости СПО', 'url' => '/news/novosti-spo'],
    ['title' => 'Новости школ', 'url' => '/news/novosti-shkol'],
    ['title' => 'Новости образования', 'url' => '/news/novosti-obrazovaniya']
];

echo "<h4>Navigation Logic Step by Step:</h4>";
foreach ($newsNavItems as $item) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
    echo "<h5>{$item['title']} - {$item['url']}</h5>";
    
    $isActive = false;
    
    // Check exact path match
    $exactMatch = ($cleanPath === $item['url'] || (empty($cleanPath) && $item['url'] === '/'));
    echo "<p>1. Exact path match: '{$cleanPath}' === '{$item['url']}' = " . ($exactMatch ? 'TRUE' : 'false') . "</p>";
    
    if ($exactMatch) {
        $isActive = true;
        echo "<p>✅ ACTIVE (exact match)</p>";
    } else {
        // Check news category mapping
        $isNewsPath = strpos($currentPath, '/news') === 0;
        echo "<p>2. Is news path: " . ($isNewsPath ? 'TRUE' : 'false') . "</p>";
        echo "<p>3. Has url_news param: " . ($urlNews ? 'TRUE' : 'false') . "</p>";
        
        if ($isNewsPath && $urlNews) {
            $newsMapping = [
                'novosti-vuzov' => '/news/novosti-vuzov',
                'novosti-spo' => '/news/novosti-spo', 
                'novosti-shkol' => '/news/novosti-shkol',
                'novosti-obrazovaniya' => '/news/novosti-obrazovaniya'
            ];
            
            $mappedUrl = $newsMapping[$urlNews] ?? 'NOT_FOUND';
            echo "<p>4. Mapped URL: {$urlNews} → {$mappedUrl}</p>";
            echo "<p>5. Mapped match: '{$mappedUrl}' === '{$item['url']}' = " . ($mappedUrl === $item['url'] ? 'TRUE' : 'false') . "</p>";
            
            if (isset($newsMapping[$urlNews]) && $newsMapping[$urlNews] === $item['url']) {
                $isActive = true;
                echo "<p>✅ ACTIVE (mapped match)</p>";
            }
        }
    }
    
    if (!$isActive) {
        echo "<p>❌ NOT ACTIVE</p>";
    }
    
    echo "</div>";
}
?>