<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Онлайн тесты - Проверьте свои знания';
$metaD = 'Пройдите бесплатные онлайн тесты по различным предметам: IQ тест, математика, русский язык, профориентация и многое другое';
$metaK = 'онлайн тесты, IQ тест, тесты знаний, профориентация, математика, русский язык';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'metaD' => $metaD,
    'metaK' => $metaK
];

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, 'pages/tests/tests-main-content-fixed.php', $templateConfig);
?>