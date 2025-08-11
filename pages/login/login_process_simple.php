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
$remember = isset($_POST['remember']) ? true : false;
$redirect = $_POST['redirect'] ?? null;

if (!$email || empty($password)) {
    $_SESSION['error'] = 'Пожалуйста, введите email и пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate-limiter.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/security-logger.php';

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

// Check rate limit
$rateLimitCheck = checkRateLimit($connection, $email);
if ($rateLimitCheck['limited']) {
    // Log blocked login attempt
    logSecurityEvent($connection, SecurityLogger::EVENT_LOGIN_BLOCKED, [
        'email' => $email,
        'details' => ['remaining_minutes' => $rateLimitCheck['remaining_minutes']]
    ]);
    $_SESSION['error'] = 'Слишком много неудачных попыток входа. Попробуйте через ' . $rateLimitCheck['remaining_minutes'] . ' минут.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

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
    // Record failed attempt and log
    recordFailedLogin($connection, $email);
    logSecurityEvent($connection, SecurityLogger::EVENT_LOGIN_FAILED, [
        'email' => $email,
        'details' => ['reason' => 'user_not_found']
    ]);
    $_SESSION['error'] = 'Неверный email или пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    // Record failed attempt and log
    recordFailedLogin($connection, $email);
    logSecurityEvent($connection, SecurityLogger::EVENT_LOGIN_FAILED, [
        'email' => $email,
        'user_id' => $user['id'],
        'details' => ['reason' => 'invalid_password']
    ]);
    $_SESSION['error'] = 'Неверный email или пароль.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Check if account is active
if (isset($user['is_active']) && $user['is_active'] == 0) {
    $_SESSION['error'] = 'Ваш аккаунт не активирован.';
    $redirectParam = $redirect ? '?redirect=' . urlencode($redirect) : '';
    header('Location: /login' . $redirectParam);
    exit();
}

// Account is valid, proceed with login

// Clear login attempts after successful login
clearLoginAttempts($connection, $email);

// Log successful login
logSecurityEvent($connection, SecurityLogger::EVENT_LOGIN_SUCCESS, [
    'email' => $email,
    'user_id' => $user['id']
]);

// Get username from email (part before @)
$username = explode('@', $email)[0];

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $email;
$_SESSION['username'] = $username;
$_SESSION['role'] = $user['role'];
$_SESSION['occupation'] = $user['occupation'];
$_SESSION['logged_in'] = true; // Important: SessionManager checks for this

// Handle "Remember Me"
if ($remember) {
    // Set a cookie that expires in 30 days
    $cookieValue = $user['id'] . ':' . hash('sha256', $email . $user['password']);
    setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', true, true);
}

// Close connection
$stmt->close();
$connection->close();

// Redirect based on redirect parameter or role
if ($redirect) {
    // Security check: only allow internal relative paths
    if (strpos($redirect, '/') === 0 && strpos($redirect, '//') !== 0) {
        // It's a relative path starting with / and not //
        header('Location: ' . $redirect);
        exit();
    }
}

// Default redirect to home page for all users
header('Location: /');
exit();