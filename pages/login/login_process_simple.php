<?php
session_start();

// Simple CSRF validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    header('Location: /login');
    exit();
}

// Get form data
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    $_SESSION['error'] = 'Пожалуйста, введите email и пароль.';
    header('Location: /login');
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    $_SESSION['error'] = 'Ошибка конфигурации базы данных.';
    header('Location: /login');
    exit();
}

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    $_SESSION['error'] = 'Ошибка подключения к базе данных.';
    header('Location: /login');
    exit();
}

$connection->set_charset("utf8mb4");

// Check user credentials - only select columns that exist
$stmt = $connection->prepare("SELECT id, password, email, role, occupation, is_active FROM users WHERE email = ?");
if (!$stmt) {
    $_SESSION['error'] = 'Ошибка базы данных: ' . $connection->error;
    header('Location: /login');
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Неверный email или пароль.';
    header('Location: /login');
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = 'Неверный email или пароль.';
    header('Location: /login');
    exit();
}

// Check if account is active
if (isset($user['is_active']) && $user['is_active'] == 0) {
    $_SESSION['error'] = 'Ваш аккаунт не активирован.';
    header('Location: /login');
    exit();
}

// Account is valid, proceed with login

// Get username from email (part before @)
$username = explode('@', $email)[0];

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $email;
$_SESSION['username'] = $username;
$_SESSION['role'] = $user['role'];
$_SESSION['occupation'] = $user['occupation'];
$_SESSION['logged_in'] = true; // Important: SessionManager checks for this

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