<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'unauthorized-content.php';
$pageTitle = 'Доступ ограничен';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent);
