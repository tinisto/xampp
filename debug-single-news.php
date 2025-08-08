<?php
// Debug single news article
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$newsUrl = 'rektor-rggu-e-n-ivahnenko-rasskazal-o-hode-priemnoy-kampanii';

echo "<h2>Debug Single News: $newsUrl</h2>";

// First, let's check if the news table has this URL
echo "<h3>1. Check if URL exists in news table:</h3>";
$checkUrl = "SELECT id_news, title_news, url_news, status FROM news WHERE url_news = ?";
$stmt = $connection->prepare($checkUrl);
$stmt->bind_param("s", $newsUrl);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<pre>" . print_r($row, true) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ No news found with url_news = '$newsUrl'</p>";
    
    // Let's check similar URLs
    echo "<h3>2. Check similar URLs in news table:</h3>";
    $similarQuery = "SELECT id_news, title_news, url_news, status FROM news WHERE url_news LIKE ? LIMIT 5";
    $stmt = $connection->prepare($similarQuery);
    $searchTerm = '%rektor%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>Found similar URLs:</p>";
        while ($row = $result->fetch_assoc()) {
            echo "<pre>" . print_r($row, true) . "</pre>";
        }
    } else {
        echo "<p>No similar URLs found</p>";
    }
}

// Check the table structure
echo "<h3>3. News table structure:</h3>";
$describeQuery = "DESCRIBE news";
$result = mysqli_query($connection, $describeQuery);
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check how many news articles exist
echo "<h3>4. Total news count:</h3>";
$countQuery = "SELECT COUNT(*) as total FROM news";
$result = mysqli_query($connection, $countQuery);
$count = mysqli_fetch_assoc($result);
echo "<p>Total news articles: " . $count['total'] . "</p>";

// Show some example news URLs
echo "<h3>5. Sample news URLs:</h3>";
$sampleQuery = "SELECT id_news, title_news, url_news, status FROM news ORDER BY id_news DESC LIMIT 10";
$result = mysqli_query($connection, $sampleQuery);
while ($row = mysqli_fetch_assoc($result)) {
    echo "<p><strong>" . $row['id_news'] . "</strong>: " . $row['url_news'] . " (" . $row['status'] . ")</p>";
}

mysqli_close($connection);
?>