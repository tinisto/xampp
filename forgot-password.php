<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Восстановление пароля - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/pages/forgot-password/forgot-password-content.php';
$metaD = 'Восстановление пароля для аккаунта на сайте 11классники.ru';
$metaK = 'восстановление пароля, забыли пароль, сброс пароля';

// Auth page options - no header/footer
$options = [
    'header' => false,
    'footer' => false,
    'robotsMeta' => 'noindex,nofollow',
    'analytics' => false,
    'container' => 'd-flex justify-content-center align-items-center min-vh-100',
    'css' => ['styles.css', 'authorization.css']
];

include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template.php';
renderTemplate($pageTitle, $mainContent, [], $metaD, $metaK, '', '', '', $options);
?>