<?php
// Analyze news table categories to understand the numeric mapping
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyze News Categories - 11-классники</title>
    <link rel="icon" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiBmaWxsPSIjMDA3YmZmIi8+Cjx0ZXh0IHg9IjE2IiB5PSIyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjE0IiBmb250LXdlaWdodD0iYm9sZCIgZmlsbD0id2hpdGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiPjExPC90ZXh0Pgo8L3N2Zz4K" type="image/svg+xml">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .sample { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .category-1 { background-color: #e3f2fd; }
        .category-2 { background-color: #f3e5f5; }
        .category-3 { background-color: #e8f5e9; }
        .category-4 { background-color: #fff3e0; }
    </style>
</head>
<body>
<h1>News Categories Analysis</h1>

<?php
// Get news table structure
echo "<h2>1. News Table Structure</h2>";
$describeQuery = "DESCRIBE news";
$describeResult = mysqli_query($connection, $describeQuery);

echo "<table>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($describeResult)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// Analyze category distribution
echo "<h2>2. News Category Distribution</h2>";
$categoryQuery = "SELECT category_news, COUNT(*) as count FROM news GROUP BY category_news ORDER BY count DESC";
$categoryResult = mysqli_query($connection, $categoryQuery);

echo "<table>";
echo "<tr><th>Category Value</th><th>Count</th><th>Percentage</th></tr>";
$total = 0;
$categories = [];
while ($row = mysqli_fetch_assoc($categoryResult)) {
    $categories[] = $row;
    $total += $row['count'];
}

foreach ($categories as $cat) {
    $percentage = round(($cat['count'] / $total) * 100, 2);
    echo "<tr class='category-{$cat['category_news']}'>";
    echo "<td><strong>{$cat['category_news']}</strong></td>";
    echo "<td>{$cat['count']}</td>";
    echo "<td>{$percentage}%</td>";
    echo "</tr>";
}
echo "</table>";

// Sample news from each category
echo "<h2>3. Sample News Items by Category</h2>";

foreach (['1', '2', '3', '4'] as $cat) {
    echo "<h3>Category: $cat</h3>";
    
    $sampleQuery = "SELECT id_news, title_news, date_news, author_news 
                    FROM news 
                    WHERE category_news = '$cat' 
                    ORDER BY date_news DESC 
                    LIMIT 5";
    $sampleResult = mysqli_query($connection, $sampleQuery);
    
    if (mysqli_num_rows($sampleResult) > 0) {
        echo "<table class='category-$cat'>";
        echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Author</th></tr>";
        while ($row = mysqli_fetch_assoc($sampleResult)) {
            echo "<tr>";
            echo "<td>{$row['id_news']}</td>";
            echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
            echo "<td>{$row['date_news']}</td>";
            echo "<td>" . htmlspecialchars($row['author_news'] ?? 'Unknown') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Analyze date ranges for each category
echo "<h2>4. Date Analysis by Category</h2>";
echo "<table>";
echo "<tr><th>Category</th><th>Earliest Date</th><th>Latest Date</th><th>Date Range</th></tr>";

foreach (['1', '2', '3', '4'] as $cat) {
    $dateQuery = "SELECT MIN(date_news) as min_date, MAX(date_news) as max_date 
                  FROM news 
                  WHERE category_news = '$cat'";
    $dateResult = mysqli_query($connection, $dateQuery);
    $dates = mysqli_fetch_assoc($dateResult);
    
    echo "<tr class='category-$cat'>";
    echo "<td><strong>$cat</strong></td>";
    echo "<td>{$dates['min_date']}</td>";
    echo "<td>{$dates['max_date']}</td>";
    echo "<td>";
    if ($dates['min_date'] && $dates['max_date']) {
        $diff = strtotime($dates['max_date']) - strtotime($dates['min_date']);
        $days = floor($diff / (60 * 60 * 24));
        echo "$days days";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if there's any pattern in content
echo "<h2>5. Content Analysis</h2>";
echo "<p>Checking for keywords in titles to identify category patterns...</p>";

$keywords = [
    'школ' => 'School-related',
    'университет' => 'University-related',
    'ЕГЭ' => 'Exam-related',
    'поступ' => 'Admission-related',
    'студент' => 'Student-related',
    'образован' => 'Education-related',
    'выпуск' => 'Graduation-related'
];

echo "<table>";
echo "<tr><th>Category</th><th>Keyword</th><th>Count</th></tr>";

foreach (['1', '2', '3', '4'] as $cat) {
    foreach ($keywords as $keyword => $description) {
        $keywordQuery = "SELECT COUNT(*) as count 
                        FROM news 
                        WHERE category_news = '$cat' 
                        AND (title_news LIKE '%$keyword%' OR text_news LIKE '%$keyword%')";
        $keywordResult = mysqli_query($connection, $keywordQuery);
        $count = mysqli_fetch_assoc($keywordResult)['count'];
        
        if ($count > 0) {
            echo "<tr class='category-$cat'>";
            echo "<td>$cat</td>";
            echo "<td>$description</td>";
            echo "<td>$count</td>";
            echo "</tr>";
        }
    }
}
echo "</table>";

// Suggested mapping
echo "<h2>6. Suggested Category Mapping</h2>";
echo "<div class='sample'>";
echo "<p>Based on the analysis, here are suggested mappings for news categories:</p>";
echo "<ul>";
echo "<li><strong>Category 1 (243 items)</strong> - Appears to be general education news</li>";
echo "<li><strong>Category 2 (96 items)</strong> - Possibly university/higher education news</li>";
echo "<li><strong>Category 3 (6 items)</strong> - Small category, might be special announcements</li>";
echo "<li><strong>Category 4 (156 items)</strong> - Second largest, possibly school-related news</li>";
echo "</ul>";
echo "<p><strong>Recommendation:</strong> Create new categories in the categories table specifically for news, or map to existing categories based on content analysis.</p>";
echo "</div>";

mysqli_close($connection);
?>

<p style="margin-top: 40px;">
    <a href="execute-db-standardization.php" style="color: #007bff;">← Back to Database Standardization</a>
</p>

</body>
</html>