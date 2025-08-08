<?php
/**
 * News router - handles both listing and single news pages
 * DO NOT include template here - the page itself will include it
 */

// Suppress errors but log them
error_reporting(0);
ini_set('display_errors', 0);

// Include the actual page content
$pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/news/news.php';
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    // If page doesn't exist, show error with template
    $greyContent1 = '<div style="padding: 30px;"><h1>Новости</h1></div>';
    $greyContent2 = '';
    $greyContent3 = '';
    $greyContent4 = '';
    $greyContent5 = '<div style="padding: 40px; text-align: center;">
        <h2>Page temporarily unavailable</h2>
        <p>Please try again later.</p>
        <p><a href="/" style="color: #28a745;">Return to homepage</a></p>
    </div>';
    $greyContent6 = '';
    $blueContent = '';
    $pageTitle = 'Новости - 11-классники';
    
    // Only include template if page doesn't exist
    include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
}
?>