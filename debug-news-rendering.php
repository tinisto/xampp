<?php
// Debug news rendering - step by step
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>News Rendering Debug</h2>";

// Simulate what happens when accessing /news/miit-snova-smenil-imya
$_GET['url_news'] = 'miit-snova-smenil-imya';

echo "<p>Simulating: /news/miit-snova-smenil-imya</p>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h3>Step 1: Database Query</h3>";
$newsUrl = $_GET['url_news'] ?? '';
echo "<p>URL parameter: <strong>$newsUrl</strong></p>";

$query = "SELECT n.*, c.title_category, c.url_category
          FROM news n
          LEFT JOIN categories c ON n.category_news = c.id_category
          WHERE n.url_slug = ? AND n.approved = 1";

$stmt = $connection->prepare($query);
$stmt->bind_param("s", $newsUrl);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if ($news) {
    echo "<p>✅ Article found: <strong>" . htmlspecialchars($news['title_news']) . "</strong></p>";
    echo "<p>Content length: " . strlen($news['text_news']) . " characters</p>";
    echo "<p>Has content: " . (empty($news['text_news']) ? "❌ EMPTY" : "✅ YES") . "</p>";
} else {
    echo "<p>❌ Article not found</p>";
    exit;
}

echo "<h3>Step 2: Template Variables</h3>";

try {
    // Section 1: Title
    ob_start();
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php')) {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/real_title.php';
        renderRealTitle($news['title_news'], [
            'fontSize' => '32px',
            'margin' => '30px 0'
        ]);
        echo "<p>✅ Title component loaded</p>";
    } else {
        echo "<p>❌ Title component missing</p>";
    }
    $greyContent1 = ob_get_clean();
    echo "<p>Section 1 length: " . strlen($greyContent1) . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Title error: " . $e->getMessage() . "</p>";
}

try {
    // Section 5: Main content
    ob_start();
    ?>
    <div style="padding: 30px 20px;">        
        <div style="font-size: 16px; line-height: 1.8; color: #333;">
            <?= $news['text_news'] ?>
        </div>
        
        <hr style="margin: 20px 0;">
        <h4>Debug Info:</h4>
        <p><strong>Raw content:</strong></p>
        <pre style="background: #f4f4f4; padding: 10px; max-height: 200px; overflow: auto;"><?= htmlspecialchars($news['text_news']) ?></pre>
    </div>
    <?php
    $greyContent5 = ob_get_clean();
    echo "<p>✅ Main content created</p>";
    echo "<p>Section 5 length: " . strlen($greyContent5) . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Content error: " . $e->getMessage() . "</p>";
}

echo "<h3>Step 3: Template Loading</h3>";

// Set other required variables
$greyContent2 = '<p>Breadcrumb test</p>';
$greyContent3 = '<p>Metadata test</p>';
$greyContent4 = '';
$greyContent6 = '';
$blueContent = '<p>Comments test</p>';
$pageTitle = $news['title_news'];

echo "<p>All template variables set:</p>";
echo "<ul>";
echo "<li>pageTitle: " . (isset($pageTitle) ? "✅" : "❌") . "</li>";
echo "<li>greyContent1: " . (isset($greyContent1) ? "✅ (" . strlen($greyContent1) . ")" : "❌") . "</li>";
echo "<li>greyContent2: " . (isset($greyContent2) ? "✅ (" . strlen($greyContent2) . ")" : "❌") . "</li>";
echo "<li>greyContent3: " . (isset($greyContent3) ? "✅ (" . strlen($greyContent3) . ")" : "❌") . "</li>";
echo "<li>greyContent4: " . (isset($greyContent4) ? "✅ (" . strlen($greyContent4) . ")" : "❌") . "</li>";
echo "<li>greyContent5: " . (isset($greyContent5) ? "✅ (" . strlen($greyContent5) . ")" : "❌") . "</li>";
echo "<li>greyContent6: " . (isset($greyContent6) ? "✅ (" . strlen($greyContent6) . ")" : "❌") . "</li>";
echo "<li>blueContent: " . (isset($blueContent) ? "✅ (" . strlen($blueContent) . ")" : "❌") . "</li>";
echo "</ul>";

// Test if template exists
$templateFile = $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
if (file_exists($templateFile)) {
    echo "<p>✅ Template file exists: $templateFile</p>";
    
    // Show a preview instead of including (to avoid output)
    echo "<h3>Step 4: Template Preview</h3>";
    echo "<div style='border: 2px solid #28a745; padding: 20px; margin: 20px 0;'>";
    echo "<h4>What the template should render:</h4>";
    echo "<div><strong>Title Section:</strong><br>" . $greyContent1 . "</div>";
    echo "<div><strong>Content Section:</strong><br>" . $greyContent5 . "</div>";
    echo "</div>";
    
    echo "<p><strong>Next step:</strong> The template rendering should work. Issue might be in the actual news-single.php file having errors.</p>";
    
} else {
    echo "<p>❌ Template file missing: $templateFile</p>";
}

mysqli_close($connection);
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
</style>