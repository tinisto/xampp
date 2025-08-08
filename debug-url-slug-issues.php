<?php
// Debug tool to identify and fix URL slug issues causing 404s
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug: URL Slug Issues Causing 404</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (!$connection) {
    die("❌ Database connection failed");
}

echo "<h3>1. Test specific problematic articles:</h3>";
$testArticles = [
    '621' => 'dasdasdada--adadad-a-dasdasda',
    '620' => 'sdfdsfd', 
    '617' => '11'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>URL Slug</th><th>Title</th><th>Database Check</th><th>Issues</th><th>Test Link</th></tr>";

foreach ($testArticles as $id => $slug) {
    $query = "SELECT id, title_news, url_slug, approved FROM news WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $issues = [];
        
        // Check for potential slug issues
        if (strpos($row['url_slug'], '--') !== false) {
            $issues[] = "Double dashes in URL";
        }
        if (is_numeric($row['url_slug'])) {
            $issues[] = "Numeric-only URL (conflicts with pagination)";
        }
        if (strlen($row['url_slug']) < 3) {
            $issues[] = "URL too short";
        }
        if ($row['approved'] != 1) {
            $issues[] = "Not approved (approved=" . $row['approved'] . ")";
        }
        
        $issueText = empty($issues) ? "No obvious issues" : implode(", ", $issues);
        $issueColor = empty($issues) ? "#d4edda" : "#f8d7da";
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td style='color: green;'>✅ Found in DB</td>";
        echo "<td style='background: $issueColor;'>" . $issueText . "</td>";
        echo "<td><a href='/news/" . htmlspecialchars($row['url_slug']) . "' target='_blank'>Test</a></td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "<td>$id</td>";
        echo "<td>$slug</td>";
        echo "<td>-</td>";
        echo "<td style='color: red;'>❌ NOT FOUND</td>";
        echo "<td style='background: #f8d7da;'>Article missing from database</td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
}
echo "</table>";

echo "<h3>2. Check news-single.php routing logic:</h3>";
echo "<p>Let's test what happens when we access these URLs...</p>";

// Test the routing logic manually
$testSlugs = ['dasdasdada--adadad-a-dasdasda', 'sdfdsfd', '11'];

foreach ($testSlugs as $testSlug) {
    echo "<h4>Testing slug: '$testSlug'</h4>";
    
    // Simulate what news.php does
    $categoryUrls = ['novosti-vuzov', 'novosti-spo', 'novosti-shkol', 'novosti-obrazovaniya'];
    $isSingleArticle = !in_array($testSlug, $categoryUrls);
    
    echo "<p>is single article: " . ($isSingleArticle ? 'YES' : 'NO') . "</p>";
    
    if ($isSingleArticle) {
        // Test the query that news-single.php would run
        $query = "SELECT id, title_news, text_news, url_slug, date_news, view_news, category_news, approved
                  FROM news 
                  WHERE url_slug = ? AND approved = 1";
        
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "s", $testSlug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            echo "<p style='color: green;'>✅ Query would find article: '" . htmlspecialchars($row['title_news']) . "'</p>";
            echo "<p>approved: " . $row['approved'] . ", category_news: " . $row['category_news'] . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Query would return no results</p>";
            
            // Try without approved=1 filter
            $query2 = "SELECT id, title_news, url_slug, approved FROM news WHERE url_slug = ?";
            $stmt2 = mysqli_prepare($connection, $query2);
            mysqli_stmt_bind_param($stmt2, "s", $testSlug);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            
            if ($row2 = mysqli_fetch_assoc($result2)) {
                echo "<p style='color: orange;'>⚠️ Article exists but approved=" . $row2['approved'] . " (needs to be 1)</p>";
            } else {
                echo "<p style='color: red;'>❌ Article doesn't exist with this URL slug</p>";
            }
        }
    }
    
    echo "<hr>";
}

echo "<h3>3. Quick fix for approval status:</h3>";
echo "<p>If articles exist but have approved != 1, we can fix that:</p>";

if ($_POST['action'] === 'fix_approval') {
    $idsToFix = $_POST['article_ids'] ?? [];
    
    foreach ($idsToFix as $id) {
        $id = intval($id);
        $updateQuery = "UPDATE news SET approved = 1 WHERE id = ?";
        $stmt = mysqli_prepare($connection, $updateQuery);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>✅ Fixed approval status for article ID $id</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to fix article ID $id</p>";
        }
    }
    
    echo "<p><strong>Refresh page to see updated results.</strong></p>";
}

// Check which articles need approval fixing
$needsApprovalQuery = "SELECT id, title_news, url_slug, approved FROM news WHERE id IN (621, 620, 617) AND approved != 1";
$result = mysqli_query($connection, $needsApprovalQuery);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<form method='post' style='background: #f0f8ff; padding: 15px; border: 1px solid #0066cc;'>";
    echo "<p><strong>Articles that need approval status fixed:</strong></p>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div>";
        echo "<input type='checkbox' name='article_ids[]' value='" . $row['id'] . "' id='article_" . $row['id'] . "'>";
        echo "<label for='article_" . $row['id'] . "'>ID " . $row['id'] . ": " . htmlspecialchars($row['title_news']) . " (approved=" . $row['approved'] . ")</label>";
        echo "</div>";
    }
    
    echo "<input type='hidden' name='action' value='fix_approval'>";
    echo "<br><input type='submit' value='Fix Approval Status' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>";
    echo "</form>";
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