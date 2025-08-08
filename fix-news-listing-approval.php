<?php
// Fix to ensure news listing only shows approved articles
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fix News Listing to Only Show Approved Articles</h2>";

// Show the fix that needs to be applied
echo "<h3>Current Issue:</h3>";
echo "<p>The main news listing page (/news) shows ALL articles regardless of approval status, but single article pages only display approved articles (approved=1).</p>";
echo "<p>This causes articles with approved=0 to appear in listings but return 404 when clicked.</p>";

echo "<h3>Fix to Apply:</h3>";
echo "<p>Update <code>/pages/common/news/news.php</code> to add approval filter to queries:</p>";

echo "<pre style='background: #f4f4f4; padding: 15px; border: 1px solid #ddd;'>";
echo htmlspecialchars("// Line ~143 - Update count query:
\$baseWhere = \$whereClause ? \$whereClause . \" AND approved = 1\" : \"WHERE approved = 1\";

// Line ~149 - Update main query:
\$query = \"SELECT id, title_news, url_slug, image_news, date_news, category_news 
          FROM news 
          {\$baseWhere}
          ORDER BY date_news DESC 
          LIMIT \$perPage OFFSET \$offset\";");
echo "</pre>";

echo "<h3>Or Apply This Automated Fix:</h3>";

if ($_POST['action'] === 'apply_fix') {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Backup original
        $backupPath = $filePath . '.backup_' . date('YmdHis');
        file_put_contents($backupPath, $content);
        echo "<p>✅ Created backup: " . basename($backupPath) . "</p>";
        
        // Fix 1: Update the baseWhere construction
        $oldPattern1 = '/\$baseWhere = \$whereClause \? \$whereClause : "WHERE 1=1";/';
        $newCode1 = '$baseWhere = $whereClause ? $whereClause . " AND approved = 1" : "WHERE approved = 1";';
        $content = preg_replace($oldPattern1, $newCode1, $content);
        
        // Save fixed file
        if (file_put_contents($filePath, $content)) {
            echo "<p style='color: green;'>✅ Successfully updated news.php to filter approved articles only</p>";
            echo "<p>Now only articles with approved=1 will appear in listings!</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to save changes</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ File not found: $filePath</p>";
    }
}

echo "<form method='post' style='background: #f0f8ff; padding: 15px; margin: 20px 0; border: 1px solid #0066cc;'>";
echo "<p><strong>This will update the news listing to only show approved articles (approved=1)</strong></p>";
echo "<p>This prevents unapproved articles from appearing in listings when they can't be accessed individually.</p>";
echo "<input type='hidden' name='action' value='apply_fix'>";
echo "<input type='submit' value='Apply Fix to News Listing' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>";
echo "</form>";

echo "<h3>Alternative: Approve the Problematic Articles</h3>";
echo "<p>Instead of hiding unapproved articles, you can approve them using the previous debug tool.</p>";
echo "<p><a href='/debug-url-slug-issues.php' style='color: #0066cc;'>Go back to approve articles →</a></p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { overflow-x: auto; }
h3 { margin-top: 30px; }
</style>