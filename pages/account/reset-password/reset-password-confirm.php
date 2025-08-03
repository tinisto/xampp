<?php
$pageTitle = 'Подтверждение сброса пароля';

// Template configuration
$templateConfig = [
    'layoutType' => 'auth',
    'cssFramework' => 'custom',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'noHeader' => true,
    'noFooter' => true
];

// Render template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, 'pages/account/reset-password/reset-password-confirm-content.php', $templateConfig);