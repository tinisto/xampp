<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Новости - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/news-content.php';
$metaD = 'Последние новости образования, ЕГЭ, поступления в вузы и школьные события';
$metaK = 'новости образования, ЕГЭ новости, школьные новости, вузы новости, поступление';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK);
?>