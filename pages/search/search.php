<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
$mainContent = 'search-content.php';
$pageTitle = 'Поиск - 11-классники';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent);
