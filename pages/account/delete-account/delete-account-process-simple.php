<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Check if user is admin - admins cannot delete their account
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $_SESSION['error'] = 'Администраторы не могут удалить свой аккаунт.';
    header('Location: /account/delete-account');
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    header('Location: /account/delete-account');
    exit();
}

// Validate confirmation
if (!isset($_POST['confirm_delete']) || !isset($_POST['password'])) {
    $_SESSION['error'] = 'Пожалуйста, подтвердите удаление аккаунта.';
    header('Location: /account/delete-account');
    exit();
}

$password = $_POST['password'];
$userId = $_SESSION['user_id'];

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

// Verify password
$stmt = $connection->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Неверный пароль.';
    header('Location: /account/delete-account');
    exit();
}

// Delete user account
$stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    // Destroy session
    session_destroy();
    
    // Redirect to success page
    header('Location: /pages/account/delete-account/success-delete.php');
} else {
    $_SESSION['error'] = 'Ошибка при удалении аккаунта.';
    header('Location: /account/delete-account');
}

$stmt->close();
$connection->close();
exit();