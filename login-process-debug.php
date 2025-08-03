<?php
session_start();

// Simple CSRF validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    header('Location: /login-simple.php');
    exit();
}

// Get form data
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    $_SESSION['error'] = 'Пожалуйста, введите email и пароль.';
    header('Location: /login-simple.php');
    exit();
}

// Database connection - using direct connection like in debug page
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Check user credentials
$stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
if (!$stmt) {
    $_SESSION['error'] = 'Ошибка базы данных: ' . $connection->error;
    header('Location: /login-simple.php');
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Неверный email или пароль.';
    $stmt->close();
    $connection->close();
    header('Location: /login-simple.php');
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Неверный email или пароль.';
    $stmt->close();
    $connection->close();
    header('Location: /login-simple.php');
    exit();
}

// Check if account is active
if (isset($user['is_active']) && $user['is_active'] == 0) {
    $_SESSION['error'] = 'Ваш аккаунт не активирован.';
    $stmt->close();
    $connection->close();
    header('Location: /login-simple.php');
    exit();
}

// Account is valid, proceed with login

// Get username from email (part before @)
$username = explode('@', $email)[0];

// Set session variables - matching the debug page
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['username'] = $username;
$_SESSION['role'] = $user['role'] ?? 'user';
$_SESSION['occupation'] = $user['occupation'] ?? '';
$_SESSION['logged_in'] = true;

// Also set these for compatibility
$_SESSION['user_logged_in'] = true;
$_SESSION['user'] = [
    'id' => $user['id'],
    'email' => $user['email'],
    'username' => $username,
    'role' => $user['role'] ?? 'user'
];

// Close connection
$stmt->close();
$connection->close();

// Redirect based on role
if (isset($user['role']) && $user['role'] === 'admin') {
    header('Location: /dashboard');
} else {
    header('Location: /account');
}
exit();