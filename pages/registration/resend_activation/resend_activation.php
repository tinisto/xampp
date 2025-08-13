<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$pageTitle = 'Повторная активация - 11классники.ru';
$mainContent = $_SERVER['DOCUMENT_ROOT'] . '/pages/registration/resend_activation/resend_activation_content.php';
$metaD = 'Повторная отправка письма активации аккаунта';
$metaK = 'повторная активация, письмо активации';

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
