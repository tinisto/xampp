<?php
// Simple category.php that bypasses complex dependencies

// Direct database connection
$connection = new mysqli('11klassnikiru67871.ipagemysql.com', '11klone_user', 'K8HqqBV3hTf4mha', '11klassniki_ru');
if ($connection->connect_error) {
    header("Location: /error");
    exit();
}
$connection->set_charset('utf8mb4');

// Include category data fetch
include 'category-data-fetch.php';

// Set up the page
$mainContent = 'pages/category/category-content-unified.php';

// Minimal template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD ?? '',
    'metaK' => $metaK ?? '',
    'categoryData' => $categoryData ?? []
];

// Check if template engine exists
$templatePath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
if (file_exists($templatePath)) {
    include $templatePath;
    if (function_exists('renderTemplate')) {
        renderTemplate($pageTitle ?? 'Category', $mainContent, $templateConfig);
    } else {
        // Fallback: include content directly
        include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent;
    }
} else {
    // Direct include fallback
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($pageTitle ?? 'Category') ?></title>
    </head>
    <body>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . $mainContent; ?>
    </body>
    </html>
    <?php
}
?>