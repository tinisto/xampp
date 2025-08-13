<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'school-single-data-fetch.php';

if (isset($pageTitle)) {
    $additionalData = ['row' => $row];
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/school/school-single-content-modern.php';
    $content = ob_get_clean();
    
    // Pass content in additionalData
    $additionalData['content'] = $content;
    
    $metaD = $pageTitle . ' – образовательное учреждение, предоставляющее высококачественное образование. Узнайте больше о наших программах и возможностях обучения.';
    $metaK = $pageTitle . ', образование, обучение, школьники, 11-классники, адрес, руководство, директор, новости, сайт, электронная почта';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
    renderTemplate($pageTitle, '', $additionalData, $metaD, $metaK);
} else {
    header("Location: /404");
    exit();
}
