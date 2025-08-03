<?php
// Approve news articles - Fixed version
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>News Approval Tool</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// First check if is_approved column exists
$check_column = $connection->query("SHOW COLUMNS FROM news LIKE 'is_approved'");
if (!$check_column || $check_column->num_rows == 0) {
    echo "<h2 style='color: red;'>⚠️ Column 'is_approved' does not exist in news table</h2>";
    
    if (isset($_GET['add_column']) && $_GET['add_column'] == 'yes') {
        // Add the column
        $add_column = "ALTER TABLE news ADD COLUMN is_approved TINYINT(1) DEFAULT 1";
        if ($connection->query($add_column)) {
            echo "<p style='color: green;'>✅ Added is_approved column to news table</p>";
            echo "<p><a href='approve_news_fixed.php'>Continue</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Error adding column: " . $connection->error . "</p>";
        }
    } else {
        echo "<p>The news table doesn't have an approval system. Would you like to add it?</p>";
        echo "<p><a href='?add_column=yes' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none;'>Add Approval Column</a></p>";
    }
} else {
    // Column exists, proceed with approval logic
    $unapproved_result = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 0");
    $approved_result = $connection->query("SELECT COUNT(*) as count FROM news WHERE is_approved = 1");
    
    $unapproved = $unapproved_result ? $unapproved_result->fetch_assoc()['count'] : 0;
    $approved = $approved_result ? $approved_result->fetch_assoc()['count'] : 0;
    
    echo "<h2>Current Status:</h2>";
    echo "<p>✅ Approved news: <strong>$approved</strong></p>";
    echo "<p>⏳ Unapproved news: <strong>$unapproved</strong></p>";
    
    // If all news are approved but count is 0, check if there are any news
    if ($approved == 0 && $unapproved == 0) {
        $total_news = $connection->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
        if ($total_news > 0) {
            echo "<p style='color: orange;'>⚠️ Found $total_news news articles with NULL approval status</p>";
            if (isset($_GET['fix_null']) && $_GET['fix_null'] == 'yes') {
                $fix_query = "UPDATE news SET is_approved = 1 WHERE is_approved IS NULL";
                if ($connection->query($fix_query)) {
                    $affected = $connection->affected_rows;
                    echo "<p style='color: green;'>✅ Fixed $affected news articles</p>";
                    echo "<p><a href='approve_news_fixed.php'>Refresh</a></p>";
                }
            } else {
                echo "<p><a href='?fix_null=yes' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none;'>Set All to Approved</a></p>";
            }
        } else {
            echo "<p>No news articles found in the database.</p>";
        }
    } elseif (isset($_GET['approve']) && $_GET['approve'] == 'all') {
        // Approve all news
        $approve_query = "UPDATE news SET is_approved = 1 WHERE is_approved = 0";
        if ($connection->query($approve_query)) {
            $affected = $connection->affected_rows;
            echo "<p style='color: green; font-size: 18px;'>✅ Successfully approved $affected news articles!</p>";
            echo "<p><a href='approve_news_fixed.php'>Refresh</a></p>";
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
            echo "<p><a href='approve_news_fixed.php'>Refresh</a></p>";
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
            
            // Show recent unapproved news
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
        } else {
            echo "<p style='color: green; font-size: 18px;'>✅ All news articles are already approved!</p>";
        }
    }
}

// Show all news table columns for debugging
echo "<h2>News Table Structure:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM news");
if ($cols) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    while ($col = $cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . $col['Field'] . "</td>";
        echo "<td style='padding: 5px;'>" . $col['Type'] . "</td>";
        echo "<td style='padding: 5px;'>" . $col['Null'] . "</td>";
        echo "<td style='padding: 5px;'>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<p><a href='/'>← Homepage</a> | <a href='/site_review.php'>Site Review</a></p>";

$connection->close();
?>