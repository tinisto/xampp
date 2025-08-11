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
    // Tests listing - use existing tests main page
    $pageFile = $_SERVER['DOCUMENT_ROOT'] . '/pages/tests/tests-main.php';
    if (file_exists($pageFile)) {
        include $pageFile;
    } else {
        // Fallback - load content and use template engine
        require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
        
        $pageTitle = 'Онлайн тесты';
        $metaD = 'Пройдите бесплатные онлайн тесты по различным предметам: IQ тест, математика, русский язык, профориентация и многое другое';
        
        // Page configuration
        $pageConfig = [
            'metaD' => $metaD,
            'pageHeader' => [
                'title' => 'Онлайн тесты',
                'showSearch' => false
            ]
        ];
        
        // Render the page using the unified template
        renderTemplate($pageTitle, 'pages/tests/tests-main-content.php', $pageConfig);
    }
}
?>