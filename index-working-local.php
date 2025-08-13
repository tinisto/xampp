<?php
// Working index with local database
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use local database connection
require_once __DIR__ . '/database/db_connections_local.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mainContent = 'index_content_posts_with_news_style.php';
$pageTitle = 'Главная';

// Check if template engine exists
if (file_exists(__DIR__ . '/common-components/template-engine-ultimate.php')) {
    // Template configuration
    $templateConfig = [
        'layoutType' => 'default',
        'cssFramework' => 'custom',
        'headerType' => 'no-bootstrap',
        'darkMode' => true
    ];
    
    // Include template engine
    include __DIR__ . '/common-components/template-engine-ultimate.php';
    
    // Render the template
    renderTemplate($pageTitle, $mainContent, $templateConfig);
} else {
    // Fallback - render directly
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?> - 11klassniki.ru</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <?php include __DIR__ . '/common-components/header-local.php'; ?>
        
        <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
            <?php 
            if (file_exists($mainContent)) {
                include $mainContent;
            } else {
                echo "<h1>Welcome to 11klassniki.ru</h1>";
                echo "<p>Main content file not found, but the site is working!</p>";
                echo "<h2>Navigation:</h2>";
                echo "<ul>";
                echo "<li><a href='/tests'>Tests</a></li>";
                echo "<li><a href='/news'>News</a></li>";
                echo "<li><a href='/vpo-all-regions'>Universities (VPO)</a></li>";
                echo "<li><a href='/spo-all-regions'>Colleges (SPO)</a></li>";
                echo "<li><a href='/schools-all-regions'>Schools</a></li>";
                echo "</ul>";
            }
            ?>
        </div>
        
        <?php include __DIR__ . '/common-components/footer.php'; ?>
    </body>
    </html>
    <?php
}
?>