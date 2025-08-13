<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'test-handler-content.php';
$pageTitle = 'Тест';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, $mainContent);
