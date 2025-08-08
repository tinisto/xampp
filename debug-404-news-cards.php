<?php
// Debug tool to find the 5 news cards that lead to 404 errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug: 5 News Cards Leading to 404</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

echo "<h3>1. All category_news values in database:</h3>";
$categoryQuery = "SELECT category_news, COUNT(*) as count 
                  FROM news 
                  GROUP BY category_news 
                  ORDER BY category_news";

$result = mysqli_query($connection, $categoryQuery);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>category_news</th><th>Article Count</th><th>Navigation Support</th></tr>";
    
    $expectedCategories = ['1', '2', '3', '4'];
    $problematicCategories = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $category = $row['category_news'];
        $count = $row['count'];
        
        $isSupported = in_array($category, $expectedCategories) || $category === 'education';
        $statusColor = $isSupported ? '#d4edda' : '#f8d7da';
        $status = $isSupported ? '✅ Supported' : '❌ Not supported';
        
        if (!$isSupported) {
            $problematicCategories[] = $category;
        }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($category) . "</td>";
        echo "<td>" . $count . "</td>";
        echo "<td style='background: $statusColor;'>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>2. Articles with problematic categories (likely causing 404s):</h3>";
if (!empty($problematicCategories)) {
    $problemList = "'" . implode("', '", array_map('mysqli_real_escape_string', array_fill(0, count($problematicCategories), $connection), $problematicCategories)) . "'";
    
    $problemQuery = "SELECT id, title_news, url_slug, category_news, date_news
                     FROM news 
                     WHERE category_news IN ($problemList)
                     ORDER BY date_news DESC
                     LIMIT 10";
    
    $result = mysqli_query($connection, $problemQuery);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Problem Category</th><th>Date</th><th>Test Link</th></tr>";
        
        $problemArticles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
            echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
            echo "<td style='background: #ffcccc;'>'" . htmlspecialchars($row['category_news']) . "'</td>";
            echo "<td>" . $row['date_news'] . "</td>";
            echo "<td><a href='/news/" . htmlspecialchars($row['url_slug']) . "' target='_blank'>Test</a></td>";
            echo "</tr>";
            
            $problemArticles[] = $row;
        }
        echo "</table>";
        
        if (count($problemArticles) > 0) {
            echo "<h3>3. Fix these problematic categories:</h3>";
            echo "<p>These articles need their category_news values updated to valid categories (1,2,3,4).</p>";
            
            echo "<form method='post' style='background: #f0f8ff; padding: 15px; margin: 10px 0; border: 1px solid #0066cc;'>";
            echo "<p><strong>Quick Fix Options:</strong></p>";
            
            foreach ($problematicCategories as $cat) {
                echo "<div style='margin: 10px 0;'>";
                echo "<label>Convert category_news = '$cat' to: </label>";
                echo "<select name='fix_category[$cat]'>";
                echo "<option value=''>Select new category</option>";
                echo "<option value='1'>1 - Новости ВПО</option>";
                echo "<option value='2'>2 - Новости СПО</option>";
                echo "<option value='3'>3 - Новости школ</option>";
                echo "<option value='4'>4 - Новости образования</option>";
                echo "</select>";
                echo "</div>";
            }
            
            echo "<input type='hidden' name='action' value='fix_categories'>";
            echo "<input type='submit' value='Apply Category Fixes' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;'>";
            echo "</form>";
            
            // Process form submission
            if ($_POST['action'] === 'fix_categories' && isset($_POST['fix_category'])) {
                echo "<h4>🔧 Applying fixes...</h4>";
                
                foreach ($_POST['fix_category'] as $oldCat => $newCat) {
                    if (!empty($newCat)) {
                        $oldCat = mysqli_real_escape_string($connection, $oldCat);
                        $newCat = mysqli_real_escape_string($connection, $newCat);
                        
                        $updateQuery = "UPDATE news SET category_news = '$newCat' WHERE category_news = '$oldCat'";
                        $updateResult = mysqli_query($connection, $updateQuery);
                        
                        if ($updateResult) {
                            $affected = mysqli_affected_rows($connection);
                            echo "<p style='color: green;'>✅ Updated $affected articles from '$oldCat' to '$newCat'</p>";
                        } else {
                            echo "<p style='color: red;'>❌ Failed to update '$oldCat': " . mysqli_error($connection) . "</p>";
                        }
                    }
                }
                
                echo "<p><strong>Refresh the page to see updated results.</strong></p>";
            }
        }
        
    } else {
        echo "<p style='color: green;'>✅ No problematic categories found</p>";
    }
} else {
    echo "<p style='color: green;'>✅ All categories are supported by the navigation system</p>";
}

echo "<h3>4. Articles with NULL/empty categories:</h3>";
$nullQuery = "SELECT id, title_news, url_slug, category_news, date_news
              FROM news 
              WHERE category_news IS NULL OR category_news = '' OR category_news = '0'
              ORDER BY date_news DESC
              LIMIT 5";

$result = mysqli_query($connection, $nullQuery);
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Category Value</th><th>Date</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td style='background: #ffcccc;'>" . htmlspecialchars($row['category_news'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'>✅ No articles with NULL/empty categories</p>";
}

echo "<h3>5. Recent articles that might appear on /news page:</h3>";
$recentQuery = "SELECT id, title_news, url_slug, category_news, date_news
                FROM news 
                ORDER BY date_news DESC
                LIMIT 20";

$result = mysqli_query($connection, $recentQuery);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Category</th><th>Date</th><th>URL Status</th></tr>";
    
    $expectedCategories = ['1', '2', '3', '4', 'education'];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $isValidCategory = in_array($row['category_news'], $expectedCategories);
        $statusColor = $isValidCategory ? '#d4edda' : '#f8d7da';
        $status = $isValidCategory ? '✅ Valid' : '❌ 404 Risk';
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td>" . htmlspecialchars($row['category_news']) . "</td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "<td style='background: $statusColor;'>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
}

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
h3 { margin-top: 30px; }
</style>