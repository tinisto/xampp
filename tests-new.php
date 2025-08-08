<?php
// Tests router - updated
error_reporting(0);

// Check for single test
$testUrl = $_GET['url_test'] ?? '';

if (!empty($testUrl)) {
    // Single test page - use existing single test handler
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/test.php';
    if (file_exists($pageFile)) {
        include $pageFile;
    }
} else {
    // Tests listing - use NEW real template version
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/tests-main-real.php';
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        // Fallback
        $greyContent1 = '<div style="padding: 30px;"><h1>Тесты</h1></div>';
        $greyContent2 = '';
        $greyContent3 = '';
        $greyContent4 = '';
        $greyContent5 = '<div style="padding: 20px;"><p>Страница временно недоступна</p></div>';
        $greyContent6 = '';
        $blueContent = '';
        $pageTitle = 'Тесты - 11-классники';
        
        include $_SERVER['DOCUMENT_ROOT'] . '/real_template.php';
    }
}
?>