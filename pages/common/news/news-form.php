<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Default values for variables
$pageTitle = 'Create/Edit News';
$metaD = 'Create or edit a news article';
$metaK = 'news, create, edit';

$mainContent = 'news-form-content.php';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-dashboard.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK);
