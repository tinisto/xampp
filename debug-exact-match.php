<?php
// Debug exact matching issue

echo "<h3>Debug Exact URL Matching</h3>";

// Simulate the actual URL
$currentPath = '/news/novosti-spo';
$cleanPath = parse_url($currentPath, PHP_URL_PATH);

echo "<p><strong>Current Path:</strong> {$currentPath}</p>";
echo "<p><strong>Clean Path:</strong> {$cleanPath}</p>";

// Test navigation items
$newsNavItems = [
    ['title' => 'Все новости', 'url' => '/news'],
    ['title' => 'Новости ВПО', 'url' => '/news/novosti-vuzov'],
    ['title' => 'Новости СПО', 'url' => '/news/novosti-spo'],
    ['title' => 'Новости школ', 'url' => '/news/novosti-shkol'],
    ['title' => 'Новости образования', 'url' => '/news/novosti-obrazovaniya']
];

echo "<h4>Exact Match Tests:</h4>";
foreach ($newsNavItems as $item) {
    $isExactMatch = ($cleanPath === $item['url']);
    $isEmptyMatch = (empty($cleanPath) && $item['url'] === '/');
    $isActive = $isExactMatch || $isEmptyMatch;
    
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px; background:" . ($isActive ? '#d4edda' : '#fff') . ";'>";
    echo "<h5>{$item['title']} - {$item['url']}</h5>";
    echo "<p>Exact match: '{$cleanPath}' === '{$item['url']}' = " . ($isExactMatch ? 'TRUE' : 'false') . "</p>";
    echo "<p>Empty match: " . ($isEmptyMatch ? 'TRUE' : 'false') . "</p>";
    echo "<p><strong>RESULT: " . ($isActive ? 'ACTIVE' : 'inactive') . "</strong></p>";
    echo "</div>";
}

// Test with the real logic from the component
echo "<h4>Component Logic Test:</h4>";
$urlNews = ''; // No query params for clean URLs

foreach ($newsNavItems as $item) {
    echo "<div style='border: 1px solid #ddd; padding: 8px; margin: 3px;'>";
    echo "<strong>{$item['title']}:</strong> ";
    
    $isActive = false;
    
    // Check for exact path match first (handles clean URLs like /news/novosti-spo)
    if ($cleanPath === $item['url'] || (empty($cleanPath) && $item['url'] === '/')) {
        $isActive = true;
        echo "ACTIVE (exact match)";
    }
    // Fallback: Special handling for news categories with query parameters  
    elseif (strpos($currentPath, '/news') === 0 && $urlNews) {
        // Map url_news parameter to expected URLs
        $newsMapping = [
            'novosti-vuzov' => '/news/novosti-vuzov',
            'novosti-spo' => '/news/novosti-spo', 
            'novosti-shkol' => '/news/novosti-shkol',
            'novosti-obrazovaniya' => '/news/novosti-obrazovaniya'
        ];
        
        if (isset($newsMapping[$urlNews]) && $newsMapping[$urlNews] === $item['url']) {
            $isActive = true;
            echo "ACTIVE (query mapping)";
        } else {
            echo "inactive";
        }
    } else {
        echo "inactive";
    }
    
    echo "</div>";
}
?>