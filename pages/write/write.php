<?php
$mainContent = 'pages/write/write-content.php';
$pageTitle = 'Напишите нам';
$templateConfig = [
    'layoutType' => 'default',
    'cssFramework' => 'custom',  // Use unified CSS framework
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'pageHeader' => [
        'title' => 'Напишите нам',
        'showSearch' => false
    ]
];
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);
