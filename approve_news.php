<?php
// Approve news articles
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>News Approval Tool</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check current status
$unapproved = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 0")->fetch_assoc()['count'];
$approved = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 1")->fetch_assoc()['count'];

echo "<h2>Current Status:</h2>";
echo "<p>✅ Approved news: <strong>$approved</strong></p>";
echo "<p>⏳ Unapproved news: <strong>$unapproved</strong></p>";

if (isset($_GET['approve']) && $_GET['approve'] == 'all') {
    // Approve all news
    $approve_query = "UPDATE news SET is_approved = 1 WHERE is_approved = 0";
    if ($connection->query($approve_query)) {
        $affected = $connection->affected_rows;
        echo "<p style='color: green; font-size: 18px;'>✅ Successfully approved $affected news articles!</p>";
        echo "<p><a href='approve_news.php'>Refresh</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
    }
} elseif (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    // Approve specific number
    $limit = intval($_GET['approve']);
    $approve_query = "UPDATE news SET is_approved = 1 WHERE is_approved = 0 ORDER BY created_at DESC LIMIT $limit";
    if ($connection->query($approve_query)) {
        $affected = $connection->affected_rows;
        echo "<p style='color: green; font-size: 18px;'>✅ Successfully approved $affected news articles!</p>";
        echo "<p><a href='approve_news.php'>Refresh</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
    }
} else {
    // Show options
    if ($unapproved > 0) {
        echo "<h2>Choose Action:</h2>";
        echo "<p>";
        echo "<a href='?approve=20' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; margin-right: 10px;'>Approve 20 Recent</a>";
        echo "<a href='?approve=50' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; margin-right: 10px;'>Approve 50 Recent</a>";
        echo "<a href='?approve=all' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none;'>Approve All</a>";
        echo "</p>";
    } else {
        echo "<p style='color: green; font-size: 18px;'>✅ All news articles are already approved!</p>";
    }
}

// Show recent unapproved news
if ($unapproved > 0 && !isset($_GET['approve'])) {
    echo "<h2>Recent Unapproved News (Preview):</h2>";
    $preview = $connection->query("SELECT id_news, title_news, created_at FROM news WHERE is_approved = 0 ORDER BY created_at DESC LIMIT 5");
    if ($preview && $preview->num_rows > 0) {
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Created</th></tr>";
        while ($row = $preview->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . $row['id_news'] . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['title_news']) . "</td>";
            echo "<td style='padding: 5px;'>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

echo "<hr>";
echo "<p><a href='/'>← Homepage</a> | <a href='/site_review.php'>Site Review</a></p>";

$connection->close();
?>