<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'error-content.php';
$pageTitle = 'Ошибка';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-no-header.php';
renderTemplate($pageTitle, $mainContent);
