<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Онлайн тесты - Проверьте свои знания';
$mainContent = 'pages/tests/tests-main-content.php';
$additionalData = [
    'metaD' => 'Пройдите бесплатные онлайн тесты по различным предметам: IQ тест, математика, русский язык, профориентация и многое другое',
    'metaK' => 'онлайн тесты, IQ тест, тесты знаний, профориентация, математика, русский язык'
];

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';

// Template configuration
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];

renderTemplate($pageTitle, $mainContent, $templateConfig);
?>