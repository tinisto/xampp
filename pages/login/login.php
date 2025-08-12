<?php
// Configuration for the form
$formConfig = [
    'title' => 'Вход',
    'action' => '/pages/login/login_process.php',
    'submitText' => 'Войти',
    'bottomLink' => [
        'text' => 'Нет аккаунта?',
        'url' => '/registration',
        'linkText' => 'Зарегистрироваться'
    ]
];

// Specify which fields file to include
$formFields = 'login-fields.php';

// Use the fixed form template with CSS variables support
include $_SERVER['DOCUMENT_ROOT'] . '/includes/form-template-fixed.php';