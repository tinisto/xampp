<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = '11-классники';
$mainContent = 'index_content.php';
$metaD = 'Образовательный портал для учеников 11 классов. Новости образования, подготовка к ЕГЭ, выбор вуза.';
$metaK = '11 класс, ЕГЭ, образование, школа, университет, поступление';

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK);
