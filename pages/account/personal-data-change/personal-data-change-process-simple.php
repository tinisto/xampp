<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    header('Location: /account/personal-data-change');
    exit();
}

// Get form data
$occupation = $_POST['occupation'] ?? '';
$userId = $_SESSION['user_id'];

// Validate occupation
if (empty($occupation)) {
    $_SESSION['error'] = 'Выберите род деятельности.';
    header('Location: /account/personal-data-change');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    $_SESSION['error'] = 'Ошибка подключения к базе данных.';
    header('Location: /account/personal-data-change');
    exit();
}

$connection->set_charset("utf8mb4");

// Update occupation
$stmt = $connection->prepare("UPDATE users SET occupation = ? WHERE id = ?");
$stmt->bind_param("si", $occupation, $userId);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Данные успешно обновлены.';
    $_SESSION['occupation'] = $occupation; // Update session
} else {
    $_SESSION['error'] = 'Ошибка при обновлении данных.';
}

$stmt->close();
$connection->close();

header('Location: /account/personal-data-change');
exit();