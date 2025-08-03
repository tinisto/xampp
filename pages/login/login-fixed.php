<?php
// Start session FIRST before any output
session_start();

// DEBUG: Add temporary debugging
error_log("Login page accessed. Session data: " . print_r($_SESSION, true));

// If user is already logged in, redirect to account page
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    error_log("User is logged in (ID: " . $_SESSION['user_id'] . "), redirecting to account");
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