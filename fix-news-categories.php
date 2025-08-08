<?php
/**
 * Fix News Categories System
 * Remove education-news from regular categories and set up proper news routing
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<h1>News Categories Fix</h1>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset("utf8mb4");
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    echo "<h2>Current Issue:</h2>";
    echo "<p>❌ 'education-news' category in regular categories table has 0 posts</p>";
    echo "<p>✅ News articles are in separate 'news' table with category_news field</p>";
    
    // Check news categories in news table
    echo "<h2>News Categories in News Table:</h2>";
    $news_cats_query = "SELECT category_news, COUNT(*) as count FROM news WHERE approved = 1 GROUP BY category_news ORDER BY count DESC";
    $news_cats_result = $connection->query($news_cats_query);
    
    if ($news_cats_result && $news_cats_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>News Category</th>";
        echo "<th style='padding: 8px;'>Count</th>";
        echo "<th style='padding: 8px;'>Suggested URL</th>";
        echo "</tr>";
        
        while ($row = $news_cats_result->fetch_assoc()) {
            $category = $row['category_news'];
            $count = $row['count'];
            $suggested_url = '';
            
            // Suggest URL based on category name
            if (stripos($category, 'ВПО') !== false || stripos($category, 'вуз') !== false) {
                $suggested_url = '/news/novosti-vpo';
            } elseif (stripos($category, 'СПО') !== false || stripos($category, 'колледж') !== false) {
                $suggested_url = '/news/novosti-spo';
            } elseif (stripos($category, 'школ') !== false) {
                $suggested_url = '/news/novosti-shkol';
            } elseif (stripos($category, 'образован') !== false) {
                $suggested_url = '/news/novosti-obrazovaniya';
            } else {
                $suggested_url = '/news/' . strtolower(str_replace([' ', ',', '.'], '-', $category));
            }
            
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($category) . "</td>";
            echo "<td style='padding: 5px;'>" . $count . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($suggested_url) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Recommendation:</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #ccc; border-radius: 5px;'>";
    echo "<h3>Option 1: Remove education-news from categories</h3>";
    echo "<p>Since it has 0 posts, we can safely remove it:</p>";
    echo "<code>UPDATE categories SET is_active = 0 WHERE id_category = 1;</code>";
    
    echo "<h3>Option 2: Redirect education-news to news system</h3>";
    echo "<p>Redirect /category/education-news to /news (main news page)</p>";
    
    echo "<h3>Option 3: Keep both systems separate</h3>";
    echo "<ul>";
    echo "<li><strong>/category/*</strong> - for regular articles/posts</li>";
    echo "<li><strong>/news/*</strong> - for news articles</li>";
    echo "</ul>";
    
    echo "<p><strong>News categories should be:</strong></p>";
    echo "<ul>";
    echo "<li>/news/novosti-vpo (ВПО news)</li>";
    echo "<li>/news/novosti-spo (СПО news)</li>";
    echo "<li>/news/novosti-shkol (School news)</li>";
    echo "<li>/news/novosti-obrazovaniya (Education news)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>Actions to take:</h2>";
    echo "<ol>";
    echo "<li><strong>Deactivate education-news category:</strong> <button onclick=\"deactivateCategory()\">Deactivate</button></li>";
    echo "<li><strong>Update .htaccess:</strong> Add redirect from /category/education-news to /news</li>";
    echo "<li><strong>Fix dropdown menu:</strong> Remove education-news from categories dropdown</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<script>
function deactivateCategory() {
    if (confirm('Are you sure you want to deactivate the education-news category?')) {
        fetch('fix-news-categories.php?action=deactivate', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            alert('Category deactivated!');
            location.reload();
        });
    }
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
table { margin: 10px 0; }
code { background: #f0f0f0; padding: 2px 5px; border-radius: 3px; }
button { padding: 5px 10px; background: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer; }
</style>

<?php
// Handle deactivation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'deactivate') {
    try {
        $update_query = "UPDATE categories SET is_active = 0 WHERE id_category = 1";
        $connection->query($update_query);
        echo "<p style='color: green;'>✓ Education-news category deactivated successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error deactivating category: " . $e->getMessage() . "</p>";
    }
}
?>