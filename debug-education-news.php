<?php
/**
 * Debug Education News Category Specifically
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<h1>Education News Category Debug</h1>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset("utf8mb4");
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<h2>Looking for 'education-news' category...</h2>";
    
    // Search by url_category
    $query1 = "SELECT * FROM categories WHERE url_category = 'education-news'";
    $result1 = $connection->query($query1);
    
    if ($result1 && $result1->num_rows > 0) {
        echo "<h3>✓ Found by url_category = 'education-news':</h3>";
        while ($row = $result1->fetch_assoc()) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            foreach ($row as $key => $value) {
                echo "<tr>";
                echo "<td style='padding: 5px; background: #f0f0f0; font-weight: bold;'>$key</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($value) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>❌ No category found with url_category = 'education-news'</p>";
    }
    
    // Search by url_slug
    $query2 = "SELECT * FROM categories WHERE url_slug = 'education-news'";
    $result2 = $connection->query($query2);
    
    if ($result2 && $result2->num_rows > 0) {
        echo "<h3>✓ Found by url_slug = 'education-news':</h3>";
        while ($row = $result2->fetch_assoc()) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            foreach ($row as $key => $value) {
                echo "<tr>";
                echo "<td style='padding: 5px; background: #f0f0f0; font-weight: bold;'>$key</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($value) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>❌ No category found with url_slug = 'education-news'</p>";
    }
    
    // Show all categories with their URLs
    echo "<h2>All Categories with URLs:</h2>";
    $query3 = "SELECT id_category, title_category, url_category, url_slug, category_name FROM categories WHERE is_active = 1 ORDER BY id_category";
    $result3 = $connection->query($query3);
    
    if ($result3 && $result3->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>ID</th>";
        echo "<th style='padding: 8px;'>Title</th>";
        echo "<th style='padding: 8px;'>URL Category</th>";
        echo "<th style='padding: 8px;'>URL Slug</th>";
        echo "<th style='padding: 8px;'>Category Name</th>";
        echo "<th style='padding: 8px;'>Test Link</th>";
        echo "</tr>";
        
        while ($row = $result3->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . $row['id_category'] . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['title_category']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['url_category']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['url_slug']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td style='padding: 5px;'>";
            echo "<a href='/category/" . htmlspecialchars($row['url_category']) . "' target='_blank'>Test</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check what posts exist for category ID 1 (which should be education-news)
    echo "<h2>Posts in Category ID 1 (Education News):</h2>";
    $query4 = "SELECT COUNT(*) as total FROM posts WHERE category = 1";
    $result4 = $connection->query($query4);
    
    if ($result4) {
        $count = $result4->fetch_assoc()['total'];
        echo "<p><strong>Total posts in category 1:</strong> $count</p>";
        
        if ($count > 0) {
            $query5 = "SELECT id, title, created_at FROM posts WHERE category = 1 LIMIT 5";
            $result5 = $connection->query($query5);
            
            echo "<h4>Sample posts:</h4><ul>";
            while ($post = $result5->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($post['title']) . " (ID: " . $post['id'] . ", " . $post['created_at'] . ")</li>";
            }
            echo "</ul>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
table { margin: 10px 0; }
</style>