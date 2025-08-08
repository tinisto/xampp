<?php
// Fixed router for category pages
// Force no caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Bypass any potential issues by including the working version directly
$_GET['category_en'] = isset($_GET['category_en']) ? $_GET['category_en'] : '';

// Include the category page
$categoryFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php';

// Add timestamp to force reload
clearstatcache(true, $categoryFile);

if (file_exists($categoryFile)) {
    // Force fresh include
    $uniqueVar = 'cat_' . uniqid();
    eval('$' . $uniqueVar . ' = 1;');
    include $categoryFile;
} else {
    // Fallback
    $greyContent1 = '<div style="padding: 30px;"><h1>Категория</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Category page not found</h2>
        <p>File: ' . htmlspecialchars($categoryFile) . '</p>
    </div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Категория';
    
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>