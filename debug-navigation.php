<?php
// Debug navigation active state issue

echo "<h3>Debug Navigation Active State</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Test the navigation logic
$newsNavItems = [
    ['title' => 'Все новости', 'url' => '/news'],
    ['title' => 'Новости ВПО', 'url' => '/news/novosti-vuzov'],
    ['title' => 'Новости СПО', 'url' => '/news/novosti-spo'],
    ['title' => 'Новости школ', 'url' => '/news/novosti-shkol'],
    ['title' => 'Новости образования', 'url' => '/news/novosti-obrazovaniya']
];

$currentPath = $_SERVER['REQUEST_URI'];

echo "<h4>Navigation Items Active State:</h4>";
foreach ($newsNavItems as $item) {
    $isActive = ($currentPath === $item['url']) || (empty($currentPath) && $item['url'] === '/');
    $status = $isActive ? '✅ ACTIVE' : '❌ inactive';
    echo "<p>{$item['title']} ({$item['url']}) = {$status}</p>";
    echo "<p>&nbsp;&nbsp;&nbsp;Match: '{$currentPath}' === '{$item['url']}' = " . ($currentPath === $item['url'] ? 'true' : 'false') . "</p>";
}

// Check if there are query parameters
if (strpos($currentPath, '?') !== false) {
    echo "<h4>Query Parameters Detected:</h4>";
    $pathWithoutQuery = parse_url($currentPath, PHP_URL_PATH);
    echo "<p><strong>Path without query:</strong> {$pathWithoutQuery}</p>";
    
    echo "<h4>Testing with clean path:</h4>";
    foreach ($newsNavItems as $item) {
        $isActive = ($pathWithoutQuery === $item['url']) || (empty($pathWithoutQuery) && $item['url'] === '/');
        $status = $isActive ? '✅ ACTIVE' : '❌ inactive';
        echo "<p>{$item['title']} ({$item['url']}) = {$status}</p>";
    }
}
?>