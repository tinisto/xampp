<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/database.php';

echo "<h2>Testing News Query</h2>";

// Test 1: Count all news
$query1 = "SELECT COUNT(*) as total FROM news";
$result1 = mysqli_query($connection, $query1);
$count1 = mysqli_fetch_assoc($result1)['total'];
echo "Total news in database: $count1<br><br>";

// Test 2: Count approved news
$query2 = "SELECT COUNT(*) as total FROM news WHERE approved = 1";
$result2 = mysqli_query($connection, $query2);
$count2 = mysqli_fetch_assoc($result2)['total'];
echo "Approved news: $count2<br><br>";

// Test 3: Show first 5 news records
$query3 = "SELECT id_news, title_news, approved, date_news FROM news ORDER BY date_news DESC LIMIT 5";
$result3 = mysqli_query($connection, $query3);
echo "Latest 5 news records:<br>";
while ($row = mysqli_fetch_assoc($result3)) {
    echo "ID: {$row['id_news']}, Title: {$row['title_news']}, Approved: {$row['approved']}, Date: {$row['date_news']}<br>";
}
echo "<br>";

// Test 4: Check if approved field has different values
$query4 = "SELECT DISTINCT approved FROM news";
$result4 = mysqli_query($connection, $query4);
echo "Distinct values in approved field: ";
while ($row = mysqli_fetch_assoc($result4)) {
    echo "'{$row['approved']}' ";
}
echo "<br><br>";

// Test 5: Try the exact query from news.php
$query5 = "SELECT n.* FROM news n WHERE n.approved = 1 ORDER BY n.date_news DESC LIMIT 12";
$result5 = mysqli_query($connection, $query5);
if ($result5) {
    $count5 = mysqli_num_rows($result5);
    echo "Query from news.php returns: $count5 rows<br>";
    if ($count5 > 0) {
        echo "First result: ";
        $first = mysqli_fetch_assoc($result5);
        echo "ID: {$first['id_news']}, Title: {$first['title_news']}<br>";
    }
} else {
    echo "Query error: " . mysqli_error($connection) . "<br>";
}

mysqli_close($connection);
?>
EOF < /dev/null