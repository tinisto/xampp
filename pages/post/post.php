<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

include 'post-data-fetch.php';

if (isset($pageTitle)) {
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post-content-modern.php';
    $content = ob_get_clean();
    
    $additionalData = ['content' => $content];
    
    include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
    renderTemplate($pageTitle, '', $additionalData, $metaD, $metaK);
} else {
    header("Location: /404");
    exit();
}
