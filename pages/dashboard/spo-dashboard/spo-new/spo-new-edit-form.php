<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";
$mainContent = "admin-approve-spo-edit-form-content.php";
$pageTitle = "Страница редактирования";
include $_SERVER["DOCUMENT_ROOT"] .
    "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent);
