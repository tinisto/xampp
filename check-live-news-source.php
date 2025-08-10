<?php
// Check what the actual news page returns
$url = 'https://11klassniki.ru/news';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$output = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h1>Live News Page Analysis</h1>";
echo "<p>URL: $url</p>";
echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Output length: " . strlen($output) . " characters</p>";

// Look for specific content
$searches = [
    'Новости загружаются' => 'Loading message',
    'База данных новостей' => 'Database header',
    '496' => 'News count',
    'grid-template-columns' => 'Grid layout',
    'cards-grid' => 'Cards grid component',
    'greyContent5' => 'Content variable',
    '<div class="card"' => 'Card elements',
    'В базе данных найдено' => 'Database count message'
];

echo "<h2>Content Search:</h2>";
foreach ($searches as $search => $description) {
    $pos = strpos($output, $search);
    if ($pos !== false) {
        echo "<p style='color: green;'>✓ Found '$description' at position $pos</p>";
        
        // Show context
        $start = max(0, $pos - 100);
        $end = min(strlen($output), $pos + 200);
        $context = substr($output, $start, $end - $start);
        echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 12px;'>" . htmlspecialchars($context) . "</pre>";
    } else {
        echo "<p style='color: red;'>✗ Not found: $description ('$search')</p>";
    }
}

// Check if it's using the template or something else
if (strpos($output, 'real_template.php') !== false) {
    echo "<p>Using real_template.php</p>";
}

// Save output for inspection
file_put_contents('news-page-output.html', $output);
echo "<p>Full output saved to news-page-output.html</p>";
?>