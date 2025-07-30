<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'post-data-fetch.php';
$mainContent = 'pages/post/post-content.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent, ['metaD' => $metaD, 'metaK' => $metaK, 'postData' => $postData]);
