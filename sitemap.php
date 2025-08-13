<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Карта сайта - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/sitemap-content.php';
$metaD = 'Карта сайта 11классники.ru - все страницы и разделы сайта';
$metaK = 'карта сайта, навигация, страницы сайта, разделы';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK);
?>