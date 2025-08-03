<?php
$pageTitle = 'Вход';
$mainContent = 'pages/login/login_content.php';
$additionalData = [
    'layoutType' => 'auth',
    'noIndex' => true,
    'metaD' => 'Войти в личный кабинет',
    'metaK' => 'вход, авторизация, логин'
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