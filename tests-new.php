<?php
// Tests router - updated to use real_template
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
    // Tests listing - use real template
    include $_SERVER['DOCUMENT_ROOT'] . '/tests-main-real.php';
}
?>