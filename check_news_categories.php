<?php
// Check news categories and approval status
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>News Categories & Approval Check</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// 1. Check unique categories
echo "<h2>1. News Categories:</h2>";
$cat_query = "SELECT DISTINCT category_news, COUNT(*) as count FROM news GROUP BY category_news ORDER BY count DESC";
$cat_result = $connection->query($cat_query);

if ($cat_result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Category</th><th>Count</th></tr>";
    while ($row = $cat_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . ($row['category_news'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 5px;'>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Check approval status breakdown
echo "<h2>2. Approval Status Breakdown:</h2>";
$approval_query = "SELECT approved, COUNT(*) as count FROM news GROUP BY approved";
$approval_result = $connection->query($approval_query);

if ($approval_result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Approved</th><th>Count</th></tr>";
    while ($row = $approval_result->fetch_assoc()) {
        $status = $row['approved'] === null ? 'NULL' : ($row['approved'] == 1 ? 'Yes (1)' : 'No (0)');
        echo "<tr>";
        echo "<td style='padding: 5px;'>$status</td>";
        echo "<td style='padding: 5px;'>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 3. Check sample news with different queries
echo "<h2>3. Sample News Queries:</h2>";

// Query 1: Simple query for approved news
echo "<h3>Query 1: SELECT * FROM news WHERE approved = 1 LIMIT 5</h3>";
$q1 = $connection->query("SELECT id_news, title_news, approved, category_news FROM news WHERE approved = 1 LIMIT 5");
if ($q1 && $q1->num_rows > 0) {
    echo "<p style='color: green;'>✅ Found " . $q1->num_rows . " approved news</p>";
    while ($row = $q1->fetch_assoc()) {
        echo "<p>- " . htmlspecialchars($row['title_news']) . " (Category: " . $row['category_news'] . ")</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No results</p>";
}

// Query 2: Check for specific category
echo "<h3>Query 2: SELECT * FROM news WHERE category_news = 'novosti-obrazovaniya' AND approved = 1 LIMIT 5</h3>";
$q2 = $connection->query("SELECT id_news, title_news, approved, category_news FROM news WHERE category_news = 'novosti-obrazovaniya' AND approved = 1 LIMIT 5");
if ($q2 && $q2->num_rows > 0) {
    echo "<p style='color: green;'>✅ Found " . $q2->num_rows . " approved education news</p>";
} else {
    echo "<p style='color: red;'>❌ No results for education category</p>";
}

// 4. Check what the news page might be querying
echo "<h2>4. Possible Issue - Category Mismatch:</h2>";
$edu_check = "SELECT COUNT(*) as total, 
              SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved,
              SUM(CASE WHEN category_news = 'novosti-obrazovaniya' THEN 1 ELSE 0 END) as edu_category
              FROM news";
$result = $connection->query($edu_check);
if ($result) {
    $data = $result->fetch_assoc();
    echo "<p>Total news: " . $data['total'] . "</p>";
    echo "<p>Approved news: " . $data['approved'] . "</p>";
    echo "<p>News with 'novosti-obrazovaniya' category: " . $data['edu_category'] . "</p>";
}

// 5. Show all unique categories with their approved counts
echo "<h2>5. Categories with Approval Status:</h2>";
$cat_approved = "SELECT category_news, 
                 COUNT(*) as total,
                 SUM(CASE WHEN approved = 1 THEN 1 ELSE 0 END) as approved_count
                 FROM news 
                 GROUP BY category_news";
$result = $connection->query($cat_approved);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Category</th><th>Total</th><th>Approved</th><th>%</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $percent = $row['total'] > 0 ? round(($row['approved_count'] / $row['total']) * 100, 1) : 0;
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . htmlspecialchars($row['category_news'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 5px;'>" . $row['total'] . "</td>";
        echo "<td style='padding: 5px;'>" . $row['approved_count'] . "</td>";
        echo "<td style='padding: 5px;'>$percent%</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p><a href='/'>← Homepage</a> | <a href='/news/novosti-obrazovaniya'>News Education Page</a> | <a href='/approve_news_correct.php'>Approve News</a></p>";

$connection->close();
?>