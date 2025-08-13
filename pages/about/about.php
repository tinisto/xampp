<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'О сайте 11-классники';

ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/pages/about/about_content_modern.php';
$content = ob_get_clean();

$additionalData = ['content' => $content];

$metaD = '11klassniki.ru - платформа для выпускников школ. Интервью с одиннадцатиклассниками, советы абитуриентам, база учебных заведений России.';
$metaK = '11klassniki, выпускники, абитуриенты, ЕГЭ, поступление, вузы, колледжи, образование';

include_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, '', $additionalData, $metaD, $metaK);
