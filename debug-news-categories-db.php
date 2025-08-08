<?php
echo "<h3>News Categories Analysis</h3>";

try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection) {
        echo "<p>✅ Database connection successful</p>";
        
        // Check news table structure
        echo "<h4>News Table Structure:</h4>";
        $structure = mysqli_query($connection, "DESCRIBE news");
        if ($structure) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = mysqli_fetch_assoc($structure)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . ($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Check what values exist in news_type field
        echo "<h4>News Types (news_type field):</h4>";
        $newsTypes = mysqli_query($connection, "SELECT news_type, COUNT(*) as count FROM news GROUP BY news_type ORDER BY count DESC");
        if ($newsTypes && mysqli_num_rows($newsTypes) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>News Type</th><th>Count</th></tr>";
            while ($row = mysqli_fetch_assoc($newsTypes)) {
                echo "<tr><td>" . ($row['news_type'] ?? 'NULL') . "</td><td>{$row['count']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No news_type field found or query failed</p>";
        }
        
        // Check category_news field (if it exists)
        echo "<h4>News Categories (category_news field):</h4>";
        $categoryNews = mysqli_query($connection, "SELECT category_news, COUNT(*) as count FROM news GROUP BY category_news ORDER BY count DESC");
        if ($categoryNews && mysqli_num_rows($categoryNews) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Category News</th><th>Count</th></tr>";
            while ($row = mysqli_fetch_assoc($categoryNews)) {
                echo "<tr><td>" . ($row['category_news'] ?? 'NULL') . "</td><td>{$row['count']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No category_news field found or query failed</p>";
        }
        
        // Check category_id field (if it exists)
        echo "<h4>Category IDs (category_id field):</h4>";
        $categoryIds = mysqli_query($connection, "SELECT category_id, COUNT(*) as count FROM news GROUP BY category_id ORDER BY count DESC");
        if ($categoryIds && mysqli_num_rows($categoryIds) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Category ID</th><th>Count</th></tr>";
            while ($row = mysqli_fetch_assoc($categoryIds)) {
                echo "<tr><td>" . ($row['category_id'] ?? 'NULL') . "</td><td>{$row['count']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>❌ No category_id field found or query failed</p>";
        }
        
        // Show total news count
        echo "<h4>Total News:</h4>";
        $total = mysqli_query($connection, "SELECT COUNT(*) as total FROM news");
        if ($total) {
            $result = mysqli_fetch_assoc($total);
            echo "<p><strong>Total news articles: {$result['total']}</strong></p>";
        }
        
        // Show published news count
        echo "<h4>Published News:</h4>";
        $published = mysqli_query($connection, "SELECT COUNT(*) as total FROM news WHERE status = 'published'");
        if ($published) {
            $result = mysqli_fetch_assoc($published);
            echo "<p><strong>Published news articles: {$result['total']}</strong></p>";
        }
        
        // Show sample news entries
        echo "<h4>Sample News Entries (first 5):</h4>";
        $sample = mysqli_query($connection, "SELECT id_news, title_news, news_type, category_news, category_id, status, created_at FROM news ORDER BY created_at DESC LIMIT 5");
        if ($sample && mysqli_num_rows($sample) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
            echo "<tr><th>ID</th><th>Title</th><th>News Type</th><th>Category News</th><th>Category ID</th><th>Status</th><th>Created</th></tr>";
            while ($row = mysqli_fetch_assoc($sample)) {
                echo "<tr>";
                echo "<td>{$row['id_news']}</td>";
                echo "<td>" . substr($row['title_news'], 0, 50) . "...</td>";
                echo "<td>" . ($row['news_type'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['category_news'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['category_id'] ?? 'NULL') . "</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>