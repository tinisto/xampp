<?php
// Cache-busting test version
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Copy of category-new.php
error_reporting(0);

// Set default content
$greyContent1 = '<div style="padding: 30px;"><h1>Категория</h1></div>';
$greyContent2 = '';
$greyContent3 = '';
$greyContent4 = '';
$greyContent5 = '<div style="padding: 20px;"><p>Loading...</p></div>';
$greyContent6 = '';
$blueContent = '';
$pageTitle = 'Категория';
$metaD = '';
$metaK = '';

// Force include with timestamp
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php?t=' . time();
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/category/category.php';
} else {
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Page temporarily unavailable</h2>
        <p>Please try again later.</p>
        <p><a href="/" style="color: #28a745;">Return to homepage</a></p>
    </div>';
}

// Ensure template exists
if (!isset($greyContent1)) {
    $greyContent1 = '<div style="padding: 30px;"><h1>Категория</h1></div>';
}

// Include template
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_template.php')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
} else {
    echo "Template not found";
}
?>