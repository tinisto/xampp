<?php
session_start();

// Enable error logging
error_reporting(E_ALL);
ini_set('log_errors', 1);

// Log to a temporary debug file
$debugLog = $_SERVER['DOCUMENT_ROOT'] . '/debug_login.txt';
file_put_contents($debugLog, "=== LOGIN DEBUG " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

// Simple CSRF validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    file_put_contents($debugLog, "CSRF validation failed\n", FILE_APPEND);
    $_SESSION['error'] = 'Ошибка безопасности. Попробуйте снова.';
    $redirectParam = ($_POST['redirect'] ?? null) ? '?redirect=' . urlencode($_POST['redirect']) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Get form data
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? true : false;
$redirect = $_POST['redirect'] ?? null;

file_put_contents($debugLog, "Email: $email\n", FILE_APPEND);
file_put_contents($debugLog, "Redirect: '$redirect'\n", FILE_APPEND);

if (!$email || empty($password)) {
    file_put_contents($debugLog, "Missing email or password\n", FILE_APPEND);
    $_SESSION['error'] = 'Пожалуйста, введите email и пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
    file_put_contents($debugLog, "Database config missing\n", FILE_APPEND);
    $_SESSION['error'] = 'Ошибка конфигурации базы данных.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connection->connect_error) {
    file_put_contents($debugLog, "Database connection failed\n", FILE_APPEND);
    $_SESSION['error'] = 'Ошибка подключения к базе данных.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

$connection->set_charset("utf8mb4");

// Check user credentials
$stmt = $connection->prepare("SELECT id, password, email, role, occupation, is_active FROM users WHERE email = ?");
if (!$stmt) {
    file_put_contents($debugLog, "SQL prepare failed\n", FILE_APPEND);
    $_SESSION['error'] = 'Ошибка базы данных: ' . $connection->error;
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    file_put_contents($debugLog, "User not found\n", FILE_APPEND);
    $_SESSION['error'] = 'Неверный email или пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

$user = $result->fetch_assoc();
file_put_contents($debugLog, "User found: " . $user['id'] . "\n", FILE_APPEND);

// Verify password
if (!password_verify($password, $user['password'])) {
    file_put_contents($debugLog, "Password verification failed\n", FILE_APPEND);
    $_SESSION['error'] = 'Неверный email или пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Check if account is active
if (isset($user['is_active']) && $user['is_active'] == 0) {
    file_put_contents($debugLog, "Account not active\n", FILE_APPEND);
    $_SESSION['error'] = 'Ваш аккаунт не активирован.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

file_put_contents($debugLog, "Login validation passed\n", FILE_APPEND);

// Get username from email (part before @)
$username = explode('@', $email)[0];

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $email;
$_SESSION['username'] = $username;
$_SESSION['role'] = $user['role'];
$_SESSION['occupation'] = $user['occupation'];
$_SESSION['logged_in'] = true;

file_put_contents($debugLog, "Session variables set\n", FILE_APPEND);

// Handle "Remember Me"
if ($remember) {
    $cookieValue = $user['id'] . ':' . hash('sha256', $email . $user['password']);
    setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', true, true);
    file_put_contents($debugLog, "Remember cookie set\n", FILE_APPEND);
}

// Close connection
$stmt->close();
$connection->close();

// REDIRECT LOGIC with extensive logging
file_put_contents($debugLog, "=== REDIRECT LOGIC ===\n", FILE_APPEND);
file_put_contents($debugLog, "Redirect parameter: '$redirect'\n", FILE_APPEND);

if ($redirect) {
    file_put_contents($debugLog, "Redirect is truthy\n", FILE_APPEND);
    
    // Security check: only allow internal relative paths
    $check1 = strpos($redirect, '/') === 0;
    $check2 = strpos($redirect, '//') !== 0;
    
    file_put_contents($debugLog, "Check 1 (starts with /): " . ($check1 ? 'PASS' : 'FAIL') . "\n", FILE_APPEND);
    file_put_contents($debugLog, "Check 2 (not //): " . ($check2 ? 'PASS' : 'FAIL') . "\n", FILE_APPEND);
    
    if ($check1 && $check2) {
        file_put_contents($debugLog, "Security check PASSED - redirecting to: '$redirect'\n", FILE_APPEND);
        header('Location: ' . $redirect);
        exit();
    } else {
        file_put_contents($debugLog, "Security check FAILED\n", FILE_APPEND);
    }
} else {
    file_put_contents($debugLog, "No redirect parameter\n", FILE_APPEND);
}

// Default redirect based on role
file_put_contents($debugLog, "Using default redirect\n", FILE_APPEND);
if (isset($user['role']) && $user['role'] === 'admin') {
    file_put_contents($debugLog, "Redirecting admin to dashboard\n", FILE_APPEND);
    header('Location: /dashboard');
} else {
    file_put_contents($debugLog, "Redirecting user to account\n", FILE_APPEND);
    header('Location: /account');
}
exit();
?>