<?php
// Debug all news categories comprehensively
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Complete News Categories Debug</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

echo "<h3>1. All category_news values in database:</h3>";
$categoryQuery = "SELECT category_news, COUNT(*) as count 
                  FROM news 
                  WHERE approved = 1 
                  GROUP BY category_news 
                  ORDER BY count DESC";

$result = mysqli_query($connection, $categoryQuery);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>category_news Value</th><th>Article Count</th><th>Current Navigation Mapping</th><th>Articles Sample</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $catValue = $row['category_news'];
        $count = $row['count'];
        
        // Show current mapping
        $mapping = "Unknown";
        switch ($catValue) {
            case '1': $mapping = "ВПО (vpo)"; break;
            case '2': $mapping = "СПО (spo)"; break;
            case '3': $mapping = "Школы (school)"; break;
            case '4': $mapping = "Образование (education)"; break;
            case 'education': $mapping = "Education (string value)"; break;
            default: $mapping = "❌ NOT MAPPED"; break;
        }
        
        echo "<tr>";
        echo "<td><strong>$catValue</strong></td>";
        echo "<td>$count articles</td>";
        echo "<td>$mapping</td>";
        
        // Get sample articles
        $sampleQuery = "SELECT title_news, url_slug FROM news WHERE category_news = ? AND approved = 1 LIMIT 3";
        $stmt = $connection->prepare($sampleQuery);
        $stmt->bind_param("s", $catValue);
        $stmt->execute();
        $sampleResult = $stmt->get_result();
        
        echo "<td>";
        while ($sample = $sampleResult->fetch_assoc()) {
            $url = htmlspecialchars($sample['url_slug']);
            $title = htmlspecialchars($sample['title_news']);
            echo "<a href='/news/$url' target='_blank'>" . mb_substr($title, 0, 50) . "...</a><br>";
        }
        echo "</td>";
        
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Query failed: " . mysqli_error($connection) . "</p>";
}

echo "<h3>2. What the navigation currently expects:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Navigation Link</th><th>URL Parameter</th><th>Expected category_news</th><th>Current Articles</th></tr>";

$navMappings = [
    'Все новости' => ['url' => '/news', 'param' => 'none', 'category' => 'all'],
    'Новости ВПО' => ['url' => '/news/novosti-vuzov', 'param' => 'vpo', 'category' => '1'],
    'Новости СПО' => ['url' => '/news/novosti-spo', 'param' => 'spo', 'category' => '2'],
    'Новости школ' => ['url' => '/news/novosti-shkol', 'param' => 'school', 'category' => '3'],
    'Новости образования' => ['url' => '/news/novosti-obrazovaniya', 'param' => 'education', 'category' => '4']
];

foreach ($navMappings as $title => $info) {
    echo "<tr>";
    echo "<td>$title</td>";
    echo "<td><a href='{$info['url']}' target='_blank'>{$info['url']}</a></td>";
    echo "<td>{$info['param']}</td>";
    echo "<td>{$info['category']}</td>";
    
    if ($info['category'] !== 'all') {
        $countQuery = "SELECT COUNT(*) as count FROM news WHERE category_news = ? AND approved = 1";
        $stmt = $connection->prepare($countQuery);
        $stmt->bind_param("s", $info['category']);
        $stmt->execute();
        $countResult = $stmt->get_result();
        $countRow = $countResult->fetch_assoc();
        echo "<td>" . $countRow['count'] . " articles</td>";
    } else {
        echo "<td>All articles</td>";
    }
    
    echo "</tr>";
}
echo "</table>";

echo "<h3>3. Check for articles with unexpected category_news values:</h3>";
$unexpectedQuery = "SELECT category_news, title_news, url_slug, date_news 
                     FROM news 
                     WHERE category_news NOT IN ('1', '2', '3', '4', 'education') 
                     AND approved = 1 
                     ORDER BY date_news DESC";

$result = mysqli_query($connection, $unexpectedQuery);
if ($result && mysqli_num_rows($result) > 0) {
    echo "<p style='color: red;'>❌ Found articles with unexpected category_news values:</p>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>category_news</th><th>Title</th><th>URL</th><th>Date</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $url = htmlspecialchars($row['url_slug']);
        echo "<tr>";
        echo "<td style='background: #ffcccc;'><strong>" . htmlspecialchars($row['category_news']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td><a href='/news/$url' target='_blank'>$url</a></td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h4>Possible Solutions:</h4>";
    echo "<ul>";
    echo "<li>Add these category_news values to navigation</li>";
    echo "<li>OR map these articles to existing categories (1,2,3,4)</li>";
    echo "<li>OR create additional navigation items</li>";
    echo "</ul>";
    
} else {
    echo "<p style='color: green;'>✅ All articles use expected category_news values (1,2,3,4,education)</p>";
}

echo "<h3>4. Specific issue - Articles showing but not clickable:</h3>";
echo "<p>Let's check what articles are being shown in /news/novosti-obrazovaniya but are not accessible individually...</p>";

$educationQuery = "SELECT id, title_news, url_slug, category_news, date_news 
                   FROM news 
                   WHERE category_news = '4' AND approved = 1 
                   ORDER BY date_news DESC 
                   LIMIT 10";

$result = mysqli_query($connection, $educationQuery);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>category_news</th><th>Test Link</th><th>Access Test</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $url = htmlspecialchars($row['url_slug']);
        $title = htmlspecialchars($row['title_news']);
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>$title</td>";
        echo "<td>$url</td>";
        echo "<td>" . $row['category_news'] . "</td>";
        echo "<td><a href='/news/$url' target='_blank'>Test</a></td>";
        
        // Quick test if URL is numeric (which would be problematic)
        if (is_numeric($url)) {
            echo "<td style='color: red;'>❌ NUMERIC URL</td>";
        } else {
            echo "<td style='color: green;'>✅ Text URL</td>";
        }
        
        echo "</tr>";
    }
    echo "</table>";
}

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>