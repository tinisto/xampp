<?php
/**
 * Debug News Categories - Check for news-specific category tables
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<h1>News Categories Debug</h1>";
echo "<p>Looking for news-specific category tables and data...</p>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset("utf8mb4");
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<h2>All Database Tables:</h2>";
    $tables_query = "SHOW TABLES";
    $tables_result = $connection->query($tables_query);
    
    $all_tables = [];
    $news_tables = [];
    
    while ($row = $tables_result->fetch_row()) {
        $table_name = $row[0];
        $all_tables[] = $table_name;
        
        if (stripos($table_name, 'news') !== false) {
            $news_tables[] = $table_name;
            echo "<p>📰 <strong>$table_name</strong> (news-related)</p>";
        } elseif (stripos($table_name, 'categor') !== false) {
            echo "<p>📁 <strong>$table_name</strong> (category-related)</p>";
        }
    }
    
    echo "<h2>News-Related Tables:</h2>";
    if (empty($news_tables)) {
        echo "<p>❌ No dedicated news category tables found</p>";
        echo "<p>🔍 This suggests all categories (including news) are in the main 'categories' table</p>";
    } else {
        foreach ($news_tables as $table) {
            echo "<h3>Table: $table</h3>";
            
            $structure_query = "DESCRIBE $table";
            $structure_result = $connection->query($structure_query);
            
            echo "<h4>Structure:</h4><ul>";
            while ($field = $structure_result->fetch_assoc()) {
                echo "<li><strong>" . $field['Field'] . "</strong> (" . $field['Type'] . ")</li>";
            }
            echo "</ul>";
            
            $count_query = "SELECT COUNT(*) as total FROM $table";
            $count_result = $connection->query($count_query);
            $count = $count_result->fetch_assoc()['total'];
            echo "<p><strong>Total records:</strong> $count</p>";
            
            if ($count > 0) {
                $data_query = "SELECT * FROM $table LIMIT 10";
                $data_result = $connection->query($data_query);
                
                echo "<h4>Sample Data:</h4>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                
                $fields = $data_result->fetch_fields();
                echo "<tr style='background: #f0f0f0;'>";
                foreach ($fields as $field) {
                    echo "<th style='padding: 5px;'>" . $field->name . "</th>";
                }
                echo "</tr>";
                
                $data_result->data_seek(0);
                while ($row = $data_result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td style='padding: 5px; border: 1px solid #ccc;'>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }
    
    echo "<h2>News Categories in Main Categories Table:</h2>";
    echo "<p>Searching for categories that might be news-related...</p>";
    
    $news_search = [
        "WHERE category_name LIKE '%новост%'",
        "WHERE category_name LIKE '%news%'",
        "WHERE title_category LIKE '%новост%'",
        "WHERE title_category LIKE '%news%'",
        "WHERE url_category LIKE '%news%'",
        "WHERE url_slug LIKE '%news%'"
    ];
    
    foreach ($news_search as $condition) {
        $query = "SELECT id_category, category_name, title_category, url_category, url_slug FROM categories $condition";
        $result = $connection->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo "<h4>Found with: $condition</h4>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th style='padding: 5px;'>ID</th>";
            echo "<th style='padding: 5px;'>Category Name</th>";
            echo "<th style='padding: 5px;'>Title</th>";
            echo "<th style='padding: 5px;'>URL Category</th>";
            echo "<th style='padding: 5px;'>URL Slug</th>";
            echo "<th style='padding: 5px;'>Link</th>";
            echo "</tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='padding: 5px;'>" . $row['id_category'] . "</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($row['category_name']) . "</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($row['title_category']) . "</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($row['url_category']) . "</td>";
                echo "<td style='padding: 5px;'>" . htmlspecialchars($row['url_slug']) . "</td>";
                echo "<td style='padding: 5px;'>";
                echo "<a href='/category/" . htmlspecialchars($row['url_category']) . "' target='_blank'>Test</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h2>Summary:</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #ccc; border-radius: 5px;'>";
    echo "<p><strong>Total tables found:</strong> " . count($all_tables) . "</p>";
    echo "<p><strong>News-related tables:</strong> " . count($news_tables) . "</p>";
    echo "<p><strong>Recommendation:</strong> ";
    if (empty($news_tables)) {
        echo "All categories (including news) appear to be in the main 'categories' table. No separate news category system exists.";
    } else {
        echo "Separate news category system found. Check the tables above.";
    }
    echo "</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3, h4 { color: #333; }
table { margin: 10px 0; }
</style>