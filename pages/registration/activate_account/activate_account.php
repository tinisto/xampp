<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Активация аккаунта - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/pages/registration/activate_account/activate_account_content.php';
$metaD = 'Активация аккаунта пользователя на сайте 11классники.ru';
$metaK = 'активация аккаунта, подтверждение регистрации';

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