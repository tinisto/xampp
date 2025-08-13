<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Тесты - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/tests-content.php';
$metaD = 'Онлайн тесты для подготовки к ЕГЭ, ОГЭ и профориентации. Проверьте свои знания и подготовьтесь к экзаменам.';
$metaK = 'онлайн тесты, ЕГЭ, ОГЭ, профориентация, подготовка к экзаменам, проверка знаний';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK);
?>