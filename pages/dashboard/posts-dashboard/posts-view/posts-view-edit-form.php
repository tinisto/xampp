<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
$mainContent = 'posts-view-edit-form-content.php';
$pageTitle = 'Edit Post';
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-dashboard.php';
renderTemplate($pageTitle, $mainContent);
