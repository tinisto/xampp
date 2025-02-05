<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include 'school-single-data-fetch.php';
$mainContent = 'school-single-content.php';
$metaD = $pageTitle . ' – образовательное учреждение, предоставляющее высококачественное образование. Узнайте больше о наших программах и возможностях обучения.';
$metaK = $pageTitle . ', образование, обучение, школьники, 11-классники, адрес, руководство, директор, новости, сайт, электронная почта';
$additionalData = ['row' => $row];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent, $additionalData, $metaD, $metaK);
