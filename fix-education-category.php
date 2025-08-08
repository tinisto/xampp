<?php
// Fix duplicate education category issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fix Education Category Duplicates</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

echo "<h3>1. Current situation:</h3>";
$checkQuery = "SELECT category_news, COUNT(*) as count 
               FROM news 
               WHERE category_news IN ('4', 'education') 
               AND approved = 1";

$result = mysqli_query($connection, $checkQuery);
if ($result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>category_news</th><th>Article Count</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['category_news']) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>2. Articles with string 'education' value:</h3>";
$educationQuery = "SELECT id, title_news, url_slug, category_news 
                   FROM news 
                   WHERE category_news = 'education'";

$result = mysqli_query($connection, $educationQuery);
if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Current category_news</th></tr>";
    
    $articlesToFix = [];
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td style='background: #ffcccc;'>" . htmlspecialchars($row['category_news']) . "</td>";
        echo "</tr>";
        
        $articlesToFix[] = $row['id'];
    }
    echo "</table>";
    
    if (!empty($articlesToFix)) {
        echo "<h3>3. Fix the duplicate categories:</h3>";
        
        echo "<p><strong>Option 1: Convert 'education' to '4' (Recommended)</strong></p>";
        echo "<p>This will standardize all education articles to use numeric category '4'</p>";
        
        echo "<form method='post' style='background: #f0f8ff; padding: 15px; margin: 10px 0; border: 1px solid #0066cc;'>";
        echo "<p>Click to convert " . count($articlesToFix) . " articles from category_news = 'education' to category_news = '4':</p>";
        echo "<input type='hidden' name='action' value='fix_education'>";
        echo "<input type='submit' value='Fix Education Categories' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>";
        echo "</form>";
        
        if ($_POST['action'] === 'fix_education') {
            echo "<h4>🔧 Applying fix...</h4>";
            
            $updateQuery = "UPDATE news SET category_news = '4' WHERE category_news = 'education'";
            $updateResult = mysqli_query($connection, $updateQuery);
            
            if ($updateResult) {
                $affected = mysqli_affected_rows($connection);
                echo "<p style='color: green;'>✅ Successfully updated $affected articles</p>";
                echo "<p>All education articles now use category_news = '4'</p>";
                
                // Verify the fix
                $verifyQuery = "SELECT COUNT(*) as count FROM news WHERE category_news = 'education'";
                $verifyResult = mysqli_query($connection, $verifyQuery);
                $verifyRow = mysqli_fetch_assoc($verifyResult);
                
                if ($verifyRow['count'] == 0) {
                    echo "<p style='color: green;'>✅ Verification: No more articles with category_news = 'education'</p>";
                } else {
                    echo "<p style='color: red;'>⚠️ Verification: Still " . $verifyRow['count'] . " articles with category_news = 'education'</p>";
                }
                
                // Show new totals
                echo "<h4>Updated totals:</h4>";
                $newTotalQuery = "SELECT COUNT(*) as count FROM news WHERE category_news = '4' AND approved = 1";
                $newTotalResult = mysqli_query($connection, $newTotalQuery);
                $newTotalRow = mysqli_fetch_assoc($newTotalResult);
                echo "<p>Education articles (category_news = '4'): <strong>" . $newTotalRow['count'] . "</strong></p>";
                
            } else {
                echo "<p style='color: red;'>❌ Update failed: " . mysqli_error($connection) . "</p>";
            }
        }
    }
    
} else {
    echo "<p style='color: green;'>✅ No articles found with category_news = 'education'</p>";
    echo "<p>All education articles are already using the correct numeric value '4'</p>";
}

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>