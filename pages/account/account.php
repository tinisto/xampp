<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";
$mainContent = 'account-content.php';
$pageTitle = 'Аккаунт';
include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-nofollow.php';
renderTemplate($pageTitle, $mainContent);
