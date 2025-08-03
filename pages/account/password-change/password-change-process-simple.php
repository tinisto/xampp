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
    header('Location: /account/password-change');
    exit();
}

// Get form data
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$userId = $_SESSION['user_id'];

// Validate inputs
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['error'] = 'Заполните все поля.';
    header('Location: /account/password-change');
    exit();
}

if (strlen($newPassword) < 8) {
    $_SESSION['error'] = 'Новый пароль должен содержать минимум 8 символов.';
    header('Location: /account/password-change');
    exit();
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = 'Пароли не совпадают.';
    header('Location: /account/password-change');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Verify current password
$stmt = $connection->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($currentPassword, $user['password'])) {
    $_SESSION['error'] = 'Неверный текущий пароль.';
    header('Location: /account/password-change');
    exit();
}

// Update password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashedPassword, $userId);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Пароль успешно изменен.';
} else {
    $_SESSION['error'] = 'Ошибка при изменении пароля.';
}

$stmt->close();
$connection->close();

header('Location: /account/password-change');
exit();