<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

$metaDescription = "";
$metaKeywords = [];
$additionalData = [];
$mainContent = "";

$pageTitle = "admin Создать страницу VPO";
$mainContent = $_SERVER["DOCUMENT_ROOT"] . "/pages/common/vpo/vpo-create-form.php";
include $_SERVER["DOCUMENT_ROOT"] . "/common-components/template-engine.php";
renderTemplate($pageTitle, $mainContent, $additionalData, $metaDescription, $metaKeywords);
