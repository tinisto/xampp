<?php
// Approve news articles - Using correct column name
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>News Approval Tool</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check current status using 'approved' column
$unapproved_result = $connection->query("SELECT COUNT(*) as count FROM news WHERE approved = 0");
$approved_result = $connection->query("SELECT COUNT(*) as count FROM news WHERE approved = 1");

if (!$unapproved_result || !$approved_result) {
    echo "<p style='color: red;'>❌ Error querying news table: " . $connection->error . "</p>";
    exit;
}

$unapproved = $unapproved_result->fetch_assoc()['count'];
$approved = $approved_result->fetch_assoc()['count'];

echo "<h2>Current Status:</h2>";
echo "<p>✅ Approved news: <strong>$approved</strong></p>";
echo "<p>⏳ Unapproved news: <strong>$unapproved</strong></p>";

// Handle approval actions
if (isset($_GET['approve']) && $_GET['approve'] == 'all') {
    // Approve all news
    $approve_query = "UPDATE news SET approved = 1 WHERE approved = 0";
    if ($connection->query($approve_query)) {
        $affected = $connection->affected_rows;
        echo "<p style='color: green; font-size: 18px;'>✅ Successfully approved $affected news articles!</p>";
        echo "<p><a href='approve_news_correct.php'>Refresh</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
    }
} elseif (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    // Approve specific number
    $limit = intval($_GET['approve']);
    $approve_query = "UPDATE news SET approved = 1 WHERE approved = 0 ORDER BY date_news DESC LIMIT $limit";
    if ($connection->query($approve_query)) {
        $affected = $connection->affected_rows;
        echo "<p style='color: green; font-size: 18px;'>✅ Successfully approved $affected news articles!</p>";
        echo "<p><a href='approve_news_correct.php'>Refresh</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Error: " . $connection->error . "</p>";
    }
} else {
    // Show options
    if ($unapproved > 0) {
        echo "<h2>Choose Action:</h2>";
        echo "<p>";
        echo "<a href='?approve=20' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; margin-right: 10px; display: inline-block; margin-bottom: 10px;'>Approve 20 Recent</a>";
        echo "<a href='?approve=50' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; margin-right: 10px; display: inline-block; margin-bottom: 10px;'>Approve 50 Recent</a>";
        echo "<a href='?approve=all' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; margin-bottom: 10px;'>Approve All</a>";
        echo "</p>";
        
        // Show recent unapproved news
        echo "<h2>Recent Unapproved News (Preview):</h2>";
        $preview = $connection->query("SELECT id_news, title_news, date_news, author_news FROM news WHERE approved = 0 ORDER BY date_news DESC LIMIT 10");
        if ($preview && $preview->num_rows > 0) {
            echo "<table border='1' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
            echo "<tr style='background: #f8f9fa;'><th style='padding: 10px;'>ID</th><th style='padding: 10px;'>Title</th><th style='padding: 10px;'>Author</th><th style='padding: 10px;'>Date</th></tr>";
            while ($row = $preview->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='padding: 8px;'>" . $row['id_news'] . "</td>";
                echo "<td style='padding: 8px;'>" . htmlspecialchars(mb_substr($row['title_news'], 0, 60)) . (mb_strlen($row['title_news']) > 60 ? '...' : '') . "</td>";
                echo "<td style='padding: 8px;'>" . htmlspecialchars($row['author_news'] ?? 'Unknown') . "</td>";
                echo "<td style='padding: 8px;'>" . date('Y-m-d H:i', strtotime($row['date_news'])) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: green; font-size: 18px;'>✅ All news articles are already approved!</p>";
        
        // Show some recent approved news
        echo "<h2>Recent Approved News:</h2>";
        $recent = $connection->query("SELECT id_news, title_news, date_news, url_news FROM news WHERE approved = 1 ORDER BY date_news DESC LIMIT 5");
        if ($recent && $recent->num_rows > 0) {
            echo "<ul>";
            while ($row = $recent->fetch_assoc()) {
                $url = $row['url_news'] ? "/news/" . $row['url_news'] : "#";
                echo "<li><a href='$url'>" . htmlspecialchars($row['title_news']) . "</a> - " . date('Y-m-d', strtotime($row['date_news'])) . "</li>";
            }
            echo "</ul>";
        }
    }
}

// Statistics
echo "<h2>News Statistics:</h2>";
$total = $approved + $unapproved;
echo "<p>Total news articles: <strong>$total</strong></p>";
if ($total > 0) {
    $approved_percent = round(($approved / $total) * 100, 1);
    echo "<p>Approval rate: <strong>$approved_percent%</strong></p>";
}

// Check for news with NULL approved status
$null_count_result = $connection->query("SELECT COUNT(*) as count FROM news WHERE approved IS NULL");
if ($null_count_result) {
    $null_count = $null_count_result->fetch_assoc()['count'];
    if ($null_count > 0) {
        echo "<p style='color: orange;'>⚠️ Found $null_count news articles with NULL approval status</p>";
        if (isset($_GET['fix_null']) && $_GET['fix_null'] == 'yes') {
            $fix_query = "UPDATE news SET approved = 0 WHERE approved IS NULL";
            if ($connection->query($fix_query)) {
                $affected = $connection->affected_rows;
                echo "<p style='color: green;'>✅ Set $affected news articles to unapproved status</p>";
                echo "<p><a href='approve_news_correct.php'>Refresh</a></p>";
            }
        } else {
            echo "<p><a href='?fix_null=yes' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none;'>Fix NULL Values</a></p>";
        }
    }
}

echo "<hr>";
echo "<p><a href='/'>← Homepage</a> | <a href='/site_review.php'>Site Review</a> | <a href='/news'>View News</a></p>";

$connection->close();
?>