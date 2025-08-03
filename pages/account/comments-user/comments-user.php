<?php
// Page configuration
$pageConfig = [
    'title' => 'Мои комментарии',
    'showBackButton' => true
];

// Specify content file
$pageContent = 'comments-user-fields.php';

// Include the account template
include $_SERVER['DOCUMENT_ROOT'] . '/includes/account-template.php';