<?php
/**
 * Apply News Category Fixes
 * 1. Deactivate education-news category (has 0 posts)
 * 2. Update .htaccess to redirect /category/education-news to /news
 * 3. Remove education-news from dropdown
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

echo "<h1>Applying News Category Fixes</h1>";

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->set_charset("utf8mb4");
    
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    // Step 1: Deactivate education-news category
    echo "<h2>Step 1: Deactivating education-news category</h2>";
    $update_query = "UPDATE categories SET is_active = 0 WHERE id_category = 1 AND url_category = 'education-news'";
    
    if ($connection->query($update_query)) {
        echo "<p style='color: green;'>✓ Successfully deactivated education-news category (had 0 posts)</p>";
        
        // Verify the change
        $verify_query = "SELECT id_category, title_category, is_active FROM categories WHERE id_category = 1";
        $result = $connection->query($verify_query);
        $category = $result->fetch_assoc();
        
        echo "<p><strong>Verification:</strong> Category '{$category['title_category']}' - Active: " . ($category['is_active'] ? 'Yes' : 'No') . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Error deactivating category: " . $connection->error . "</p>";
    }
    
    // Step 2: Check current .htaccess and recommend changes
    echo "<h2>Step 2: .htaccess Update Required</h2>";
    echo "<p>Add this rule to .htaccess <strong>before</strong> the general category rule:</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
    echo "# Redirect education-news category to news section\n";
    echo "RewriteRule ^category/education-news/?$ /news [R=301,L]\n";
    echo "</pre>";
    
    // Step 3: Show current active categories for dropdown
    echo "<h2>Step 3: Updated Categories for Dropdown</h2>";
    echo "<p>After deactivating education-news, these categories will appear in dropdown:</p>";
    
    $active_cats_query = "SELECT id_category, title_category, url_category FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, title_category ASC";
    $active_result = $connection->query($active_cats_query);
    
    if ($active_result && $active_result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>ID</th>";
        echo "<th style='padding: 8px;'>Title</th>";
        echo "<th style='padding: 8px;'>URL</th>";
        echo "<th style='padding: 8px;'>Link</th>";
        echo "</tr>";
        
        while ($row = $active_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . $row['id_category'] . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['title_category']) . "</td>";
            echo "<td style='padding: 5px;'>" . htmlspecialchars($row['url_category']) . "</td>";
            echo "<td style='padding: 5px;'>";
            echo "<a href='/category/" . htmlspecialchars($row['url_category']) . "' target='_blank'>Test</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total active categories:</strong> " . $active_result->num_rows . "</p>";
    }
    
    // Step 4: News system summary
    echo "<h2>Step 4: News System Summary</h2>";
    echo "<p><strong>News articles are in separate system:</strong></p>";
    echo "<ul>";
    echo "<li><strong>News Table:</strong> 501 articles with category_news field</li>";
    echo "<li><strong>News Categories:</strong> 1 (243 articles), 4 (150 articles), 2 (96 articles), 3 (6 articles), education (1 article)</li>";
    echo "<li><strong>Access via:</strong> /news URLs (not /category)</li>";
    echo "</ul>";
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>✅ Fix Summary:</h3>";
    echo "<ol>";
    echo "<li><strong>education-news category deactivated</strong> - it had 0 posts anyway</li>";
    echo "<li><strong>Dropdown will no longer show education-news</strong> - automatic (only shows active categories)</li>";
    echo "<li><strong>Need to add .htaccess redirect</strong> - copy the rule above</li>";
    echo "<li><strong>News system works separately</strong> - 501 articles available via /news</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2, h3 { color: #333; }
table { margin: 10px 0; }
pre { font-family: monospace; }
</style>