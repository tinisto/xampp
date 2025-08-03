<?php
// Start session to check if user is already logged in
session_start();

// If user is already logged in, redirect to account page
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: /account');
    exit();
}

// Configuration for the form
$formConfig = [
    'title' => 'Вход',
    'action' => '/pages/login/login_process_simple.php',
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