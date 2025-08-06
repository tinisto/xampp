<?php
$mainContent = 'pages/404/404-content-modern.php';
$pageTitle = 'Страница не найдена';
$templateConfig = [
    'layoutType' => 'minimal',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true
];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
?>