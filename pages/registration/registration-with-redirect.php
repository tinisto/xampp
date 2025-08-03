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
    'title' => 'Регистрация',
    'action' => '/pages/registration/registration_process_email.php',
    'submitText' => 'Зарегистрироваться',
    'enctype' => 'multipart/form-data',
    'bottomLink' => [
        'text' => 'Уже есть аккаунт?',
        'url' => '/login',
        'linkText' => 'Войдите здесь'
    ]
];

// Specify which fields file to include
$formFields = 'registration-fields.php';

// Template configuration for auth page
$mainContent = 'includes/form-template-fixed.php';
$pageTitle = 'Регистрация - 11-классники';
$templateConfig = [
    'layoutType' => 'auth',
    'cssFramework' => 'bootstrap',
    'headerType' => 'modern',
    'footerType' => 'modern',
    'darkMode' => true,
    'fullHeight' => true,
    'formConfig' => $formConfig,
    'formFields' => $formFields
];

// Include ultimate template
include $_SERVER['DOCUMENT_ROOT'] . '/common-components/template-engine-ultimate.php';
renderTemplate($pageTitle, $mainContent, $templateConfig);